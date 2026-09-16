<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisNaskah extends Model
{
    protected $table = 'jenis_naskah';

    protected $fillable = ['nama', 'kelompok', 'sub_kelompok', 'urutan', 'aktif'];

    protected $casts = ['aktif' => 'boolean'];

    public function scopeAktif($query)
    {
        return $query->where('aktif', true)->orderBy('urutan');
    }
}