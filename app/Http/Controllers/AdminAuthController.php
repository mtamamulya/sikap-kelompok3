<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    /**
     * Memproses login pengguna dari satu pintu (/login)
     * dan mengarahkan ke dashboard masing-masing sesuai id_role.
     */
    public function login(Request $request): RedirectResponse
    {
        // 1. Validasi Input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Sandi wajib diisi.',
        ]);
        $credentials['status'] = true;

        // 2. Coba Auth via Database
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Sesi kompatibilitas untuk middleware lama
            if ($user->id_role == 1) {
                $request->session()->put('admin', ['email' => $user->email, 'nama' => 'Admin']);
            } elseif ($user->id_role == 2) {
                $request->session()->put('kaprodi', ['email' => $user->email, 'nama' => 'Kaprodi']);
            }

            // 3. Pengalihan Dashboard Berdasarkan id_role
            return match ((int) $user->id_role) {
                1 => redirect()->route('admin.dashboard'),
                2 => redirect()->route('kaprodi.dashboard'),
                3 => redirect()->route('dosen.dashboard'),
                4 => redirect()->route('mahasiswa.dashboard'),
                default => redirect()->route('home'),
            };
        }

        // 4. Jika Gagal
        return back()->withInput($request->only('email'))
            ->withErrors(['email' => 'Email atau sandi yang Anda masukkan salah.']);
    }

    /**
     * Memproses keluar (logout) dari sistem.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->forget(['admin', 'kaprodi']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
