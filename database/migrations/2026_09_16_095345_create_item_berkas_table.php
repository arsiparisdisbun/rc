<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_berkas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('berkas_id')->constrained('berkas')->cascadeOnDelete();
            $table->unsignedSmallInteger('nomor_item');

            // Bila terisi, data item diambil dari arsip surat masuk/keluar.
            // Bila kosong, item diketik sendiri dan datanya disimpan di kolom bawah.
            $table->foreignId('arsip_id')->nullable()->constrained('arsip')->nullOnDelete();

            $table->string('nomor_surat', 500)->nullable();
            $table->text('uraian')->nullable();
            $table->date('tanggal')->nullable();
            $table->unsignedSmallInteger('tahun')->nullable(); // bila hanya diketahui tahunnya

            $table->unsignedInteger('jumlah')->nullable();
            $table->enum('satuan', ['Berkas', 'Lembar', 'Sampul'])->default('Lembar');

            $table->enum('skkad', ['Biasa/Terbuka', 'Terbatas', 'Rahasia', 'Sangat Rahasia'])
                  ->default('Biasa/Terbuka');

            $table->string('kode_klasifikasi', 50)->nullable();
            $table->text('keterangan')->nullable();

            $table->timestamps();

            $table->unique(['berkas_id', 'nomor_item'], 'item_berkas_nomor_unique');

            // Satu arsip tidak boleh masuk dua berkas sekaligus
            $table->unique('arsip_id', 'item_berkas_arsip_unique');

            $table->foreign('kode_klasifikasi')
                  ->references('kode_klasifikasi')->on('klasifikasi')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_berkas');
    }
};