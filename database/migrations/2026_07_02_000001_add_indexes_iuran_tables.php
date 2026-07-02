<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Index periode_id untuk mempercepat query tutup buku & rekap
        Schema::table('iuran_tagihan', function (Blueprint $table) {
            $table->index('periode_id', 'idx_iuran_tagihan_periode_id');
        });

        // Unique constraint mencegah double-alokasi pembayaran ke tagihan yang sama
        Schema::table('iuran_alokasi', function (Blueprint $table) {
            $table->unique(['pembayaran_id', 'tagihan_id'], 'uq_alokasi_pembayaran_tagihan');
        });
    }

    public function down(): void
    {
        Schema::table('iuran_tagihan', function (Blueprint $table) {
            $table->dropIndex('idx_iuran_tagihan_periode_id');
        });

        Schema::table('iuran_alokasi', function (Blueprint $table) {
            $table->dropUnique('uq_alokasi_pembayaran_tagihan');
        });
    }
};
