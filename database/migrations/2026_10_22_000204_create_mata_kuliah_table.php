<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mata_kuliah', function (Blueprint $table) {
            $table->id('id_mk');
            $table->foreignId('id_kurikulum')
                ->constrained('kurikulum', 'id_kurikulum')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('semester'); // 1-8
            $table->string('kode_mk', 20);
            $table->string('nama_mk', 150);
            $table->unsignedTinyInteger('sks_teori');
            $table->unsignedTinyInteger('sks_praktik');
            $table->unsignedTinyInteger('jam_teori');
            $table->unsignedTinyInteger('jam_praktek');
            $table->timestamps();

            $table->unique(['id_kurikulum', 'kode_mk']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_kuliah');
    }
};
