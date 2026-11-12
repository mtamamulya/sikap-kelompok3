<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;
use Throwable;

/**
 * Menerbitkan token API atas nama pengguna saat login web dan mencabutnya saat logout.
 * Kegagalan di sini tidak boleh menggagalkan login web (mis. tabel token belum dimigrasi).
 */
class ApiTokenSesi
{
    public static function terbitkan(User $user): void
    {
        if (! config('api_client.token_sesi')) {
            return;
        }

        try {
            $berlakuSampai = now()->addMinutes((int) config('session.lifetime', 120));
            $token = $user->createToken((string) config('api_client.token_sesi_nama', 'web'), ['*'], $berlakuSampai);

            session()->put('api_token', $token->plainTextToken);
        } catch (Throwable $e) {
            Log::warning('Token API sesi gagal dibuat: '.$e->getMessage());
        }
    }

    public static function cabut(): void
    {
        $teks = session()->pull('api_token');

        if (! is_string($teks) || $teks === '') {
            return;
        }

        try {
            PersonalAccessToken::findToken($teks)?->delete();
        } catch (Throwable $e) {
            Log::warning('Token API sesi gagal dicabut: '.$e->getMessage());
        }
    }
}
