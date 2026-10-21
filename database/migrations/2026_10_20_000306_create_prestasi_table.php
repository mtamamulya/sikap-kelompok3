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
            $table->unsignedBigInteger('id_kegiatan');
            $table->string('nama_kejuaraan')->nullable();
            $table->string('nama_lomba')->nullable();
            $table->string('penyelenggara')->nullable();
            $table->enum('tingkat_kejuaraan', ['kabupaten/kota', 'provinsi', 'nasional', 'internasional'])->nullable();
            $table->string('juara_dikelasnya')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('sertifikat')->nullable();
            $table->text('deskripsi')->nullable();

            $table->foreign('id_kegiatan')->references('id_kegiatan')->on('kegiatan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestasi');
    }
};