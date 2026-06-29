<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_user')->nullable();     // simpan nama user saat log dibuat
            $table->string('aksi', 30);                 // created, updated, deleted, login, logout, export, bayar, dll.
            $table->string('modul', 60)->nullable();    // Warga, KartuKeluarga, Kas, Iuran, Pengumuman, dll.
            $table->unsignedBigInteger('modul_id')->nullable();
            $table->string('deskripsi')->nullable();    // ringkasan human-readable
            $table->json('sebelum')->nullable();        // nilai sebelum perubahan
            $table->json('sesudah')->nullable();        // nilai sesudah perubahan
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
