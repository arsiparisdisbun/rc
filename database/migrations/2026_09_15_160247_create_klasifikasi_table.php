<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('klasifikasi', function (Blueprint $table) {
            $table->string('kode_klasifikasi', 50)->primary();
            $table->text('uraian');
            $table->unsignedSmallInteger('retensi_aktif')->default(0);
            $table->unsignedSmallInteger('retensi_inaktif')->default(0);
            $table->string('nasib_akhir', 50); // Musnah / Permanen / Dinilai Kembali
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('klasifikasi');
    }
};