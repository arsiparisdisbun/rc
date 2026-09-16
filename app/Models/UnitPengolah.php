<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitPengolah extends Model
{
    protected $table = 'unit_pengolah';
    protected $primaryKey = 'kode';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['kode', 'nama', 'urutan', 'aktif'];

    protected $casts = ['aktif' => 'boolean'];

    public function scopeAktif($query)
    {
        return $query->where('aktif', true)->orderBy('urutan');
    }

    public function getLabelAttribute(): string
    {
        return "{$this->kode} — {$this->nama}";
    }
}