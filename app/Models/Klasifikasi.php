<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Klasifikasi extends Model
{
    protected $table = 'klasifikasi';
    protected $primaryKey = 'kode_klasifikasi';
    public $incrementing = false;   // primary key berupa teks, bukan angka
    protected $keyType = 'string';

    protected $fillable = [
        'kode_klasifikasi', 'uraian',
        'retensi_aktif', 'retensi_inaktif', 'nasib_akhir',
    ];

    public function arsip()
    {
        return $this->hasMany(Arsip::class, 'kode_klasifikasi', 'kode_klasifikasi');
    }
}