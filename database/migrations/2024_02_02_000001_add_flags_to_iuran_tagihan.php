<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('iuran_tagihan', function (Blueprint $table) {
            // Flag tagihan khusus (warga tidak mampu, keringanan, dll.)
            if (! Schema::hasColumn('iuran_tagihan', 'is_keringanan')) {
                $table->boolean('is_keringanan')->default(false)->after('bukti_bayar');
            }
            if (! Schema::hasColumn('iuran_tagihan', 'catatan_khusus')) {
                $table->string('catatan_khusus')->nullable()->after('is_keringanan');
            }
            // Untuk tunggakan lama yang diimpor
            if (! Schema::hasColumn('iuran_tagihan', 'is_historis')) {
                $table->boolean('is_historis')->default(false)->after('catatan_khusus');
            }
        });
    }

    public function down(): void
    {
        Schema::table('iuran_tagihan', function (Blueprint $table) {
            $table->dropColumn(['is_keringanan', 'catatan_khusus', 'is_historis']);
        });
    }
};
