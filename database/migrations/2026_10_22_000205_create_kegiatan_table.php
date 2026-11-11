<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kegiatan', function (Blueprint $table) {
            $table->id('id_kegiatan');
            $table->foreignId('id_kategori')
                ->constrained('kategori', 'id_kategori')
                ->restrictOnDelete();
            $table->foreignId('id_user')
                ->constrained('users', 'id_user')
                ->restrictOnDelete();
            $table->foreignId('id_skim')
                ->nullable()
                ->constrained('skim', 'id_skim')
                ->restrictOnDelete();
            $table->foreignId('id_kelompok_bidang')
                ->nullable()
                ->constrained('kelompok_bidang', 'id_kelompok_bidang')
                ->restrictOnDelete();

            $table->string('nama_kegiatan', 255);
            $table->string('nomor_sk', 100)->nullable();
            $table->date('tanggal_sk')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kegiatan');
    }
};