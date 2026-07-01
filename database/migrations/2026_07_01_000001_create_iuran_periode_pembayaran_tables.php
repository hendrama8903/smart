<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Tabel periode iuran ─────────────────────────────────────────
        Schema::create('iuran_periode', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_iuran_id')->constrained('jenis_iuran')->cascadeOnDelete();
            $table->unsignedSmallInteger('tahun');
            $table->unsignedTinyInteger('bulan')->nullable(); // null = tahunan
            $table->enum('status', ['draft', 'buka', 'tutup'])->default('buka');
            $table->date('tanggal_buka')->nullable();
            $table->foreignId('dibuka_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tanggal_tutup')->nullable();
            $table->foreignId('ditutup_oleh')->nullable()->constrained('users')->nullOnDelete();
            // Snapshot saat tutup buku
            $table->decimal('snap_total_tagihan', 14, 2)->default(0);
            $table->decimal('snap_total_terkumpul', 14, 2)->default(0);
            $table->decimal('snap_total_tunggakan', 14, 2)->default(0);
            $table->text('catatan_penutupan')->nullable();
            $table->timestamps();

            $table->unique(['jenis_iuran_id', 'tahun', 'bulan']);
        });

        // ── 2. Tabel transaksi pembayaran ──────────────────────────────────
        Schema::create('iuran_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kartu_keluarga_id')->constrained('kartu_keluarga')->cascadeOnDelete();
            $table->foreignId('jenis_iuran_id')->constrained('jenis_iuran')->cascadeOnDelete();
            $table->date('tanggal_bayar');
            $table->decimal('jumlah_total', 14, 2);
            $table->string('metode', 50)->nullable();
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('keterangan')->nullable();
            $table->string('bukti_bayar')->nullable();
            $table->timestamps();
        });

        // ── 3. Tabel alokasi pembayaran ke tagihan (FIFO) ──────────────────
        Schema::create('iuran_alokasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_id')->constrained('iuran_pembayaran')->cascadeOnDelete();
            $table->foreignId('tagihan_id')->constrained('iuran_tagihan')->cascadeOnDelete();
            $table->decimal('jumlah', 14, 2);
            $table->timestamps();
        });

        // ── 4. Tambah kolom ke iuran_tagihan ──────────────────────────────
        Schema::table('iuran_tagihan', function (Blueprint $table) {
            $table->foreignId('periode_id')
                ->nullable()
                ->after('jenis_iuran_id')
                ->constrained('iuran_periode')
                ->nullOnDelete();
            $table->boolean('is_tunggakan')->default(false)->after('is_historis');
        });
    }

    public function down(): void
    {
        Schema::table('iuran_tagihan', function (Blueprint $table) {
            $table->dropForeign(['periode_id']);
            $table->dropColumn(['periode_id', 'is_tunggakan']);
        });
        Schema::dropIfExists('iuran_alokasi');
        Schema::dropIfExists('iuran_pembayaran');
        Schema::dropIfExists('iuran_periode');
    }
};
