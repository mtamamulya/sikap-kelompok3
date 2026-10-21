<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berita', function (Blueprint $table) {
            $table->id('id_berita');
            $table->unsignedBigInteger('id_kegiatan');
            $table->string('judul_berita')->nullable();
            $table->text('isi_berita')->nullable();
            $table->enum('status', ['publish', 'draft'])->default('draft');
            $table->dateTime('tanggal_publish')->nullable();
            $table->timestamps();

            $table->foreign('id_kegiatan')->references('id_kegiatan')->on('kegiatan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berita');
    }
};
