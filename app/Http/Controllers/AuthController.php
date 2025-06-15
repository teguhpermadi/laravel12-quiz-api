<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function register(Request $request): JsonResponse
    {
        // Validasi input dari request
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'device_name' => ['required', 'string', 'max:255'], // Diperlukan untuk Sanctum
        ]);

        // Buat user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Buat token API untuk user yang baru terdaftar
        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
        ], 201); // Status 201 Created
    }

    /**
     * Authenticate the user and generate an API token.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        // Validasi input untuk login
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'device_name' => ['required', 'string', 'max:255'], // Diperlukan untuk Sanctum
        ]);

        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // Verifikasi kredensial (email dan password)
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Hapus token lama jika ada (opsional, tergantung kebutuhan)
        // Ini akan memastikan setiap login baru mendapatkan token baru
        // dan token lama untuk device_name yang sama dihapus.
        $user->tokens()->where('name', $request->device_name)->delete();


        // Buat token API baru untuk user
        $token = $user->createToken($request->device_name)->plainTextToken;

        // Eager load roles jika Anda ingin menggunakannya di UserResource dengan whenLoaded
        $user->load('roles', 'permissions'); // Opsional, jika Anda ingin 'roles' di Resource

        return response()->json([
            'message' => 'Login successful',
            'user' => new AuthResource($user), // Menggunakan AuthResource untuk format data user
            'token' => $token,
        ], 200); // Status 200 OK
    }

    /**
     * Log the user out (revoke their API token).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        // Hapus token yang saat ini digunakan oleh user
        // Ini adalah token yang disertakan dalam header Authorization request saat ini
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ], 200);
    }

    /**
     * Get the authenticated user's details.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        // Mengembalikan data user yang sedang login
        // User ini otomatis tersedia karena middleware 'auth:sanctum'
        return response()->json($request->user());
    }
}