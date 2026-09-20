<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jejak_audit', function (Blueprint $table) {
            $table->id();

            // Objek yang diubah, disimpan sebagai nama model dan id-nya
            $table->string('model', 100);
            $table->unsignedBigInteger('model_id');

            $table->enum('aksi', ['tambah', 'ubah', 'hapus']);

            // Isi sebelum dan sesudah, hanya kolom yang berubah
            $table->json('sebelum')->nullable();
            $table->json('sesudah')->nullable();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_user')->nullable(); // disalin agar tetap terbaca bila akun dihapus
            $table->string('unit_pengolah', 20)->nullable();

            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['model', 'model_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jejak_audit');
    }
};