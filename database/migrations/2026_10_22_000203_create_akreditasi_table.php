<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('akreditasi', function (Blueprint $table) {
            $table->id('id_akreditasi');
            $table->foreignId('id_user')
                ->constrained('users', 'id_user')
                ->restrictOnDelete();
            $table->string('keterangan', 50);        // contoh: "Baik Sekali"
            $table->string('link', 255)->nullable(); // file PDF
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akreditasi');
    }
};
