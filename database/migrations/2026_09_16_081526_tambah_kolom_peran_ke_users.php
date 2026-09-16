<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->unique()->after('id');
            $table->enum('peran', ['operator', 'kearsipan', 'superadmin'])
                  ->default('operator')->after('password');
            $table->string('unit_pengolah', 20)->nullable()->after('peran');
            $table->boolean('aktif')->default(true)->after('unit_pengolah');

            $table->foreign('unit_pengolah')
                  ->references('kode')->on('unit_pengolah')
                  ->nullOnDelete();
        });

        // Email tidak dipakai, jadi kewajibannya dilepas
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['unit_pengolah']);
            $table->dropColumn(['username', 'peran', 'unit_pengolah', 'aktif']);
        });
    }
};