<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel master koordinator gang
        Schema::create('koordinator_gang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warga_id')->constrained('warga')->cascadeOnDelete();
            $table->string('keterangan')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->unique('warga_id'); // satu warga = satu entri koordinator
        });

        // 2. Ubah FK gang.koordinator_id dari users ke koordinator_gang
        Schema::table('gang', function (Blueprint $table) {
            // Hapus FK lama ke users
            $table->dropForeign(['koordinator_id']);

            // Tambah FK baru ke koordinator_gang (nullable, bisa kosong dulu)
            $table->foreignId('koordinator_id')
                ->nullable()
                ->change();

            $table->foreign('koordinator_id')
                ->references('id')
                ->on('koordinator_gang')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('gang', function (Blueprint $table) {
            $table->dropForeign(['koordinator_id']);
            $table->foreign('koordinator_id')->references('id')->on('users')->nullOnDelete();
        });
        Schema::dropIfExists('koordinator_gang');
    }
};
