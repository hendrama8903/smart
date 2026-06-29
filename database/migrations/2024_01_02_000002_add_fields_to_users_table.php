<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'role_id')) {
                $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete()->after('id');
            }
            if (! Schema::hasColumn('users', 'warga_id')) {
                $table->unsignedBigInteger('warga_id')->nullable()->after('role_id');
                $table->foreign('warga_id')->references('id')->on('warga')->nullOnDelete();
            }
            if (! Schema::hasColumn('users', 'username')) {
                $table->string('username')->unique()->nullable()->after('name');
            }
            if (! Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->after('password');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['warga_id']);
            $table->dropColumn(['role_id', 'warga_id', 'username', 'status']);
        });
    }
};
