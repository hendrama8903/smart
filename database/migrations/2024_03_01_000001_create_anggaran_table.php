<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggaran', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->foreignId('kategori_id')->nullable()->constrained('kas_kategori')->nullOnDelete();
            $table->enum('tipe', ['masuk', 'keluar']);
            $table->string('nama_pos');                    // cth: "Iuran Bulanan", "Sewa Pendopo"
            $table->decimal('nominal_rencana', 14, 2);    // target setahun
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggaran');
    }
};
