<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota_kegiatan', function (Blueprint $table) {
            $table->id('id_anggota_kegiatan');
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_kegiatan');
            $table->unsignedBigInteger('id_kegiatan_dosen')->nullable();
            $table->string('peran')->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('id_kegiatan')->references('id_kegiatan')->on('kegiatan')->onDelete('cascade');
            $table->foreign('id_kegiatan_dosen')->references('id_kegiatan_dosen')->on('kegiatan_dosen')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota_kegiatan');
    }
};
