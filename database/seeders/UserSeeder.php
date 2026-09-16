<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $akun = [
            // [username, nama, peran, unit]
            ['superadmin', 'Administrator Sistem',        'superadmin', null],
            ['kearsipan',  'Unit Kearsipan',              'kearsipan',  null],
            ['sekretariat','Operator Sekretariat',        'operator',   '121.1'],
            ['semusim',    'Operator Tanaman Semusim',    'operator',   '121.2'],
            ['tahunan',    'Operator Tanaman Tahunan',    'operator',   '121.3'],
            ['pph',        'Operator Bidang PPH',         'operator',   '121.4'],
            ['perlindungan','Operator Bidang Perlindungan','operator',  '121.5'],
            ['ppbtp',      'Operator UPT PPBTP',          'operator',   '121.6.1'],
            ['psbp',       'Operator UPT PSBP',           'operator',   '121.6.2'],
        ];

        foreach ($akun as [$username, $nama, $peran, $unit]) {
            User::updateOrCreate(
                ['username' => $username],
                [
                    'name'          => $nama,
                    'password'      => 'siarsip2026',
                    'peran'         => $peran,
                    'unit_pengolah' => $unit,
                    'aktif'         => true,
                ]
            );
        }

        $this->command->info(count($akun) . ' akun dibuat. Kata sandi awal semuanya: siarsip2026');
        $this->command->warn('Segera ganti kata sandi setelah masuk pertama kali.');
    }
}