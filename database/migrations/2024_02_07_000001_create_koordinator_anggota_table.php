<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('koordinator_anggota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('koordinator_id')->constrained('koordinator_gang')->cascadeOnDelete();
            $table->foreignId('warga_id')->constrained('warga')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['koordinator_id', 'warga_id']); // satu warga tidak duplikat per koordinator
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('koordinator_anggota');
    }
};
