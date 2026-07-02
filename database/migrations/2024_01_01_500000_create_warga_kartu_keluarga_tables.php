<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel dasar ini sebelumnya dibuat langsung di database (tidak lewat migration),
     * sehingga fresh install/CI gagal karena migration lain (mis. add_fields_to_users_table,
     * update_warga_tables) mengasumsikan tabel ini sudah ada. Struktur di sini mencerminkan
     * kondisi tabel SEBELUM migration-migration lanjutan menambah kolom (gang_id, foto,
     * deleted_at, dll.), supaya urutan migration tetap konsisten.
     */
    public function up(): void
    {
        Schema::create('kartu_keluarga', function (Blueprint $table) {
            $table->id();
            $table->string('no_kk', 20)->unique();
            $table->string('kepala_keluarga', 120);
            $table->string('blok', 10)->nullable();
            $table->string('no_rumah', 10)->nullable();
            $table->string('alamat')->nullable();
            $table->string('rt', 5)->default('001');
            $table->string('rw', 5)->default('015');
            $table->string('no_telepon', 20)->nullable();
            $table->enum('status_tinggal', ['tetap', 'kontrak'])->default('tetap');
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->index('blok');
        });

        Schema::create('warga', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kartu_keluarga_id')->nullable()->constrained('kartu_keluarga')->nullOnDelete();
            $table->string('nik', 20)->unique();
            $table->string('nama', 120);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir', 80)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('pekerjaan', 80)->nullable();
            $table->string('hubungan', 40)->nullable();
            $table->string('no_telepon', 20)->nullable();
            $table->enum('status_tinggal', ['tetap', 'kontrak', 'kos', 'numpang'])->default('tetap');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warga');
        Schema::dropIfExists('kartu_keluarga');
    }
};
