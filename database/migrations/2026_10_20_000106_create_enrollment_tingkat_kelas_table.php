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
        Schema::create('enrollment_tingkat_kelas', function (Blueprint $table) {
            $table->id('id_enrollment_tingkat_kelas');
            $table->foreignId('id_kelas')
                  ->constrained('kelas', 'id_kelas')
                  ->onDelete('restrict');
            $table->foreignId('id_prodi')
                  ->constrained('prodi', 'id_prodi')
                  ->onDelete('restrict');
            $table->foreignId('id_tingkat')
                  ->constrained('tingkat', 'id_tingkat')
                  ->onDelete('restrict');
            $table->foreignId('id_tahun_akademik')
                  ->constrained('tahun_akademik', 'id_tahun_akademik')
                  ->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment_tingkat_kelas');
    }
};