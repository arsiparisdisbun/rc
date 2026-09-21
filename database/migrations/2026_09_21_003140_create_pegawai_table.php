<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 30)->nullable()->unique();
            $table->string('nama');
            $table->string('jabatan')->nullable();
            $table->string('unit_pengolah', 20)->nullable();
            $table->enum('status', ['aktif', 'pensiun', 'pindah', 'berhenti', 'meninggal'])->default('aktif');
            $table->date('tanggal_status')->nullable(); // kapan status berubah dari aktif
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('unit_pengolah')->references('kode')->on('unit_pengolah')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};