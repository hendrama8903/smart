<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom baru ke kartu_keluarga
        Schema::table('kartu_keluarga', function (Blueprint $table) {
            if (! Schema::hasColumn('kartu_keluarga', 'tgl_daftar')) {
                $table->date('tgl_daftar')->nullable()->after('no_telepon');
            }
            if (! Schema::hasColumn('kartu_keluarga', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('tgl_daftar');
            }
        });

        // Tambah kolom baru ke warga
        Schema::table('warga', function (Blueprint $table) {
            if (! Schema::hasColumn('warga', 'agama')) {
                $table->string('agama', 30)->nullable()->after('tanggal_lahir');
            }
            if (! Schema::hasColumn('warga', 'pendidikan')) {
                $table->string('pendidikan', 50)->nullable()->after('agama');
            }
            if (! Schema::hasColumn('warga', 'status_perkawinan')) {
                $table->enum('status_perkawinan', ['belum_kawin', 'kawin', 'cerai_hidup', 'cerai_mati'])
                      ->default('belum_kawin')->after('pekerjaan');
            }
            if (! Schema::hasColumn('warga', 'status_warga')) {
                $table->enum('status_warga', ['aktif', 'pindah', 'meninggal'])
                      ->default('aktif')->after('status_tinggal');
            }
            if (! Schema::hasColumn('warga', 'tgl_masuk')) {
                $table->date('tgl_masuk')->nullable()->after('status_warga');
            }
            if (! Schema::hasColumn('warga', 'tgl_keluar')) {
                $table->date('tgl_keluar')->nullable()->after('tgl_masuk');
            }
            if (! Schema::hasColumn('warga', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('tgl_keluar');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kartu_keluarga', function (Blueprint $table) {
            $table->dropColumn(['tgl_daftar', 'keterangan']);
        });
        Schema::table('warga', function (Blueprint $table) {
            $table->dropColumn(['agama', 'pendidikan', 'status_perkawinan', 'status_warga', 'tgl_masuk', 'tgl_keluar', 'keterangan']);
        });
    }
};
