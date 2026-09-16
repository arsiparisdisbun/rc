<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_naskah', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 120)->unique();
            $table->string('kelompok', 80);
            $table->string('sub_kelompok', 80)->nullable();
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_naskah');
    }
};