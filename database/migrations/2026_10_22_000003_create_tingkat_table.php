<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tingkat', function (Blueprint $table) {
            $table->id('id_tingkat');
            // 1, 2, 3, 4
            $table->unsignedTinyInteger('tingkat')->unique();
            // contoh: "Tingkat 1"
            $table->string('keterangan', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tingkat');
    }
};
