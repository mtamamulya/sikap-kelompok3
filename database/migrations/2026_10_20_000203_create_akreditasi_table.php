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
        Schema::create('akreditasi', function (Blueprint $table) {
    $table->id('id_akreditasi');
    $table->foreignId('id_user')->constrained('user', 'id_user');
    $table->string('keterangan');
    $table->string('link');
    $table->dateTime('create_date');
    $table->dateTime('update_date');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akreditasi');
    }
};
