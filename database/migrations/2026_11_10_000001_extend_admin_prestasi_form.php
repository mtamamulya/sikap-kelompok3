<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prestasi', function (Blueprint $table) {
            $table->dropForeign(['id_kegiatan']);
            $table->unsignedBigInteger('id_kegiatan')->nullable()->change();
            $table->foreign('id_kegiatan')
                ->references('id_kegiatan')
                ->on('kegiatan')
                ->nullOnDelete();
            $table->json('form_data')->nullable();
        });
        Schema::table('bukti_prestasi', function (Blueprint $table) {
            $table->string('link', 2048)->change();
        });
    }

    public function down(): void
    {
        if (DB::table('prestasi')->whereNull('id_kegiatan')->exists()) {
            throw new \RuntimeException('Cannot restore required activities while standalone achievements exist.');
        }
        if (DB::table('bukti_prestasi')->whereRaw('length(link) > 500')->exists()) {
            throw new \RuntimeException('Cannot restore 500-character proof links while longer links exist.');
        }

        Schema::table('prestasi', function (Blueprint $table) {
            $table->dropForeign(['id_kegiatan']);
            $table->unsignedBigInteger('id_kegiatan')->nullable(false)->change();
            $table->foreign('id_kegiatan')
                ->references('id_kegiatan')
                ->on('kegiatan')
                ->cascadeOnDelete();
            $table->dropColumn('form_data');
        });
        Schema::table('bukti_prestasi', function (Blueprint $table) {
            $table->string('link', 500)->change();
        });
    }
};
