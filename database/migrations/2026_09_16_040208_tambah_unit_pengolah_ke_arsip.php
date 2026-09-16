<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('arsip', function (Blueprint $table) {
            $table->string('unit_pengolah', 20)->nullable()->after('jenis');
        });

        Schema::table('arsip', function (Blueprint $table) {
            $table->foreign('unit_pengolah')
                  ->references('kode')->on('unit_pengolah')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('arsip', function (Blueprint $table) {
            $table->dropForeign(['unit_pengolah']);
            $table->dropColumn('unit_pengolah');
        });
    }
};