<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boks', function (Blueprint $table) {
            $table->id();
            $table->string('unit_pengolah', 20);

            // Penomoran terpisah antara boks aktif dan inaktif,
            // menyambung terus tanpa direset tiap tahun.
            $table->enum('jenis', ['aktif', 'inaktif']);
            $table->unsignedInteger('nomor');

            $table->string('lokasi')->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('terpakai')->default(true);
            $table->timestamps();

            $table->unique(['unit_pengolah', 'jenis', 'nomor'], 'boks_unit_jenis_nomor_unique');

            $table->foreign('unit_pengolah')
                  ->references('kode')->on('unit_pengolah')
                  ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boks');
    }
};