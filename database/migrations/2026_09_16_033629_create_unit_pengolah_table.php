<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_pengolah', function (Blueprint $table) {
            $table->string('kode', 20);
            $table->string('nama');
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->primary('kode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_pengolah');
    }
};