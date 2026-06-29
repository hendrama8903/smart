<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // gang sudah tidak punya koordinator_id (sudah mandiri)
        // Hanya perlu tambah gang_id ke koordinator_gang

        Schema::table('koordinator_gang', function (Blueprint $table) {
            // Hapus FK warga_id dulu agar bisa drop unique index
            $table->dropForeign(['warga_id']);
            $table->dropUnique(['warga_id']);

            // Pasang kembali FK warga_id
            $table->foreign('warga_id')->references('id')->on('warga')->cascadeOnDelete();

            // Tambah kolom gang_id
            $table->foreignId('gang_id')
                ->nullable()
                ->after('warga_id')
                ->constrained('gang')
                ->nullOnDelete();

            // Unique: satu warga hanya koordinator di satu gang
            $table->unique(['warga_id']);
        });
    }

    public function down(): void
    {
        Schema::table('koordinator_gang', function (Blueprint $table) {
            $table->dropForeign(['gang_id']);
            $table->dropColumn('gang_id');
            $table->unique(['warga_id']);
        });
        Schema::table('gang', function (Blueprint $table) {
            $table->foreignId('koordinator_id')->nullable()
                ->constrained('koordinator_gang')->nullOnDelete();
        });
    }
};
