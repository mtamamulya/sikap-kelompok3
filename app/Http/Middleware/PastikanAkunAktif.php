<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mengeluarkan akun yang dinonaktifkan (users.status = 0) walau sesi loginnya masih berlaku.
 * Dipasang di grup "web" (bootstrap/app.php), jadi berlaku untuk semua peran tanpa mengubah
 * AdminOnly / KaprodiOnly. Pengunjung yang belum login tidak terpengaruh.
 */
class PastikanAkunAktif
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && ! $user->status) {
            Auth::logout();

            $request->session()->forget(['admin', 'kaprodi']);
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akun Anda dinonaktifkan. Hubungi admin.'], 403);
            }

            return redirect()->route('login')
                ->withErrors(['email' => 'Akun Anda dinonaktifkan. Hubungi admin.']);
        }

        return $next($request);
    }
}
