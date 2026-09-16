<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Boks extends Model
{
    protected $table = 'boks';

    protected $fillable = ['unit_pengolah', 'jenis', 'nomor', 'lokasi', 'keterangan', 'terpakai'];

    protected $casts = ['terpakai' => 'boolean'];

    public function unit()
    {
        return $this->belongsTo(UnitPengolah::class, 'unit_pengolah', 'kode');
    }

    public function berkas()
    {
        return $this->hasMany(Berkas::class, 'boks_id');
    }

    public static function nomorBerikutnya(string $unit, string $jenis): int
    {
        return static::where('unit_pengolah', $unit)->where('jenis', $jenis)->max('nomor') + 1;
    }

    public function getLabelAttribute(): string
    {
        return 'Boks ' . ucfirst($this->jenis) . ' ' . $this->nomor;
    }
}