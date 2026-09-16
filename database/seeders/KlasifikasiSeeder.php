<?php

namespace Database\Seeders;

use App\Models\Klasifikasi;
use Illuminate\Database\Seeder;

class KlasifikasiSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/jra.csv');

        if (! file_exists($path)) {
            $this->command->error("File tidak ditemukan: {$path}");
            return;
        }

        $handle = fopen($path, 'r');

        // Baca baris pertama (header) untuk deteksi pemisah kolom
        $firstLine = fgets($handle);
        $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';
        rewind($handle);

        $header = fgetcsv($handle, 0, $delimiter);

        // Bersihkan BOM dan spasi pada nama kolom
        $header = array_map(function ($h) {
            return strtolower(trim(str_replace("\xEF\xBB\xBF", '', $h)));
        }, $header);

        $masuk = 0;
        $lewat = 0;

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (count($row) < count($header)) continue;

            $data = array_combine($header, array_slice($row, 0, count($header)));

            $kode = trim($data[$this->cari($header, 'kode')] ?? '');
            $uraian = trim($data[$this->cari($header, 'uraian')] ?? '');

            // Lewati baris kosong atau baris judul kelompok tanpa kode
            if ($kode === '' || $uraian === '') {
                $lewat++;
                continue;
            }

            Klasifikasi::updateOrCreate(
                ['kode_klasifikasi' => $kode],
                [
                    'uraian'          => $uraian,
                    'retensi_aktif'   => $this->angka($data[$this->cari($header, 'aktif')] ?? 0),
                    'retensi_inaktif' => $this->angka($data[$this->cari($header, 'inaktif')] ?? 0),
                    'nasib_akhir'     => trim($data[$this->cari($header, 'nasib')] ?? '-') ?: '-',
                ]
            );

            $masuk++;
        }

        fclose($handle);

        $this->command->info("Berhasil: {$masuk} kode klasifikasi. Dilewati: {$lewat} baris.");
    }

    // Cari nama kolom yang mengandung kata kunci tertentu
    private function cari(array $header, string $kunci): ?string
    {
        foreach ($header as $kolom) {
            if (str_contains($kolom, $kunci)) return $kolom;
        }
        return null;
    }

    // Ambil angka dari teks seperti "2", "2 th", "2 tahun"
    private function angka($nilai): int
    {
        return (int) filter_var($nilai, FILTER_SANITIZE_NUMBER_INT);
    }
}