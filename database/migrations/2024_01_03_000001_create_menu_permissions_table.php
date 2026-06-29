<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menu')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->boolean('can_add')->default(false);
            $table->boolean('can_edit')->default(false);
            $table->boolean('can_delete')->default(false);
            $table->boolean('can_export')->default(false);
            $table->unique(['menu_id', 'role_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_permissions');
    }
};
