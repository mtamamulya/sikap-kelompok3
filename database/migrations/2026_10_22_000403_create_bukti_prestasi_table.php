<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bukti_prestasi', function (Blueprint $table) {
            $table->id('id_bukti_prestasi');
            $table->foreignId('id_prestasi')
                ->constrained('prestasi', 'id_prestasi')
                ->cascadeOnDelete();
            $table->foreignId('id_jenis_dokumen')
                ->constrained('jenis_dokumen', 'id_jenis_dokumen')
                ->restrictOnDelete();

            $table->string('link', 500);
            $table->string('nama_file', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bukti_prestasi');
    }
};