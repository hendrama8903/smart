<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah enum status_tinggal di kartu_keluarga menjadi status kepemilikan rumah
        DB::statement("ALTER TABLE kartu_keluarga MODIFY COLUMN status_tinggal ENUM('milik','sewa','numpang') NOT NULL DEFAULT 'milik'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE kartu_keluarga MODIFY COLUMN status_tinggal ENUM('tetap','kontrak') NOT NULL DEFAULT 'tetap'");
    }
};
