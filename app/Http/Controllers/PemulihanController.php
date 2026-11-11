<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetOtp;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class PemulihanController extends Controller
{
    private const OTP_MENIT = 5;

    private const OTP_MAKS_PERCOBAAN = 3;

    private const OTP_JEDA_KIRIM_DETIK = 60;

    public function show(Request $request)
    {
        if ($request->boolean('mulai')) {
            session()->forget('pemulihan');
        }

        $step = (int) session('pemulihan.step', 1);
        $data = [
            'step' => $step,
            'email' => session('pemulihan.email'),
        ];

        $view = view('pemulihan', $data);

        if ($step === 4) {
            session()->forget('pemulihan');
        }

        return $view;
    }

    public function kirimOtp(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:150']], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        if (! $this->emailDeliveryAvailable()) {
            return back()->withErrors([
                'email' => 'Layanan email belum dikonfigurasi. Hubungi administrator.',
            ]);
        }

        $email = Str::lower(trim($data['email']));
        $user = User::query()->where('email', $email)->where('status', true)->first();

        if ($user) {
            $this->sendOtp($user);
        }

        session(['pemulihan' => ['step' => 2, 'email' => $email]]);

        return redirect()->route('password.request')
            ->with('status', 'Jika email terdaftar, kode OTP telah dikirim. Periksa kotak masuk atau folder spam.');
    }

    public function kirimUlang(): RedirectResponse
    {
        $email = session('pemulihan.email');
        if (! $email) {
            return redirect()->route('password.request');
        }

        if (! $this->emailDeliveryAvailable()) {
            return redirect()->route('password.request')
                ->withErrors(['otp' => 'Layanan email belum dikonfigurasi. Hubungi administrator.']);
        }

        $user = User::query()->where('email', $email)->where('status', true)->first();
        if ($user) {
            $this->sendOtp($user);
        }

        return redirect()->route('password.request')
            ->with('status', 'Jika email terdaftar, kode baru telah dikirim. Periksa kotak masuk atau folder spam.');
    }

    public function ubahEmail(): RedirectResponse
    {
        session()->forget('pemulihan');

        return redirect()->route('password.request');
    }

    public function verifikasi(Request $request): RedirectResponse
    {
        abort_unless(session('pemulihan.step') === 2, 404);

        $request->validate(['otp' => ['required', 'digits:6']], [
            'otp.required' => 'Masukkan 6 digit kode OTP.',
            'otp.digits' => 'Kode OTP harus 6 digit angka.',
        ]);

        $user = User::query()->where('email', session('pemulihan.email'))
            ->where('status', true)->first();
        $otp = $user
            ? DB::table('otp_codes')
                ->where('id_user', $user->getKey())
                ->whereNull('consumed_at')
                ->latest('id_otp_code')
                ->first()
            : null;

        if (! $otp || Carbon::parse($otp->expires_at)->isPast()) {
            if ($otp) {
                DB::table('otp_codes')->where('id_otp_code', $otp->id_otp_code)
                    ->update(['consumed_at' => now(), 'updated_at' => now()]);
            }

            throw ValidationException::withMessages(['otp' => 'Kode OTP tidak valid atau sudah kedaluwarsa. Kirim ulang OTP.']);
        }

        if ((int) $otp->attempts >= self::OTP_MAKS_PERCOBAAN) {
            throw ValidationException::withMessages(['otp' => 'Batas percobaan tercapai. Kirim ulang OTP.']);
        }

        if (! Hash::check((string) $request->input('otp'), $otp->code_hash)) {
            DB::table('otp_codes')->where('id_otp_code', $otp->id_otp_code)
                ->increment('attempts', 1, ['updated_at' => now()]);
            $attempts = (int) DB::table('otp_codes')
                ->where('id_otp_code', $otp->id_otp_code)->value('attempts');

            if ($attempts >= self::OTP_MAKS_PERCOBAAN) {
                DB::table('otp_codes')->where('id_otp_code', $otp->id_otp_code)
                    ->update(['consumed_at' => now(), 'updated_at' => now()]);
            }

            throw ValidationException::withMessages([
                'otp' => $attempts >= self::OTP_MAKS_PERCOBAAN
                    ? 'Batas percobaan tercapai. Kirim ulang OTP.'
                    : 'Kode OTP tidak valid atau sudah kedaluwarsa.',
            ]);
        }

        DB::table('otp_codes')->where('id_otp_code', $otp->id_otp_code)
            ->update(['verified_at' => now(), 'updated_at' => now()]);

        session([
            'pemulihan.step' => 3,
            'pemulihan.otp_id' => $otp->id_otp_code,
        ]);

        return redirect()->route('password.request');
    }

    public function simpan(Request $request): RedirectResponse
    {
        abort_unless(session('pemulihan.step') === 3, 404);

        $data = $request->validate([
            'password' => ['required', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
        ]);

        $otpId = session('pemulihan.otp_id');
        $email = session('pemulihan.email');

        DB::transaction(function () use ($otpId, $email, $data) {
            $otp = DB::table('otp_codes')->where('id_otp_code', $otpId)
                ->lockForUpdate()->first();

            if (! $otp || $otp->consumed_at !== null || $otp->verified_at === null
                || Carbon::parse($otp->expires_at)->isPast()) {
                throw ValidationException::withMessages([
                    'password' => 'Sesi pemulihan tidak valid atau sudah kedaluwarsa. Mulai kembali.',
                ]);
            }

            $consumed = DB::table('otp_codes')->where('id_otp_code', $otpId)
                ->whereNull('consumed_at')->update(['consumed_at' => now(), 'updated_at' => now()]);

            if ($consumed !== 1) {
                throw ValidationException::withMessages([
                    'password' => 'Kode pemulihan sudah digunakan. Mulai kembali.',
                ]);
            }

            User::query()->where('email', $email)->where('status', true)
                ->update([
                    'password' => Hash::make($data['password']),
                    'remember_token' => Str::random(60),
                    'updated_at' => now(),
                ]);
        });

        session(['pemulihan.step' => 4]);

        return redirect()->route('password.request');
    }

    private function sendOtp(User $user): void
    {
        $otp = DB::table('otp_codes')->where('id_user', $user->getKey())
            ->whereNull('consumed_at')->latest('id_otp_code')->first();

        if ($otp && Carbon::parse($otp->last_sent_at)
            ->addSeconds(self::OTP_JEDA_KIRIM_DETIK)->isFuture()) {
            return;
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $now = now();
        $values = [
            'code_hash' => Hash::make($code),
            'attempts' => 0,
            'expires_at' => $now->copy()->addMinutes(self::OTP_MENIT),
            'last_sent_at' => $now,
            'verified_at' => null,
            'consumed_at' => null,
            'updated_at' => $now,
        ];

        if ($otp) {
            DB::table('otp_codes')->where('id_otp_code', $otp->id_otp_code)->update($values);
            $otpId = (int) $otp->id_otp_code;
        } else {
            $otpId = (int) DB::table('otp_codes')->insertGetId(array_merge($values, [
                'id_user' => $user->getKey(),
                'created_at' => $now,
            ]), 'id_otp_code');
        }

        try {
            Mail::to($user->email)->send(new PasswordResetOtp($code, self::OTP_MENIT));
        } catch (TransportExceptionInterface $exception) {
            DB::table('otp_codes')->where('id_otp_code', $otpId)
                ->update(['consumed_at' => now(), 'updated_at' => now()]);

            throw $exception;
        }
    }

    private function emailDeliveryAvailable(): bool
    {
        if (app()->environment('testing')) {
            return true;
        }

        $mailer = config('mail.default');
        $transport = config("mail.mailers.{$mailer}.transport");

        return ! in_array($transport, ['log', 'array', 'failover', 'roundrobin'], true);
    }
}
