<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel ini sebelumnya dibuat langsung di database (tidak lewat migration).
     * setup_fasilitas_booking sudah mengasumsikan tabel ini ada untuk ditambah kolom
     * (tarif_fasilitas_id, fasilitas_id, dll.), jadi migration ini diisi sebelumnya.
     */
    public function up(): void
    {
        Schema::create('pendopo_booking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kartu_keluarga_id')->nullable()->constrained('kartu_keluarga')->nullOnDelete();
            $table->string('nama_pemohon', 120);
            $table->string('nama_acara', 150);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->boolean('is_warga')->default(true);
            $table->decimal('tarif', 14, 2)->default(0);
            $table->decimal('deposit', 14, 2)->default(0);
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak', 'selesai', 'batal'])->default('menunggu');
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->index('status', 'pendopo_status_idx');
            $table->index('tanggal_mulai', 'pendopo_tanggal_idx');
            $table->index('kartu_keluarga_id', 'pendopo_kk_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendopo_booking');
    }
};
