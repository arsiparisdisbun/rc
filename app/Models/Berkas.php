<?php

namespace App\Models;

use App\Traits\DicatatJejak;
use Illuminate\Database\Eloquent\Model;

class Berkas extends Model
{
    use DicatatJejak;
    protected $table = 'berkas';

    protected $fillable = [
        'unit_pengolah', 'sub_bagian', 'tahun', 'no_berkas',
        'kode_klasifikasi', 'uraian',
        'tahun_mulai', 'tahun_selesai',
        'jumlah_fisik', 'satuan', 'skkad',
        'boks_id', 'lokasi_simpan',
        'status', 'diajukan_pada', 'diverifikasi_pada', 'diverifikasi_oleh',
        'catatan_verifikasi', 'dipindahkan_pada', 'keterangan','pemindahan_id',
        'penyusutan_id', 'disusutkan_pada', 'pegawai_id', 'kategori_keuangan',
    ];

    protected $casts = [
        'diajukan_pada'     => 'datetime',
        'diverifikasi_pada' => 'datetime',
        'dipindahkan_pada'  => 'date',
        'disusutkan_pada' => 'date',
    ];

    public const SUB_BAGIAN = ['Umum dan Kepegawaian', 'Sungram', 'Keuangan'];
    public const KATEGORI_KEUANGAN = ['SPJ Ganti Uang', 'Belanja LS', 'Akuntansi', 'PAD'];

    // ---------- Relasi ----------

    public function unit()
    {
        return $this->belongsTo(UnitPengolah::class, 'unit_pengolah', 'kode');
    }

    public function klasifikasi()
    {
        return $this->belongsTo(Klasifikasi::class, 'kode_klasifikasi', 'kode_klasifikasi');
    }

    public function boks()
    {
        return $this->belongsTo(Boks::class, 'boks_id');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function item()
    {
        return $this->hasMany(ItemBerkas::class, 'berkas_id')->orderBy('nomor_item');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    // ---------- Penomoran ----------

    // Nomor berkas melanjutkan urutan terakhir pada tahun yang diinput,
    // bukan tahun server, agar berkas backlog tetap masuk tahunnya sendiri.
    public static function nomorBerikutnya(string $unit, int $tahun): int
    {
        return static::where('unit_pengolah', $unit)->where('tahun', $tahun)->max('no_berkas') + 1;
    }

    public function getLabelAttribute(): string
    {
        return "{$this->no_berkas}/{$this->tahun}";
    }

    // ---------- Kurun waktu & retensi ----------

    public function getKurunWaktuAttribute(): string
    {
        if (! $this->tahun_mulai) return '-';

        return $this->tahun_selesai && $this->tahun_selesai !== $this->tahun_mulai
            ? "{$this->tahun_mulai}–{$this->tahun_selesai}"
            : (string) $this->tahun_mulai;
    }

    public function getTahunAkhirAttribute(): ?int
    {
        return $this->tahun_selesai ?: $this->tahun_mulai;
    }

    // Retensi dihitung mulai 1 Januari setelah tahun akhir kurun waktu
    public function getAwalRetensiAttribute(): ?int
    {
        return $this->tahun_akhir ? $this->tahun_akhir + 1 : null;
    }

    public function getJatuhTempoInaktifAttribute(): ?int
    {
        if (! $this->klasifikasi || ! $this->awal_retensi) return null;
        return $this->awal_retensi + $this->klasifikasi->retensi_aktif;
    }

    public function getJatuhTempoAkhirAttribute(): ?int
    {
        if (! $this->klasifikasi || ! $this->awal_retensi) return null;
        return $this->jatuh_tempo_inaktif + $this->klasifikasi->retensi_inaktif;
    }

    public function getStatusPenyimpananAttribute(): string
    {
        if ($this->sudahDisusutkan()) {
            return $this->penyusutan?->musnah() ? 'Dimusnahkan' : 'Diserahkan';
        }

        if (! $this->klasifikasi || ! $this->awal_retensi) return 'Tidak diketahui';

        $tahunIni = now()->year;

        if ($tahunIni < $this->awal_retensi)         return 'Kerja';
        if ($tahunIni >= $this->jatuh_tempo_akhir)   return 'Siap ' . strtolower($this->klasifikasi->nasib_akhir);
        if ($tahunIni >= $this->jatuh_tempo_inaktif) return 'Inaktif';

        return 'Aktif';
    }

    // ---------- Alur verifikasi ----------

    public function draf(): bool
    {
        return $this->status === 'draf';
    }

    public function terverifikasi(): bool
    {
        return $this->status === 'terverifikasi';
    }

    // Operator hanya boleh mengubah selama berkas belum terkunci
    public function bolehDiubahOleh(User $user): bool
    {
        if ($user->lihatSemuaUnit()) return true;

        return $this->unit_pengolah === $user->unit_pengolah && ! $this->terverifikasi();
    }

    public function getLabelStatusAttribute(): string
    {
        return match ($this->status) {
            'draf'          => 'Draf',
            'diajukan'      => 'Menunggu Verifikasi',
            'terverifikasi' => 'Terverifikasi',
            default         => $this->status,
        };
    }

        public function pemindahan()
    {
        return $this->belongsTo(Pemindahan::class, 'pemindahan_id');
    }

        public function penyusutan()
    {
        return $this->belongsTo(Penyusutan::class, 'penyusutan_id');
    }

    public function sudahDisusutkan(): bool
    {
        return ! is_null($this->penyusutan_id);
    }
}