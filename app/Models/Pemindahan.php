<?php

namespace App\Models;

use App\Traits\DicatatJejak;
use Illuminate\Database\Eloquent\Model;

class Pemindahan extends Model
{
    use DicatatJejak;
    protected $table = 'pemindahan';

    protected $fillable = [
        'unit_pengolah', 'nomor_ba', 'tanggal_ba',
        'pihak1_nama', 'pihak1_nip', 'pihak1_pangkat', 'pihak1_jabatan',
        'pihak2_nama', 'pihak2_nip', 'pihak2_pangkat', 'pihak2_jabatan',
        'status', 'diajukan_pada', 'diproses_pada',
        'diajukan_oleh', 'diproses_oleh', 'catatan',
    ];

    protected $casts = [
        'tanggal_ba'    => 'date',
        'diajukan_pada' => 'datetime',
        'diproses_pada' => 'datetime',
    ];

    public function unit()
    {
        return $this->belongsTo(UnitPengolah::class, 'unit_pengolah', 'kode');
    }

    public function berkas()
    {
        return $this->hasMany(Berkas::class, 'pemindahan_id')->orderBy('no_berkas');
    }

    public function pengaju()
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }

    public function pemroses()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    public function diajukan(): bool
    {
        return $this->status === 'diajukan';
    }

    public function diterima(): bool
    {
        return $this->status === 'diterima';
    }

    // Jumlah boks yang dipakai, untuk kalimat berita acara
    public function getJumlahBoksAttribute(): int
    {
        return $this->berkas->pluck('boks_id')->filter()->unique()->count();
    }

    public function getJumlahBerkasAttribute(): int
    {
        return $this->berkas->count();
    }

    public function getLabelStatusAttribute(): string
    {
        return match ($this->status) {
            'diajukan' => 'Menunggu Penerimaan',
            'diterima' => 'Diterima',
            'ditolak'  => 'Ditolak',
        };
    }
}