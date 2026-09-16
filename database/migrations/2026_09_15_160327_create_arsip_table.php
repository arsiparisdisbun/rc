<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arsip', function (Blueprint $table) {
            $table->id();

            // Pembeda jenis arsip
            $table->enum('jenis', ['masuk', 'keluar', 'manual']);

            // Penomoran agenda: direset per jenis, per tahun
            $table->unsignedInteger('no_urut');
            $table->unsignedSmallInteger('tahun');
            $table->string('no_tnde', 50)->nullable();

            // Identitas dokumen
            $table->string('nomor_surat')->nullable(); // arsip manual boleh tanpa nomor
            $table->date('tanggal_surat');
            $table->date('tanggal_penerimaan')->nullable(); // hanya untuk surat masuk
            $table->string('sifat', 50)->default('Biasa/Terbuka');
            $table->string('lampiran', 100)->nullable();
            $table->text('isi_ringkas');
            $table->string('dari')->nullable();
            $table->string('kepada')->nullable();
            $table->string('tingkat_perkembangan', 50)->nullable();

            // Relasi ke master klasifikasi + JRA
            $table->string('kode_klasifikasi', 50);

            // Penyimpanan
            $table->string('lokasi_simpan')->nullable(); // boks / rak fisik
            $table->string('dokumen_path')->nullable();

            $table->timestamps();

            // Nomor ganda jadi mustahil di level database
            $table->unique(['jenis', 'tahun', 'no_urut'], 'arsip_agenda_unique');

            $table->foreign('kode_klasifikasi')
                  ->references('kode_klasifikasi')
                  ->on('klasifikasi')
                  ->restrictOnDelete(); // kode yang sudah dipakai tak bisa dihapus
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arsip');
    }
};