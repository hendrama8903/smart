<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel ini sebelumnya dibuat langsung di database (tidak lewat migration).
     * create_menu_permissions_table & add_type_to_menu_table sudah mengasumsikan
     * tabel menu ada, jadi migration ini diisi sebelum keduanya. Kolom `type`
     * sengaja tidak disertakan karena ditambahkan oleh add_type_to_menu_table.
     */
    public function up(): void
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('menu')->cascadeOnDelete();
            $table->string('nama', 100);
            $table->string('icon', 50)->nullable();
            $table->string('controller', 100)->nullable();
            $table->string('fungsi', 50)->default('index');
            $table->string('url', 150)->nullable();
            $table->integer('urutan')->default(0);
            $table->string('roles', 150)->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};
