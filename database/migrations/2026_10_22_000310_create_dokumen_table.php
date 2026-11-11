<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id('id_dokumen');
            
            // Foreign Keys
            $table->foreignId('id_jenis_dokumen')
                  ->constrained('jenis_dokumen', 'id_jenis_dokumen')
                  ->restrictOnDelete();
                  
            // id_kegiatan WAJIB diisi (Tanpa nullable)
            $table->foreignId('id_kegiatan')
                  ->constrained('kegiatan', 'id_kegiatan')
                  ->cascadeOnDelete();

            // id_detail_kegiatan BOLEH KOSONG (Karena tidak semua dokumen berasal dari form)
            $table->foreignId('id_detail_kegiatan')
                  ->nullable()
                  ->constrained('detail_kegiatan', 'id_detail_kegiatan')
                  ->cascadeOnDelete();

            // Kolom Reguler
            $table->string('nama_file');
            $table->string('tipe_dokumen')->nullable();
            $table->string('link_dokumen')->nullable();
            $table->boolean('is_lampiran')->default(false);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen');
    }
};