<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

/** Kegagalan saat web memanggil API; status HTTP dari API ikut dibawa. */
class ApiException extends HttpException
{
    /** @param  array<string, mixed>  $errors */
    public function __construct(int $status, string $message, public readonly array $errors = [], ?Throwable $previous = null)
    {
        parent::__construct($status, $message, $previous);
    }

    /**
     * 422 kembali ke form dengan pesan galat, 401 kembali ke login.
     * Status lain diserahkan ke halaman galat bawaan Laravel (return false).
     */
    public function render(Request $request): JsonResponse|RedirectResponse|false
    {
        $status = $this->getStatusCode();

        if ($status === 422) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $this->getMessage(), 'errors' => $this->errors], 422);
            }

            return back()->withInput()->withErrors($this->errors ?: ['api' => $this->getMessage()]);
        }

        if ($status === 401 && ! $request->expectsJson()) {
            $request->session()->forget('api_token');

            return redirect()->guest(route('login'))
                ->withErrors(['email' => 'Sesi Anda berakhir. Silakan masuk lagi.']);
        }

        return false;
    }
}
