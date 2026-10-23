<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('histori_pangkat_golongan', function (Blueprint $table) {
            $table->id('id_histori_pangkat_golongan');
            $table->foreignId('id_dosen')
                ->constrained('dosen', 'id_dosen')
                ->cascadeOnDelete();
            $table->enum('pangkat', [
                'Asisten Ahli',
                'Lektor',
                'Lektor Kepala',
                'Guru Besar',
            ]);
            $table->enum('golongan', [
                '3B', '3C', '3D',
                '4A', '4B', '4C', '4D',
            ]);
            $table->string('nomor_sk', 100);
            $table->date('tanggal_sk');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('histori_pangkat_golongan');
    }
};
