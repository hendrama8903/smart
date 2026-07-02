<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tabel-tabel ini sebelumnya dibuat langsung di database (tidak lewat migration)
     * dan tidak pernah diubah oleh migration lain, sehingga aman ditambahkan di sini
     * dalam bentuk struktur akhirnya sekaligus.
     */
    public function up(): void
    {
        Schema::create('pengaturan', function (Blueprint $table) {
            $table->id();
            $table->string('kunci', 80)->unique();
            $table->text('nilai')->nullable();
            $table->timestamps();
        });

        DB::table('pengaturan')->insert([
            ['kunci' => 'nama_rt',              'nilai' => 'Perum Permata Regency', 'created_at' => now(), 'updated_at' => now()],
            ['kunci' => 'rt',                    'nilai' => '001',                   'created_at' => now(), 'updated_at' => now()],
            ['kunci' => 'rw',                    'nilai' => '015',                   'created_at' => now(), 'updated_at' => now()],
            ['kunci' => 'tarif_pendopo_warga',   'nilai' => '300000',                'created_at' => now(), 'updated_at' => now()],
            ['kunci' => 'tarif_pendopo_umum',    'nilai' => '500000',                'created_at' => now(), 'updated_at' => now()],
            ['kunci' => 'deposit_pendopo',       'nilai' => '100000',                'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::create('penggalangan', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 150);
            $table->text('deskripsi')->nullable();
            $table->decimal('target', 14, 2)->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status', ['berjalan', 'selesai', 'batal'])->default('berjalan');
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('status', 'penggalangan_status_idx');
        });

        Schema::create('sumbangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penggalangan_id')->constrained('penggalangan')->cascadeOnDelete();
            $table->foreignId('kartu_keluarga_id')->nullable()->constrained('kartu_keluarga')->nullOnDelete();
            $table->string('nama_donatur', 120)->nullable();
            $table->decimal('nominal', 14, 2);
            $table->date('tanggal');
            $table->enum('metode', ['tunai', 'transfer', 'qris'])->nullable();
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('penggalangan_id', 'sumbangan_penggalangan_idx');
            $table->index('kartu_keluarga_id', 'sumbangan_kk_idx');
        });

        Schema::create('ronda_regu', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50);
            $table->string('pos', 50)->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('ronda_jadwal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ronda_regu_id')->constrained('ronda_regu')->cascadeOnDelete();
            $table->date('tanggal')->unique('ronda_jadwal_tanggal_unique');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('ronda_anggota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ronda_regu_id')->constrained('ronda_regu')->cascadeOnDelete();
            $table->foreignId('warga_id')->constrained('warga')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['ronda_regu_id', 'warga_id'], 'ronda_anggota_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ronda_anggota');
        Schema::dropIfExists('ronda_jadwal');
        Schema::dropIfExists('ronda_regu');
        Schema::dropIfExists('sumbangan');
        Schema::dropIfExists('penggalangan');
        Schema::dropIfExists('pengaturan');
    }
};
