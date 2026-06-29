<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('piutang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kartu_keluarga_id')->nullable()->constrained('kartu_keluarga')->nullOnDelete();
            $table->string('nama_peminjam');                          // nama lengkap, bisa beda dari KK
            $table->decimal('jumlah', 12, 2);                        // total dipinjam
            $table->decimal('jumlah_kembali', 12, 2)->default(0);    // sudah dikembalikan
            $table->date('tanggal_pinjam');
            $table->date('jatuh_tempo')->nullable();
            $table->date('tanggal_lunas')->nullable();
            $table->enum('status', ['aktif','lunas','macet'])->default('aktif');
            $table->string('keterangan')->nullable();
            $table->string('bukti')->nullable();                      // scan surat/bukti
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('piutang_cicilan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('piutang_id')->constrained('piutang')->cascadeOnDelete();
            $table->date('tanggal');
            $table->decimal('jumlah', 12, 2);
            $table->string('keterangan')->nullable();
            $table->string('bukti')->nullable();
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('piutang_cicilan');
        Schema::dropIfExists('piutang');
    }
};
