<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('arsip', function (Blueprint $table) {
            // Surat tugas kerap memuat puluhan nama penerima sekaligus
            $table->text('kepada')->nullable()->change();
            $table->text('dari')->nullable()->change();
            $table->text('pembuat')->nullable()->change();
            $table->string('nomor_surat', 500)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('arsip', function (Blueprint $table) {
            $table->string('kepada', 255)->nullable()->change();
            $table->string('dari', 255)->nullable()->change();
            $table->string('pembuat', 255)->nullable()->change();
            $table->string('nomor_surat', 255)->nullable()->change();
        });
    }
};