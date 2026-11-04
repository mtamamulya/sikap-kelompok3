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
        Schema::create('bukti_history', function (Blueprint $table) {
            $table->id('id_bukti_history');
            $table->foreignId('id_dokumen')
                ->constrained('dokumen', 'id_dokumen')
                ->cascadeOnDelete();
            $table->foreignId('id_timeline')
                ->constrained('timeline', 'id_timeline')
                ->cascadeOnDelete();
            $table->unique(['id_dokumen', 'id_timeline']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bukti_history');
    }
};
