<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan', function (Blueprint $table) {
            $table->id();
            $table->string('kelompok', 30);   // sub_bagian, kategori_keuangan, satuan, skkad
            $table->string('nilai', 100);
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();

            $table->unique(['kelompok', 'nilai']);
            $table->index('kelompok');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan');
    }
};