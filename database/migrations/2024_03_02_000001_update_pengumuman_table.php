<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengumuman', function (Blueprint $table) {
            if (! Schema::hasColumn('pengumuman', 'kategori')) {
                $table->enum('kategori', ['informasi','rapat','kegiatan','keuangan','darurat','lainnya'])
                    ->default('informasi')->after('judul');
            }
            if (! Schema::hasColumn('pengumuman', 'file_lampiran')) {
                $table->string('file_lampiran')->nullable()->after('isi'); // path file
            }
            if (! Schema::hasColumn('pengumuman', 'nama_file')) {
                $table->string('nama_file')->nullable()->after('file_lampiran'); // nama asli file
            }
            if (! Schema::hasColumn('pengumuman', 'penting')) {
                $table->boolean('penting')->default(false)->after('nama_file'); // flag penting/urgent
            }
            if (! Schema::hasColumn('pengumuman', 'aktif')) {
                $table->boolean('aktif')->default(true)->after('penting');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengumuman', function (Blueprint $table) {
            $table->dropColumn(['kategori', 'file_lampiran', 'nama_file', 'penting', 'aktif']);
        });
    }
};
