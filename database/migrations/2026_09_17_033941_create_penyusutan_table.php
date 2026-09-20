<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyusutan', function (Blueprint $table) {
            $table->id();

            // Dua jalur akhir sesuai nasib akhir pada JRA
            $table->enum('jenis', ['musnah', 'serah']);

            $table->string('nomor_ba', 100)->nullable();
            $table->date('tanggal_ba')->nullable();

            // PIHAK I — Unit Kearsipan
            $table->string('pihak1_nama')->nullable();
            $table->string('pihak1_nip', 30)->nullable();
            $table->string('pihak1_pangkat')->nullable();
            $table->string('pihak1_jabatan')->nullable();

            // PIHAK II — penerima (jalur serah)
            $table->string('pihak2_nama')->nullable();
            $table->string('pihak2_nip', 30)->nullable();
            $table->string('pihak2_pangkat')->nullable();
            $table->string('pihak2_jabatan')->nullable();
            $table->string('pihak2_instansi')->nullable();

            // Dua saksi, diperlukan pada pemusnahan
            $table->string('saksi1_nama')->nullable();
            $table->string('saksi1_nip', 30)->nullable();
            $table->string('saksi1_jabatan')->nullable();
            $table->string('saksi2_nama')->nullable();
            $table->string('saksi2_nip', 30)->nullable();
            $table->string('saksi2_jabatan')->nullable();

            // Pelaksanaan pemusnahan
            $table->string('tempat')->nullable();
            $table->string('cara', 100)->nullable(); // dibakar, dicacah, dilebur

            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::table('berkas', function (Blueprint $table) {
            $table->foreignId('penyusutan_id')->nullable()->after('pemindahan_id')
                  ->constrained('penyusutan')->nullOnDelete();
            $table->date('disusutkan_pada')->nullable()->after('dipindahkan_pada');
        });
    }

    public function down(): void
    {
        Schema::table('berkas', function (Blueprint $table) {
            $table->dropForeign(['penyusutan_id']);
            $table->dropColumn(['penyusutan_id', 'disusutkan_pada']);
        });

        Schema::dropIfExists('penyusutan');
    }
};