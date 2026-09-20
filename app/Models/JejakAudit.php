<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JejakAudit extends Model
{
    protected $table = 'jejak_audit';

    public $timestamps = false;

    protected $fillable = [
        'model', 'model_id', 'aksi', 'sebelum', 'sesudah',
        'user_id', 'nama_user', 'unit_pengolah', 'ip', 'created_at',
    ];

    protected $casts = [
        'sebelum'    => 'array',
        'sesudah'    => 'array',
        'created_at' => 'datetime',
    ];

    // Kolom yang tidak perlu dicatat karena bukan isi data
    public const ABAIKAN = ['created_at', 'updated_at', 'remember_token', 'password'];

    // Nama kolom dalam bahasa Indonesia untuk ditampilkan
    public const LABEL = [
        'no_urut'              => 'No Urut',
        'no_tnde'              => 'No TNDE',
        'nomor_surat'          => 'Nomor Surat',
        'tanggal_surat'        => 'Tanggal Surat',
        'tanggal_penerimaan'   => 'Tanggal Penerimaan',
        'tanggal_verifikasi'   => 'Tanggal Verifikasi',
        'tanggal_upload'       => 'Tanggal Upload',
        'isi_ringkas'          => 'Isi Ringkas',
        'dari'                 => 'Dari',
        'kepada'               => 'Kepada',
        'pembuat'              => 'Pembuat',
        'sifat'                => 'Sifat',
        'lampiran'             => 'Lampiran',
        'jumlah_lembar'        => 'Jumlah Lembar',
        'tingkat_perkembangan' => 'Tingkat Perkembangan',
        'kode_klasifikasi'     => 'Kode Klasifikasi',
        'kode_tnde'            => 'Kode TNDE',
        'unit_pengolah'        => 'Unit Pengolah',
        'jenis_naskah_id'      => 'Jenis Naskah',
        'lokasi_simpan'        => 'Lokasi Simpan',
        'dokumen_path'         => 'Dokumen',
        'uraian'               => 'Uraian',
        'tahun_mulai'          => 'Kurun Waktu Awal',
        'tahun_selesai'        => 'Kurun Waktu Akhir',
        'jumlah_fisik'         => 'Jumlah Fisik',
        'satuan'               => 'Satuan',
        'skkad'                => 'SKKAD',
        'boks_id'              => 'Boks',
        'status'               => 'Status',
        'catatan_verifikasi'   => 'Catatan Verifikasi',
        'sub_bagian'           => 'Sub Bagian',
        'pemindahan_id'        => 'Pemindahan',
        'penyusutan_id'        => 'Penyusutan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getLabelModelAttribute(): string
    {
        return match ($this->model) {
            'Arsip'       => 'Arsip Surat',
            'Berkas'      => 'Berkas',
            'ItemBerkas'  => 'Item Berkas',
            'Boks'        => 'Boks',
            'Pemindahan'  => 'Pemindahan',
            'Penyusutan'  => 'Penyusutan',
            'User'        => 'Akun',
            default       => $this->model,
        };
    }

    public function getLabelAksiAttribute(): string
    {
        return match ($this->aksi) {
            'tambah' => 'Ditambahkan',
            'ubah'   => 'Diubah',
            'hapus'  => 'Dihapus',
        };
    }

    public static function namaKolom(string $kolom): string
    {
        return self::LABEL[$kolom] ?? ucwords(str_replace('_', ' ', $kolom));
    }
}