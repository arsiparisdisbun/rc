<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Arsip extends Model
{
    protected $table = 'arsip';

        protected $fillable = [
        'jenis', 'unit_pengolah', 'no_urut', 'tahun', 'no_tnde',
        'nomor_surat', 'tanggal_surat', 'tanggal_penerimaan',
        'sifat', 'lampiran', 'isi_ringkas', 'dari', 'kepada',
        'tingkat_perkembangan', 'kode_klasifikasi',
        'lokasi_simpan', 'dokumen_path','jenis_naskah_id', 'tanggal_upload', 'tanggal_verifikasi',
        'jumlah_lembar', 'pembuat', 'kode_tnde',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
        'tanggal_penerimaan' => 'date',
        'tanggal_upload' => 'date',
        'tanggal_verifikasi' => 'date',
    ];

    public function klasifikasi()
    {
        return $this->belongsTo(Klasifikasi::class, 'kode_klasifikasi', 'kode_klasifikasi');
    }
    

    // Relasi ke Unit Pengolah
    public function unit()
    {
        return $this->belongsTo(UnitPengolah::class, 'unit_pengolah', 'kode');
    }
        public function jenisNaskah()
    {
        return $this->belongsTo(JenisNaskah::class, 'jenis_naskah_id');
    }

    // Perhitungan JRA dimulai 1 Januari tahun berikutnya setelah tahun surat.
    // Selama masih di tahun suratnya, arsip berstatus "Kerja" dan belum dihitung.
    public function getAwalRetensiAttribute()
    {
        if (! $this->tanggal_surat) return null;
        return $this->tanggal_surat->copy()->startOfYear()->addYear();
    }

    // Tanggal arsip berpindah ke inaktif
    public function getJatuhTempoInaktifAttribute()
    {
        if (! $this->klasifikasi || ! $this->awal_retensi) return null;
        return $this->awal_retensi->copy()->addYears($this->klasifikasi->retensi_aktif);
    }

    // Tanggal arsip masuk tahap nasib akhir (musnah / permanen)
    public function getJatuhTempoAkhirAttribute()
    {
        if (! $this->klasifikasi || ! $this->awal_retensi) return null;
        return $this->awal_retensi->copy()
            ->addYears($this->klasifikasi->retensi_aktif + $this->klasifikasi->retensi_inaktif);
    }

    public function getStatusRetensiAttribute(): string
    {
        if (! $this->klasifikasi) return 'Tidak diketahui';

        $now = now();

        if ($this->awal_retensi && $now->lt($this->awal_retensi)) {
            return 'Kerja';
        }

        if ($now->gte($this->jatuh_tempo_akhir)) {
            return 'Siap ' . strtolower($this->klasifikasi->nasib_akhir);
        }

        if ($now->gte($this->jatuh_tempo_inaktif)) {
            return 'Siap inaktif';
        }

        return 'Aktif';
    }
        /**
     * Nomor agenda surat keluar mengikuti pola TNDE:
     * hari ke-berapa dalam setahun (dari tanggal surat) x 1000 + urutan pada hari itu.
     * Contoh: surat 3 Agustus 2026 (hari ke-215) -> 215001, 215002, dst.
     */
    public static function nomorKeluarBerikutnya(string $unit, $tanggalSurat): int
    {
        $tgl   = \Carbon\Carbon::parse($tanggalSurat);
        $hari  = $tgl->dayOfYear;
        $tahun = $tgl->year;
        $awal  = $hari * 1000;

        $tertinggi = static::where('jenis', 'keluar')
            ->where('unit_pengolah', $unit)
            ->where('tahun', $tahun)
            ->whereBetween('no_urut', [$awal + 1, $awal + 999])
            ->max('no_urut');

        return $tertinggi ? $tertinggi + 1 : $awal + 1;
    }
}