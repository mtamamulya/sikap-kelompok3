<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_kegiatan', function (Blueprint $table) {
            $table->id('id_detail');
            $table->unsignedBigInteger('id_kegiatan');
            $table->string('nama_kegiatan')->nullable();
            $table->text('pembimbing')->nullable(); // Disesuaikan dengan ERD terbaru (pembimbing)
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('lokasi')->nullable();
            $table->text('deskripsi')->nullable();
            $table->enum('tingkat_kejuaraan', ['kabupaten/kota', 'provinsi', 'nasional', 'internasional'])->nullable();
            $table->string('penyelenggara')->nullable();
            $table->string('nomor_surat')->nullable();
            $table->string('link_website')->nullable();
            $table->string('posisi_dalam_kegiatan')->nullable();
            $table->timestamps();

            $table->foreign('id_kegiatan')->references('id_kegiatan')->on('kegiatan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_kegiatan');
    }
};
