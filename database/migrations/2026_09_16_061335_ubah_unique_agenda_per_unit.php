<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nomor agenda kini direset per unit pengolah, bukan terpusat.
        Schema::table('arsip', function (Blueprint $table) {
            $table->dropUnique('arsip_agenda_unique');
        });

        Schema::table('arsip', function (Blueprint $table) {
            $table->unique(['unit_pengolah', 'jenis', 'tahun', 'no_urut'], 'arsip_agenda_unique');
        });
    }

    public function down(): void
    {
        Schema::table('arsip', function (Blueprint $table) {
            $table->dropUnique('arsip_agenda_unique');
        });

        Schema::table('arsip', function (Blueprint $table) {
            $table->unique(['jenis', 'tahun', 'no_urut'], 'arsip_agenda_unique');
        });
    }
};