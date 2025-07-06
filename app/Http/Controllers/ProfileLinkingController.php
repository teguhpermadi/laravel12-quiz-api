<?php

namespace App\Http\Controllers;

use App\Events\ProfileLinkedEvent;
use App\Models\Teacher; // Impor model Teacher
use App\Models\Student; // Impor model Student
use App\Models\ProfileLinkToken; // Impor model ProfileLinkToken
use App\Models\User; // Impor model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException; // Impor ValidationException

class ProfileLinkingController extends Controller
{
    /**
     * Menghasilkan token link baru untuk profil (Teacher atau Student).
     * Hanya bisa diakses oleh admin atau user yang berwenang.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $type Tipe profil ('teacher' atau 'student').
     * @param string $id ID (ULID) dari profil.
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateLinkToken(Request $request, string $type, string $id)
    {
        // Validasi tipe profil
        if (!in_array($type, ['teacher', 'student'])) {
            return response()->json(['message' => 'Tipe profil tidak valid. Hanya "teacher" atau "student" yang diizinkan.'], 400);
        }

        // Tentukan model berdasarkan tipe
        $modelClass = ($type === 'teacher') ? Teacher::class : Student::class;
        $profile = $modelClass::find($id);

        if (!$profile) {
            return response()->json(['message' => ucfirst($type) . ' tidak ditemukan.'], 404);
        }

        // Periksa apakah profil sudah memiliki user yang tertaut
        if ($profile->user()->exists()) {
            return response()->json(['message' => ucfirst($type) . ' ini sudah tertaut dengan akun user.'], 409);
        }

        DB::beginTransaction();
        try {
            // Hapus token lama yang belum digunakan untuk profil ini (opsional, tapi bagus untuk kebersihan)
            $profile->profileLinkTokens()->whereNull('used_at')->delete();

            // Buat token baru
            $token = Str::random(60); // Token acak 60 karakter
            $expiresAt = Carbon::now()->addHours(24); // Token berlaku 24 jam

            $linkToken = ProfileLinkToken::create([
                'linkable_id' => $profile->id,
                'linkable_type' => $modelClass,
                'token' => $token,
                'expires_at' => $expiresAt,
            ]);

            DB::commit();

            // URL yang akan diberikan ke frontend. Frontend akan mengarahkan user ke sini.
            // Asumsi frontend memiliki route seperti /profile?token=TOKEN
            $linkingUrl = url(env('FRONTEND_URL') . '/profile?token=' . $token); // Sesuaikan dengan URL frontend Anda

            return response()->json([
                'message' => 'Token link berhasil dibuat.',
                'token' => $token,
                'expires_at' => $expiresAt->toDateTimeString(),
                'linking_url' => $linkingUrl,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error generating profile link token: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal membuat token link.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menautkan akun user yang sedang login dengan data profil (Teacher atau Student) menggunakan token.
     * Token diambil dari request body.
     *
     * @param \Illuminate\Http\Request $request Objek request yang berisi token.
     * @return \Illuminate\Http\JsonResponse
     */
    public function linkProfileAccount(Request $request)
    {
        // Validasi bahwa token ada di request body
        try {
            $request->validate([
                'token' => ['required', 'string'],
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Token tidak boleh kosong.'], 422);
        }

        $tokenValue = $request->input('token');

        // Pastikan user sedang login
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Anda harus login untuk menautkan akun.'], 401);
        }

        // Pastikan user yang login belum tertaut ke profil lain
        if (!is_null($user->userable_id) || !is_null($user->userable_type)) {
            return response()->json(['message' => 'Akun Anda sudah tertaut dengan data lain.'], 409);
        }

        DB::beginTransaction();
        try {
            $linkToken = ProfileLinkToken::where('token', $tokenValue)->first();

            // 1. Validasi Token
            if (!$linkToken) {
                return response()->json(['message' => 'Token link tidak valid.'], 404);
            }
            if ($linkToken->isExpired()) {
                // Tandai token sebagai digunakan jika sudah kadaluarsa (opsional, tapi bagus)
                $linkToken->update(['used_at' => Carbon::now()]);
                DB::commit(); // Komit perubahan status token
                return response()->json(['message' => 'Token link sudah kadaluarsa.'], 410); // 410 Gone
            }
            if ($linkToken->isUsed()) {
                return response()->json(['message' => 'Token link sudah digunakan.'], 409);
            }

            // 2. Dapatkan profil (Teacher atau Student) yang ditautkan oleh token
            $profile = $linkToken->linkable; // Menggunakan relasi polymorphic 'linkable'

            if (!$profile) {
                return response()->json(['message' => 'Data profil tidak ditemukan untuk token ini.'], 404);
            }

            // 3. Validasi Profil (pastikan profil belum tertaut ke user lain)
            if ($profile->user()->exists()) {
                // Jika profil sudah tertaut, tandai token ini sebagai used
                $linkToken->update(['used_at' => Carbon::now()]);
                DB::commit(); // Komit perubahan status token
                return response()->json(['message' => ucfirst($profile->getTable()) . ' ini sudah tertaut dengan akun user lain.'], 409);
            }

            // 4. Tautkan User dengan Profil
            $user->userable_id = $profile->id;
            $user->userable_type = $profile->getMorphClass(); // Dapatkan nama kelas polymorphic dari model
            $user->save();

            // 5. Tandai Token sebagai Digunakan
            $linkToken->update(['used_at' => Carbon::now()]);

            DB::commit();

            // NEW: Panggil event ProfileLinked setelah berhasil menautkan
            event(new ProfileLinkedEvent($profile->id, $profile->getMorphClass()));

            return response()->json([
                'message' => 'Akun user berhasil ditautkan dengan profil!',
                'user' => $user->load('userable') // Muat relasi userable untuk respons
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error linking profile account: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal menautkan akun user.', 'error' => $e->getMessage()], 500);
        }
    }
}
