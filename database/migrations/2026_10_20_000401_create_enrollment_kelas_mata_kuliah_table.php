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
        Schema::create('enrollment_kelas_mata_kuliah', function (Blueprint $table) {
            $table->id('id_enrollment_kelas_mk');
            $table->foreignId('id_enrollment_tingkat_kelas')
                  ->constrained('enrollment_tingkat_kelas', 'id_enrollment_tingkat_kelas')
                  ->onDelete('restrict');
            $table->foreignId('id_enrollment_mk')
                  ->constrained('enrollment_mata_kuliah', 'id_enrollment_mk')
                  ->onDelete('restrict');
            $table->foreignId('id_dosen')
                  ->constrained('dosen', 'id_dosen')
                  ->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment_kelas_mata_kuliah');
    }
};