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
        Schema::create('pengajaran', function (Blueprint $table) {
            $table->id('id_pengajaran');
            $table->foreignId('id_enrollment_kelas_mk')
                  ->constrained('enrollment_kelas_mata_kuliah', 'id_enrollment_kelas_mk')
                  ->onDelete('restrict');
            $table->foreignId('id_dokumen')
                  ->constrained('dokumen', 'id_dokumen')
                  ->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajaran');
    }
};