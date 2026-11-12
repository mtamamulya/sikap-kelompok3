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
            
            // id_kegiatan harus nullable karena bisa jadi ini anggota prestasi
            $table->foreignId('id_kegiatan')
                ->nullable()
                ->constrained('kegiatan', 'id_kegiatan')
                ->cascadeOnDelete();
                
            // Tambahkan id_prestasi (nullable) sesuai dengan Model
            $table->foreignId('id_prestasi')
                ->nullable()
                ->constrained('prestasi', 'id_prestasi')
                ->cascadeOnDelete();

            $table->foreignId('id_user')
                ->constrained('users', 'id_user')
                ->restrictOnDelete();
                
            $table->string('peran', 50);
            $table->timestamps();

            // Mencegah duplikasi user yang sama di 1 kegiatan
            $table->unique(['id_kegiatan', 'id_user']);
            
            // Mencegah duplikasi user yang sama di 1 prestasi
            $table->unique(['id_prestasi', 'id_user']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota_kegiatan');
    }
};