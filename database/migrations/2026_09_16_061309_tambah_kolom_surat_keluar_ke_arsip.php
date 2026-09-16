<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('arsip', function (Blueprint $table) {
            $table->foreignId('jenis_naskah_id')->nullable()->after('unit_pengolah')
                  ->constrained('jenis_naskah')->nullOnDelete();
            $table->date('tanggal_upload')->nullable()->after('tanggal_penerimaan');
            $table->date('tanggal_verifikasi')->nullable()->after('tanggal_upload');
            $table->unsignedSmallInteger('jumlah_lembar')->nullable()->after('lampiran');
            $table->string('pembuat')->nullable()->after('kepada');
            $table->string('kode_tnde', 50)->nullable()->after('kode_klasifikasi');
        });
    }

    public function down(): void
    {
        Schema::table('arsip', function (Blueprint $table) {
            $table->dropForeign(['jenis_naskah_id']);
            $table->dropColumn([
                'jenis_naskah_id', 'tanggal_upload', 'tanggal_verifikasi',
                'jumlah_lembar', 'pembuat', 'kode_tnde',
            ]);
        });
    }
};