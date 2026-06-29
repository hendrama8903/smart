<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus data lama, ganti struktur
        // Drop dan recreate tabel (cara paling aman untuk perubahan struktural besar)
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Schema::drop('koordinator_anggota');
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');

        Schema::create('koordinator_anggota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('koordinator_id')->constrained('koordinator_gang')->cascadeOnDelete();
            $table->foreignId('kartu_keluarga_id')->constrained('kartu_keluarga')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['koordinator_id', 'kartu_keluarga_id']);
        });
    }

    public function down(): void
    {
        Schema::table('koordinator_anggota', function (Blueprint $table) {
            $table->dropForeign(['kartu_keluarga_id']);
            $table->dropUnique(['koordinator_id', 'kartu_keluarga_id']);
            $table->dropColumn('kartu_keluarga_id');

            $table->foreignId('warga_id')->constrained('warga')->cascadeOnDelete();
            $table->unique(['koordinator_id', 'warga_id']);
        });
    }
};
