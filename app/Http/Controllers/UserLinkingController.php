<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\TeacherLinkToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // Untuk membuat token acak
use Carbon\Carbon; // Untuk mengelola waktu
use Illuminate\Support\Facades\Log;

class UserLinkingController extends Controller
{
    /**
     * Menghasilkan token link baru untuk seorang guru.
     * Hanya bisa diakses oleh admin atau user yang berwenang.
     *
     * @param string $teacherId ID (ULID) dari guru.
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateTeacherLinkToken(string $teacherId)
    {
        // Pastikan guru ada
        $teacher = Teacher::find($teacherId);
        if (!$teacher) {
            return response()->json(['message' => 'Guru tidak ditemukan.'], 404);
        }

        // Opsional: Periksa apakah guru sudah memiliki user yang tertaut
        if ($teacher->user()->exists()) {
            return response()->json(['message' => 'Guru ini sudah tertaut dengan akun user.'], 409);
        }

        DB::beginTransaction();
        try {
            // Hapus token lama yang belum digunakan untuk guru ini (opsional, tapi bagus untuk kebersihan)
            $teacher->linkTokens()->whereNull('used_at')->delete();

            // Buat token baru
            $token = Str::random(60); // Token acak 60 karakter
            $expiresAt = Carbon::now()->addHours(24); // Token berlaku 24 jam

            $linkToken = TeacherLinkToken::create([
                'teacher_id' => $teacher->id,
                'token' => $token,
                'expires_at' => $expiresAt,
            ]);

            DB::commit();

            // URL yang akan diberikan ke frontend. Frontend akan mengarahkan user ke sini.
            $linkingUrl = url('/link/teacher/' . $token); // Sesuaikan dengan route frontend Anda

            return response()->json([
                'message' => 'Token link berhasil dibuat.',
                'token' => $token,
                'expires_at' => $expiresAt->toDateTimeString(),
                // 'linking_url' => $linkingUrl,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error generating teacher link token: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal membuat token link.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menautkan akun user yang sedang login dengan data guru menggunakan token dari request body.
     *
     * @param \Illuminate\Http\Request $request Objek request yang berisi token.
     * @return \Illuminate\Http\JsonResponse
     */
    public function linkTeacherAccount(Request $request) // <-- UBAH PARAMETER DI SINI
    {
        // Validasi bahwa token ada di request body
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        $token = $request->input('token'); // <-- AMBIL TOKEN DARI REQUEST BODY

        // Pastikan user sedang login
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Anda harus login untuk menautkan akun.'], 401);
        }

        // Pastikan user yang login belum tertaut ke teacher atau student lain
        if (!is_null($user->userable_id) || !is_null($user->userable_type)) {
            return response()->json(['message' => 'Akun Anda sudah tertaut dengan data lain.'], 409);
        }

        DB::beginTransaction();
        try {
            $linkToken = TeacherLinkToken::with('teacher')->where('token', $token)->first();

            // 1. Validasi Token
            if (!$linkToken) {
                return response()->json(['message' => 'Token link tidak valid.'], 404);
            }
            if ($linkToken->isExpired()) {
                return response()->json(['message' => 'Token link sudah kadaluarsa.'], 410); // 410 Gone
            }
            if ($linkToken->isUsed()) {
                return response()->json(['message' => 'Token link sudah digunakan.'], 409);
            }

            // 2. Validasi Guru (pastikan guru ada dan belum tertaut)
            $teacher = $linkToken->teacher;
            if (!$teacher) {
                return response()->json(['message' => 'Data guru tidak ditemukan untuk token ini.'], 404);
            }
            if ($teacher->user()->exists()) {
                // Jika guru sudah tertaut, tandai token ini sebagai used (opsional, tapi bagus)
                $linkToken->update(['used_at' => Carbon::now()]);
                DB::commit(); // Komit perubahan status token
                return response()->json(['message' => 'Guru ini sudah tertaut dengan akun user lain.'], 409);
            }

            // 3. Tautkan User dengan Teacher
            $user->userable_id = $teacher->id;
            $user->userable_type = Teacher::class;
            $user->save();

            // 4. Tandai Token sebagai Digunakan
            $linkToken->update(['used_at' => Carbon::now()]);

            DB::commit();

            return response()->json([
                'message' => 'Akun user berhasil ditautkan dengan guru!',
                'user' => $user->load('userable')
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error linking teacher account: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal menautkan akun user.', 'error' => $e->getMessage()], 500);
        }
    }
}
