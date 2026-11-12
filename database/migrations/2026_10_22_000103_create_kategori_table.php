<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori', function (Blueprint $table) {
            $table->id('id_kategori');
            $table->foreignId('id_jenis_kegiatan')
                ->constrained('jenis_kegiatan', 'id_jenis_kegiatan')
                ->restrictOnDelete();

            $table->string('nama_kategori', 100);
            $table->text('deskripsi')->nullable();
            $table->text('isi_surat')->nullable();
            $table->boolean('lampiran_peserta')->default(false);
            $table->enum('kebutuhan', ['kegiatan', 'surat'])->default('kegiatan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori');
    }
};