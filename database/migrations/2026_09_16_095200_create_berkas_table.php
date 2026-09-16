<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berkas', function (Blueprint $table) {
            $table->id();

            $table->string('unit_pengolah', 20);
            $table->string('sub_bagian', 50)->nullable(); // hanya untuk Sekretariat

            // Penomoran per unit per tahun berkas, bukan tahun server
            $table->unsignedSmallInteger('tahun');
            $table->unsignedInteger('no_berkas');

            $table->string('kode_klasifikasi', 50)->nullable();
            $table->text('uraian');

            // Kurun waktu arsip di dalam berkas, diusulkan dari item lalu bisa ditimpa
            $table->unsignedSmallInteger('tahun_mulai')->nullable();
            $table->unsignedSmallInteger('tahun_selesai')->nullable();

            $table->unsignedInteger('jumlah_fisik')->nullable();
            $table->enum('satuan', ['Berkas', 'Lembar', 'Sampul'])->default('Berkas');

            $table->enum('skkad', ['Biasa/Terbuka', 'Terbatas', 'Rahasia', 'Sangat Rahasia'])
                  ->default('Biasa/Terbuka');

            // Penyimpanan diisi setelah berkas terverifikasi atau ditutup
            $table->foreignId('boks_id')->nullable()->constrained('boks')->nullOnDelete();
            $table->string('lokasi_simpan')->nullable();

            // Alur verifikasi oleh unit kearsipan
            $table->enum('status', ['draf', 'diajukan', 'terverifikasi'])->default('draf');
            $table->timestamp('diajukan_pada')->nullable();
            $table->timestamp('diverifikasi_pada')->nullable();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan_verifikasi')->nullable();

            // Penyerahan arsip inaktif ke unit kearsipan
            $table->date('dipindahkan_pada')->nullable();

            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['unit_pengolah', 'tahun', 'no_berkas'], 'berkas_unit_tahun_nomor_unique');

            $table->foreign('unit_pengolah')->references('kode')->on('unit_pengolah')->restrictOnDelete();
            $table->foreign('kode_klasifikasi')->references('kode_klasifikasi')->on('klasifikasi')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berkas');
    }
};