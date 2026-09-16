<?php

namespace Database\Seeders;

use App\Models\UnitPengolah;
use Illuminate\Database\Seeder;

class UnitPengolahSeeder extends Seeder
{
    public function run(): void
    {
        $unit = [
            ['121.1',   'Sekretariat',                       1],
            ['121.2',   'Bidang Produk Tanaman Semusim',     2],
            ['121.3',   'Bidang Produk Tanaman Tahunan',     3],
            ['121.4',   'Bidang PPH',                        4],
            ['121.5',   'Bidang Perlindungan',               5],
            ['121.6.1', 'UPT PPBTP',                         6],
            ['121.6.2', 'UPT PSBP',                          7],
        ];

        foreach ($unit as [$kode, $nama, $urutan]) {
            UnitPengolah::updateOrCreate(
                ['kode' => $kode],
                ['nama' => $nama, 'urutan' => $urutan, 'aktif' => true]
            );
        }

        $this->command->info('7 unit pengolah tersimpan.');
    }
}