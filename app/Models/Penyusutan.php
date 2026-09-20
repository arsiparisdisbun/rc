<?php

namespace App\Models;

use App\Traits\DicatatJejak;
use Illuminate\Database\Eloquent\Model;

class Penyusutan extends Model
{
    use DicatatJejak;
    protected $table = 'penyusutan';

    protected $fillable = [
        'jenis', 'nomor_ba', 'tanggal_ba',
        'pihak1_nama', 'pihak1_nip', 'pihak1_pangkat', 'pihak1_jabatan',
        'pihak2_nama', 'pihak2_nip', 'pihak2_pangkat', 'pihak2_jabatan', 'pihak2_instansi',
        'saksi1_nama', 'saksi1_nip', 'saksi1_jabatan',
        'saksi2_nama', 'saksi2_nip', 'saksi2_jabatan',
        'tempat', 'cara', 'dicatat_oleh', 'catatan',
    ];

    protected $casts = ['tanggal_ba' => 'date'];

    public const CARA = ['Dibakar', 'Dicacah', 'Dilebur secara kimia', 'Lainnya'];

    public function berkas()
    {
        return $this->hasMany(Berkas::class, 'penyusutan_id')
                    ->orderBy('unit_pengolah')->orderBy('tahun')->orderBy('no_berkas');
    }

    public function pencatat()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    public function musnah(): bool
    {
        return $this->jenis === 'musnah';
    }

    public function getLabelJenisAttribute(): string
    {
        return $this->musnah() ? 'Pemusnahan' : 'Penyerahan Arsip Statis';
    }

    public function getJumlahBerkasAttribute(): int
    {
        return $this->berkas->count();
    }

    public function getJumlahBoksAttribute(): int
    {
        return $this->berkas->pluck('boks_id')->filter()->unique()->count();
    }
}