<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu', function (Blueprint $table) {
            // menu = grup/induk, screen = halaman, button = tombol aksi
            $table->enum('type', ['menu', 'screen', 'button'])->default('screen')->after('nama');
        });
    }

    public function down(): void
    {
        Schema::table('menu', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
