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
        Schema::create('mata_kuliah', function (Blueprint $table) {
    $table->id('id_mk');
    $table->foreignId('id_kurikulum')->constrained('kurikulum', 'id_kurikulum');
    $table->integer('semester');
    $table->string('kode_mk');
    $table->string('nama_mk');
    $table->integer('sks_teori');
    $table->integer('sks_praktik');
    $table->integer('jam_teori');
    $table->integer('jam_praktek');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mata_kuliah');
    }
};
