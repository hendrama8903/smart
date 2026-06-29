<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warga', function (Blueprint $table) {
            if (! Schema::hasColumn('warga', 'foto')) {
                $table->string('foto')->nullable()->after('nik');
            }
        });
    }

    public function down(): void
    {
        Schema::table('warga', function (Blueprint $table) {
            $table->dropColumn('foto');
        });
    }
};
