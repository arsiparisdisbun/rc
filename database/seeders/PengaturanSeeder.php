<?php

namespace Database\Seeders;

use App\Models\Pengaturan;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    public function run(): void
    {
        $bawaan = [
            'sub_bagian' => ['Umum dan Kepegawaian', 'Sungram', 'Keuangan'],
            'kategori_keuangan' => ['SPJ Ganti Uang', 'Belanja LS', 'Akuntansi', 'PAD'],
            'satuan' => ['Berkas', 'Lembar', 'Sampul'],
            'skkad' => ['Biasa/Terbuka', 'Terbatas', 'Rahasia', 'Sangat Rahasia'],
        ];

        foreach ($bawaan as $kelompok => $daftar) {
            foreach ($daftar as $i => $nilai) {
                // firstOrCreate agar aman dijalankan ulang tanpa menggandakan data
                Pengaturan::firstOrCreate(
                    ['kelompok' => $kelompok, 'nilai' => $nilai],
                    ['urutan' => $i]
                );
            }
        }
    }
}