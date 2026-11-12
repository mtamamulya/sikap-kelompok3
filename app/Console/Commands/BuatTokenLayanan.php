<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class BuatTokenLayanan extends Command
{
    protected $signature = 'api:token-layanan
        {email : Email akun yang menjadi pemilik token}
        {nama : Nama token, mis. kelompok1-landing}
        {--hari=30 : Masa berlaku dalam hari}
        {--abilities=* : Kemampuan token (boleh diulang), bawaan: baca}';

    protected $description = 'Buat token layanan (layanan ke layanan) untuk diisi ke API_SERVICE_TOKEN';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('Akun dengan email itu tidak ditemukan.');

            return self::FAILURE;
        }

        $kemampuan = $this->option('abilities') ?: ['baca'];
        $hari = max(1, (int) $this->option('hari'));

        $token = $user->createToken((string) $this->argument('nama'), $kemampuan, now()->addDays($hari));

        $this->warn('Salin token ini sekarang. Ia tidak akan ditampilkan lagi:');
        $this->line($token->plainTextToken);
        $this->newLine();
        $this->info('Simpan di .env (jangan di-commit):  API_SERVICE_TOKEN=<token di atas>');
        $this->comment('Berlaku '.$hari.' hari, kemampuan: '.implode(', ', $kemampuan).'.');

        return self::SUCCESS;
    }
}
