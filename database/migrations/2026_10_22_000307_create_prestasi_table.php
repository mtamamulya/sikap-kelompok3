<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestasi', function (Blueprint $table) {
            $table->id('id_prestasi');
            $table->foreignId('id_user')
                ->constrained('users', 'id_user')
                ->restrictOnDelete();
            $table->foreignId('created_by')
                ->constrained('users', 'id_user')
                ->restrictOnDelete();
            $table->foreignId('id_kegiatan')
                ->nullable()
                ->constrained('kegiatan', 'id_kegiatan')
                ->restrictOnDelete();

            $table->string('capaian', 100);
            $table->enum('jenis_prestasi', ['akademik', 'non-akademik']);
            $table->string('bidang_lomba', 150)->nullable();
            $table->string('nama_lomba', 255);
            $table->string('penyelenggara', 150);
            $table->string('lokasi', 150);
            $table->string('dosen_pembimbing', 100)->nullable();
            $table->enum('tingkat_kejuaraan', ['kabupaten/kota', 'provinsi', 'nasional', 'internasional']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('bentuk_partisipasi', ['individu', 'tim']);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestasi');
    }
};