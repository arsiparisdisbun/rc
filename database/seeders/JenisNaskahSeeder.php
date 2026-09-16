<?php

namespace Database\Seeders;

use App\Models\JenisNaskah;
use Illuminate\Database\Seeder;

class JenisNaskahSeeder extends Seeder
{
    public function run(): void
    {
        $daftar = [
            ['Peraturan Daerah',                 'Arahan',        'Pengaturan'],
            ['Peraturan Gubernur',               'Arahan',        'Pengaturan'],
            ['Peraturan DPRD',                   'Arahan',        'Pengaturan'],
            ['Keputusan Gubernur',               'Arahan',        'Penetapan'],
            ['Keputusan DPRD',                   'Arahan',        'Penetapan'],
            ['Keputusan Pimpinan DPRD',          'Arahan',        'Penetapan'],
            ['Keputusan Badan Kehormatan DPRD',  'Arahan',        'Penetapan'],
            ['Surat Perintah',                   'Arahan',        'Penugasan'],
            ['Surat Tugas',                      'Arahan',        'Penugasan'],
            ['Surat Perjalanan Dinas',           'Arahan',        'Penugasan'],

            ['Nota Dinas',                       'Korespondensi', 'Internal'],
            ['Memo',                             'Korespondensi', 'Internal'],
            ['Disposisi',                        'Korespondensi', 'Internal'],
            ['Surat Dinas',                      'Korespondensi', 'Eksternal'],

            ['Instruksi',                        'Khusus',        null],
            ['Surat Edaran',                     'Khusus',        null],
            ['Surat Kuasa',                      'Khusus',        null],
            ['Berita Acara',                     'Khusus',        null],
            ['Surat Keterangan',                 'Khusus',        null],
            ['Surat Pengantar',                  'Khusus',        null],
            ['Pengumuman',                       'Khusus',        null],
            ['Laporan',                          'Khusus',        null],
            ['Telaahan Staf',                    'Khusus',        null],
            ['Notula',                           'Khusus',        null],
            ['Surat Undangan',                   'Khusus',        null],
            ['Surat Pernyataan Melaksanakan Tugas', 'Khusus',     null],
            ['Surat Panggilan',                  'Khusus',        null],
            ['Surat Izin',                       'Khusus',        null],
            ['Lembaran Daerah',                  'Khusus',        null],
            ['Berita Daerah',                    'Khusus',        null],
            ['Rekomendasi',                      'Khusus',        null],
            ['Radiogram',                        'Khusus',        null],
            ['Surat Tanda Tamat Pendidikan dan Pelatihan (STTPP)', 'Khusus', null],
            ['Sertifikat',                       'Khusus',        null],
            ['Piagam',                           'Khusus',        null],
            ['Surat Perjanjian',                 'Khusus',        null],
        ];

        foreach ($daftar as $i => [$nama, $kelompok, $sub]) {
            JenisNaskah::updateOrCreate(
                ['nama' => $nama],
                ['kelompok' => $kelompok, 'sub_kelompok' => $sub, 'urutan' => $i + 1, 'aktif' => true]
            );
        }

        $this->command->info(count($daftar) . ' jenis naskah dinas tersimpan.');
    }
}