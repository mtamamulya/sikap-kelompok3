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
            $table->foreignId('id_kegiatan')
                ->nullable()
                ->constrained('kegiatan', 'id_kegiatan')
                ->nullOnDelete();
            $table->foreignId('id_user')
                ->constrained('users', 'id_user')
                ->restrictOnDelete();

            $table->string('judul_berita', 255);
            $table->longText('isi_berita');
            $table->enum('status', ['publish', 'draft'])->default('draft');
            $table->dateTime('tanggal_publish')->nullable();
            $table->string('sampul', 255)->nullable();
            $table->text('catatan_revisi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berita');
    }
};