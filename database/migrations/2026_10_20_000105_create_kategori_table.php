<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori', function (Blueprint $table) {
            // 1. ID_KATEGORI (Primary Key)
            $table->id('id_kategori');
            
            // 2. ID_JENIS_KEGIATAN (Foreign Key ke tabel jenis_kegiatan)
            $table->foreignId('id_jenis_kegiatan')
                  ->constrained('jenis_kegiatan', 'id_jenis_kegiatan')
                  ->cascadeOnDelete();
            
            // 3. NAMA_KATEGORI (Bentuk string karena isinya bebas seperti lomba, magang, dll)
            $table->string('nama_kategori', 150);
            
            // 4. DESKRIPSI (Bentuk text agar bisa memuat banyak karakter)
            $table->text('deskripsi')->nullable();
            
            // Bawaan Laravel (created_at & updated_at)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori');
    }
};