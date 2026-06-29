<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('nama')->unique();   // admin, ketua, sekretaris, bendahara, warga
                $table->string('label');            // Admin, Ketua RT, Sekretaris, Bendahara, Warga
                $table->string('keterangan')->nullable();
                $table->timestamps();
            });

            DB::table('roles')->insert([
                ['nama' => 'admin',      'label' => 'Admin',       'keterangan' => 'Administrator sistem', 'created_at' => now(), 'updated_at' => now()],
                ['nama' => 'ketua',      'label' => 'Ketua RT',    'keterangan' => 'Ketua RT',             'created_at' => now(), 'updated_at' => now()],
                ['nama' => 'sekretaris', 'label' => 'Sekretaris',  'keterangan' => 'Sekretaris RT',        'created_at' => now(), 'updated_at' => now()],
                ['nama' => 'bendahara',  'label' => 'Bendahara',   'keterangan' => 'Bendahara RT',         'created_at' => now(), 'updated_at' => now()],
                ['nama' => 'warga',      'label' => 'Warga',       'keterangan' => 'Warga umum',           'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
