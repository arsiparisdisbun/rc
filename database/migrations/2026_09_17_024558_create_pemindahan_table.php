<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemindahan', function (Blueprint $table) {
            $table->id();

            $table->string('unit_pengolah', 20);

            // Nomor berita acara diketik manual mengikuti penomoran surat instansi
            $table->string('nomor_ba', 100)->nullable();
            $table->date('tanggal_ba')->nullable();

            // Penanda tangan diketik per penyerahan, bukan diambil dari akun,
            // karena pejabat penanda tangan bisa berganti.
            $table->string('pihak1_nama')->nullable();
            $table->string('pihak1_nip', 30)->nullable();
            $table->string('pihak1_pangkat')->nullable();
            $table->string('pihak1_jabatan')->nullable();

            $table->string('pihak2_nama')->nullable();
            $table->string('pihak2_nip', 30)->nullable();
            $table->string('pihak2_pangkat')->nullable();
            $table->string('pihak2_jabatan')->nullable();

            $table->enum('status', ['diajukan', 'diterima', 'ditolak'])->default('diajukan');
            $table->timestamp('diajukan_pada')->nullable();
            $table->timestamp('diproses_pada')->nullable();

            $table->foreignId('diajukan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->nullOnDelete();

            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('unit_pengolah')->references('kode')->on('unit_pengolah')->restrictOnDelete();
        });

        Schema::table('berkas', function (Blueprint $table) {
            $table->foreignId('pemindahan_id')->nullable()->after('boks_id')
                  ->constrained('pemindahan')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('berkas', function (Blueprint $table) {
            $table->dropForeign(['pemindahan_id']);
            $table->dropColumn('pemindahan_id');
        });

        Schema::dropIfExists('pemindahan');
    }
};