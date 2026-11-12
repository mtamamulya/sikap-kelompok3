<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skim', function (Blueprint $table) {
            $table->id('id_skim');
            $table->string('nama_skim', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skim');
    }
};