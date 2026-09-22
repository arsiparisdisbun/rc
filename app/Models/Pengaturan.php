<?php

namespace App\Models;

use App\Traits\DicatatJejak;
use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    use DicatatJejak;

    protected $table = 'pengaturan';

    protected $fillable = ['kelompok', 'nilai', 'urutan'];

    public const KELOMPOK = [
        'sub_bagian' => [
            'judul'      => 'Sub Bagian Sekretariat',
            'keterangan' => 'Pilihan sub bagian pada berkas milik unit 121.1 (Sekretariat).',
            'contoh'     => 'Umum dan Kepegawaian',
        ],
        'kategori_keuangan' => [
            'judul'      => 'Kategori Keuangan',
            'keterangan' => 'Pengelompokan berkas keuangan, dipakai sebagai penyaring di menu Keuangan.',
            'contoh'     => 'SPJ Ganti Uang',
        ],
        'satuan' => [
            'judul'      => 'Satuan Berkas',
            'keterangan' => 'Satuan jumlah fisik arsip pada berkas.',
            'contoh'     => 'Berkas',
        ],
        'skkad' => [
            'judul'      => 'SKKAD',
            'keterangan' => 'Klasifikasi Keamanan dan Akses Arsip, mengikuti istilah baku kearsipan.',
            'contoh'     => 'Biasa/Terbuka',
        ],
    ];

    /**
     * Daftar nilai satu kelompok, siap dipakai di form.
     * Diambil sekali per permintaan lalu disimpan sementara, agar form yang
     * memanggil beberapa kelompok sekaligus tidak menembak database berulang.
     */
    public static function daftar(string $kelompok): array
    {
        static $singgahan = [];

        if (! isset($singgahan[$kelompok])) {
            $singgahan[$kelompok] = static::where('kelompok', $kelompok)
                ->orderBy('urutan')->orderBy('nilai')
                ->pluck('nilai')->all();
        }

        return $singgahan[$kelompok];
    }

    public function getLabelKelompokAttribute(): string
    {
        return self::KELOMPOK[$this->kelompok]['judul'] ?? $this->kelompok;
    }
}