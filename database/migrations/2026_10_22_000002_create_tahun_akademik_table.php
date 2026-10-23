<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tahun_akademik', function (Blueprint $table) {
            $table->id('id_tahun_akademik');
            $table->string('tahun_akademik', 30)->unique(); // contoh: "Ganjil 2025/2026"
            $table->boolean('status')->default(false);      // true = tahun berjalan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tahun_akademik');
    }
};
