<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Menjaga semua halaman admin untuk akun ber-role admin yang aktif login. */
class AdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->guest(route('login'));
        }

        abort_unless((int) $request->user()->id_role === 1, 403);

        return $next($request);
    }
}
