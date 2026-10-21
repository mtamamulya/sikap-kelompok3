<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timeline', function (Blueprint $table) {
            $table->id('id_timeline');
            $table->unsignedBigInteger('id_kegiatan');
            $table->unsignedBigInteger('id_kegiatan_dosen')->nullable();
            $table->string('status')->nullable();
            $table->timestamp('tanggal_status')->nullable();
            $table->text('catatan_revisi')->nullable();

            $table->foreign('id_kegiatan')->references('id_kegiatan')->on('kegiatan')->onDelete('cascade');
            $table->foreign('id_kegiatan_dosen')->references('id_kegiatan_dosen')->on('kegiatan_dosen')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timeline');
    }
};