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
            $table->unsignedBigInteger('id_jenis_dokumen'); 
            $table->unsignedBigInteger('id_kegiatan'); 
            
            $table->string('nama_file');
            $table->string('tipe_dokumen');
            $table->string('link_dokumen');
            
            $table->timestamps();

            $table->foreign('id_jenis_dokumen')->references('id_jenis_dokumen')->on('jenis_dokumen')->restrictOnDelete();
            $table->foreign('id_kegiatan')->references('id_kegiatan')->on('kegiatan')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen');
    }
};
