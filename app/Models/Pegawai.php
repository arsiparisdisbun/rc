<?php

namespace App\Models;

use App\Traits\DicatatJejak;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use DicatatJejak;

    protected $table = 'pegawai';

    protected $fillable = [
        'nip', 'nama', 'jabatan', 'unit_pengolah', 'status', 'tanggal_status', 'keterangan',
    ];

    protected $casts = [
        'tanggal_status' => 'date',
    ];

    public const STATUS = [
        'aktif'     => 'Aktif',
        'pensiun'   => 'Pensiun',
        'pindah'    => 'Pindah',
        'berhenti'  => 'Berhenti',
        'meninggal' => 'Meninggal',
    ];

    public function unit()
    {
        return $this->belongsTo(UnitPengolah::class, 'unit_pengolah', 'kode');
    }

    public function berkas()
    {
        return $this->hasMany(Berkas::class, 'pegawai_id');
    }

    public function getLabelStatusAttribute(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function aktif(): bool
    {
        return $this->status === 'aktif';
    }
}