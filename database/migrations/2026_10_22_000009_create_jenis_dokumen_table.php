<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_dokumen', function (Blueprint $table) {
            $table->id('id_jenis_dokumen');
            $table->string('nama_jenis_dokumen', 100)->unique();
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_dokumen');
    }
};