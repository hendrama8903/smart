<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Tabel Gang (lorong/blok) ─────────────────────────────────
        if (! Schema::hasTable('gang')) {
            Schema::create('gang', function (Blueprint $table) {
                $table->id();
                $table->string('nama_gang');
                $table->foreignId('koordinator_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('keterangan')->nullable();
                $table->boolean('aktif')->default(true);
                $table->timestamps();
            });
        }

        // ── 2. Tambah gang_id ke kartu_keluarga ─────────────────────────
        Schema::table('kartu_keluarga', function (Blueprint $table) {
            if (! Schema::hasColumn('kartu_keluarga', 'gang_id')) {
                $table->foreignId('gang_id')->nullable()->constrained('gang')->nullOnDelete()->after('id');
            }
        });

        // ── 3. Update iuran_tagihan ──────────────────────────────────────
        Schema::table('iuran_tagihan', function (Blueprint $table) {
            if (! Schema::hasColumn('iuran_tagihan', 'nominal_dibayar')) {
                $table->decimal('nominal_dibayar', 12, 2)->default(0)->after('nominal');
            }
            if (! Schema::hasColumn('iuran_tagihan', 'bukti_bayar')) {
                $table->string('bukti_bayar')->nullable()->after('keterangan');
            }
        });
        // Ubah enum status
        DB::statement("ALTER TABLE iuran_tagihan MODIFY COLUMN status ENUM('belum','sebagian','lunas') NOT NULL DEFAULT 'belum'");

        // ── 4. Tambah kolom ke kas ───────────────────────────────────────
        Schema::table('kas', function (Blueprint $table) {
            if (! Schema::hasColumn('kas', 'bukti')) {
                $table->string('bukti')->nullable()->after('keterangan');
            }
        });

        // ── 5. Tambah kolom ke jenis_iuran ──────────────────────────────
        Schema::table('jenis_iuran', function (Blueprint $table) {
            if (! Schema::hasColumn('jenis_iuran', 'keterangan')) {
                $table->string('keterangan')->nullable()->after('aktif');
            }
        });

        // ── 6. Seed kas_kategori tambahan ────────────────────────────────
        $existing = DB::table('kas_kategori')->pluck('nama')->toArray();
        $seed = [
            ['nama' => 'Sewa Bangku',    'tipe' => 'masuk'],
            ['nama' => 'Sewa Tenda',     'tipe' => 'masuk'],
            ['nama' => 'Denda',          'tipe' => 'masuk'],
            ['nama' => 'Donasi',         'tipe' => 'masuk'],
            ['nama' => 'Perlengkapan',   'tipe' => 'keluar'],
            ['nama' => 'Kebersihan',     'tipe' => 'keluar'],
            ['nama' => 'Administrasi',   'tipe' => 'keluar'],
        ];
        foreach ($seed as $s) {
            if (! in_array($s['nama'], $existing)) {
                DB::table('kas_kategori')->insert(array_merge($s, [
                    'created_at' => now(), 'updated_at' => now(),
                ]));
            }
        }
    }

    public function down(): void
    {
        Schema::table('kartu_keluarga', fn($t) => $t->dropForeign(['gang_id']));
        Schema::table('kartu_keluarga', fn($t) => $t->dropColumn('gang_id'));
        Schema::table('iuran_tagihan', fn($t) => $t->dropColumn(['nominal_dibayar', 'bukti_bayar']));
        Schema::table('kas', fn($t) => $t->dropColumn('bukti'));
        Schema::table('jenis_iuran', fn($t) => $t->dropColumn('keterangan'));
        Schema::dropIfExists('gang');
    }
};
