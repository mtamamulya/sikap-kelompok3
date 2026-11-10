<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Menjaga semua halaman kaprodi untuk akun ber-role kaprodi yang aktif login. */
class KaprodiOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->guest(route('login'));
        }

        abort_unless((int) $request->user()->id_role === 2, 403);

        return $next($request);
    }
}
