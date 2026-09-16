<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('arsip', function (Blueprint $table) {
            $table->dropForeign(['kode_klasifikasi']);
        });

        Schema::table('arsip', function (Blueprint $table) {
            $table->string('kode_klasifikasi', 50)->nullable()->change();
            $table->foreign('kode_klasifikasi')
                  ->references('kode_klasifikasi')->on('klasifikasi')
                  ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('arsip', function (Blueprint $table) {
            $table->dropForeign(['kode_klasifikasi']);
        });

        Schema::table('arsip', function (Blueprint $table) {
            $table->string('kode_klasifikasi', 50)->nullable(false)->change();
            $table->foreign('kode_klasifikasi')
                  ->references('kode_klasifikasi')->on('klasifikasi')
                  ->restrictOnDelete();
        });
    }
};