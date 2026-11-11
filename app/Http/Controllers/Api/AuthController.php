<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

/** Login API berbasis token (Sanctum). Semua respons memakai envelope {success, message, data}. */
class AuthController extends Controller
{
    private const MASA_BERLAKU_HARI = 7;

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::with('role')->where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return ApiResponse::gagal('Email atau sandi salah.', 401);
        }

        if (! $user->status) {
            return ApiResponse::gagal('Akun tidak aktif. Hubungi admin.', 403);
        }

        $berlakuSampai = now()->addDays(self::MASA_BERLAKU_HARI);
        $token = $user->createToken($data['device_name'] ?? 'api', ['*'], $berlakuSampai);

        return ApiResponse::ok([
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_at' => $berlakuSampai->toIso8601String(),
            'user' => self::bentuk($user),
        ], 'Login berhasil.');
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }

        return ApiResponse::ok(null, 'Logout berhasil.');
    }

    public function me(Request $request): JsonResponse
    {
        return ApiResponse::ok(self::bentuk($request->user()->loadMissing('role')), 'Data profil user berhasil diambil.');
    }

    /** Hanya kolom aman; sandi dan tanda tangan tidak ikut. */
    public static function bentuk(User $u): array
    {
        return [
            'id_user' => $u->id_user,
            'email' => $u->email,
            'id_role' => $u->id_role,
            'role' => $u->role?->nama_role,
            'status' => (bool) $u->status,
            'foto_profil' => $u->foto_profil,
        ];
    }
}
