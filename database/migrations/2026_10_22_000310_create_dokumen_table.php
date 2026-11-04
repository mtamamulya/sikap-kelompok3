<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id('id_dokumen');
            $table->foreignId('id_jenis_dokumen')
                ->constrained('jenis_dokumen', 'id_jenis_dokumen')
                ->restrictOnDelete();
            $table->foreignId('id_kegiatan')
                ->constrained('kegiatan', 'id_kegiatan')
                ->cascadeOnDelete();
            $table->string('nama_file', 255);
            $table->string('tipe_dokumen', 100);
            $table->string('link_dokumen', 500);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen');
    }
};
