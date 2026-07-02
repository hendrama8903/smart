<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tabel-tabel dasar ini sebelumnya dibuat langsung di database (tidak lewat migration),
     * padahal setup_keuangan_tables & add_flags_to_iuran_tagihan mengasumsikan sudah ada.
     * Struktur di sini mencerminkan kondisi SEBELUM migration lanjutan menambah kolom
     * (nominal_dibayar, bukti_bayar, is_keringanan, dst.) agar urutan migration konsisten.
     */
    public function up(): void
    {
        Schema::create('kas_kategori', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 80);
            $table->enum('tipe', ['masuk', 'keluar']);
            $table->timestamps();
        });

        DB::table('kas_kategori')->insert([
            ['nama' => 'Iuran',         'tipe' => 'masuk',  'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Sewa Pendopo',  'tipe' => 'masuk',  'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Sumbangan',     'tipe' => 'masuk',  'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Operasional',   'tipe' => 'keluar', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Lain-lain',     'tipe' => 'keluar', 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::create('jenis_iuran', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 80);
            $table->decimal('nominal', 14, 2)->default(0);
            $table->enum('periode', ['bulanan', 'tahunan', 'insidental'])->default('bulanan');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        DB::table('jenis_iuran')->insert([
            'nama' => 'Iuran Sampah', 'nominal' => 50000, 'periode' => 'bulanan',
            'aktif' => 1, 'created_at' => now(), 'updated_at' => now(),
        ]);

        Schema::create('kas', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('kategori_id')->nullable()->constrained('kas_kategori')->nullOnDelete();
            $table->enum('tipe', ['masuk', 'keluar']);
            $table->decimal('jumlah', 14, 2);
            $table->string('keterangan');
            $table->string('ref_tabel', 50)->nullable();
            $table->unsignedBigInteger('ref_id')->nullable();
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('tanggal');
            $table->index('tipe');
        });

        Schema::create('iuran_tagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kartu_keluarga_id')->constrained('kartu_keluarga')->cascadeOnDelete();
            $table->foreignId('jenis_iuran_id')->constrained('jenis_iuran');
            $table->date('periode');
            $table->decimal('nominal', 14, 2);
            $table->enum('status', ['belum', 'lunas'])->default('belum');
            $table->date('tanggal_bayar')->nullable();
            $table->enum('metode', ['tunai', 'transfer', 'qris'])->nullable();
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['kartu_keluarga_id', 'jenis_iuran_id', 'periode'], 'iuran_kk_jenis_periode_unique');
            $table->index('status', 'iuran_status_idx');
            $table->index('petugas_id', 'iuran_petugas_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iuran_tagihan');
        Schema::dropIfExists('kas');
        Schema::dropIfExists('jenis_iuran');
        Schema::dropIfExists('kas_kategori');
    }
};
