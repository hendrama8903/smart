<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel ini sebelumnya dibuat langsung di database (tidak lewat migration).
     * update_pengumuman_table sudah mengasumsikan tabel ini ada untuk ditambah kolom
     * (kategori, file_lampiran, dll.), jadi migration ini diisi sebelumnya.
     */
    public function up(): void
    {
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 150);
            $table->text('isi');
            $table->date('tanggal');
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumuman');
    }
};
