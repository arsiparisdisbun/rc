<?php

namespace App\Models;

use App\Traits\DicatatJejak;
use Illuminate\Database\Eloquent\Model;

class ItemBerkas extends Model
{
    use DicatatJejak;
    protected $table = 'item_berkas';

    protected $fillable = [
        'berkas_id', 'nomor_item', 'arsip_id',
        'nomor_surat', 'uraian', 'tanggal', 'tahun',
        'jumlah', 'satuan', 'skkad', 'kode_klasifikasi', 'keterangan',
    ];

    protected $casts = ['tanggal' => 'date'];

    public function berkas()
    {
        return $this->belongsTo(Berkas::class, 'berkas_id');
    }

    public function arsip()
    {
        return $this->belongsTo(Arsip::class, 'arsip_id');
    }

    public function klasifikasi()
    {
        return $this->belongsTo(Klasifikasi::class, 'kode_klasifikasi', 'kode_klasifikasi');
    }

    public static function nomorBerikutnya(int $berkasId): int
    {
        return static::where('berkas_id', $berkasId)->max('nomor_item') + 1;
    }

    public function dariArsip(): bool
    {
        return ! is_null($this->arsip_id);
    }

    // Data ditampilkan dari arsip bila item ini menunjuk arsip,
    // agar perubahan di buku agenda ikut terbaca di daftar isi berkas.
    public function getNomorAttribute(): ?string
    {
        return $this->dariArsip() ? $this->arsip?->nomor_surat : $this->nomor_surat;
    }

    public function getIsiAttribute(): ?string
    {
        return $this->dariArsip() ? $this->arsip?->isi_ringkas : $this->uraian;
    }

    public function getTanggalTampilAttribute(): ?string
    {
        if ($this->dariArsip()) {
            return $this->arsip?->tanggal_surat?->format('d/m/Y');
        }

        if ($this->tanggal) return $this->tanggal->format('d/m/Y');

        return $this->tahun ? (string) $this->tahun : null;
    }

    public function getTahunItemAttribute(): ?int
    {
        if ($this->dariArsip()) return $this->arsip?->tanggal_surat?->year;

        return $this->tanggal?->year ?? $this->tahun;
    }

    public function getKodeAttribute(): ?string
    {
        return $this->dariArsip()
            ? ($this->arsip?->kode_klasifikasi ?? $this->kode_klasifikasi)
            : $this->kode_klasifikasi;
    }

    public function getSumberAttribute(): string
    {
        if (! $this->dariArsip()) return 'Manual';

        return match ($this->arsip?->jenis) {
            'masuk'  => 'Surat Masuk',
            'keluar' => 'Surat Keluar',
            default  => 'Arsip',
        };
    }
}