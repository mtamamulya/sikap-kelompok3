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
        Schema::create('enrollment_mahasiswa_tingkat_kelas', function (Blueprint $table) {
            $table->id('id_enrollment_mahasiswa_tingkat_kelas');
            $table->foreignId('id_enrollment_tingkat_kelas')
                  ->constrained('enrollment_tingkat_kelas', 'id_enrollment_tingkat_kelas')
                  ->onDelete('restrict');
            $table->foreignId('id_mahasiswa')
                  ->constrained('mahasiswa', 'id_mahasiswa')
                  ->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment_mahasiswa_tingkat_kelas');
    }
};