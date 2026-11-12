<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestasi', function (Blueprint $table) {
            $table->id('id_prestasi'); //
            
            // Foreign Keys
            $table->foreignId('id_user') //
                  ->constrained('users', 'id_user')
                  ->cascadeOnDelete(); // Pemilik prestasi

            $table->foreignId('created_by') //
                  ->constrained('users', 'id_user')
                  ->restrictOnDelete(); // Perekam data (tidak boleh dihapus sembarangan)

            $table->foreignId('id_kegiatan')
                  ->nullable() 
                  ->constrained('kegiatan', 'id_kegiatan')
                  ->cascadeOnDelete();

            // Kolom Reguler
            $table->string('capaian'); 
            $table->enum('jenis_prestasi', ['akademik', 'non akademik']);
            $table->string('bidang_lomba')->nullable();
            $table->string('nama_lomba');
            $table->string('penyelenggara')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('dosen_pembimbing')->nullable();
            $table->enum('tingkat_kejuaraan', ['kabupaten/kota', 'provinsi', 'nasional', 'internasional'])->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            
            // Asumsi nilainya 'individu' & 'kelompok'
            $table->enum('bentuk_partisipasi', ['individu', 'kelompok'])->nullable(); 
            
            $table->text('deskripsi')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestasi');
    }
};