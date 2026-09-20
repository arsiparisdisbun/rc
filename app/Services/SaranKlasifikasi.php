<?php

namespace App\Services;

use App\Models\Arsip;
use App\Models\Klasifikasi;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class SaranKlasifikasi
{
    private const ABAIKAN = [
        'yang','di','ke','dari','dan','atau','dengan','untuk','pada','adalah',
        'ini','itu','akan','telah','sudah','dapat','oleh','dalam','sebagai',
        'atas','agar','bahwa','tidak','ada','juga','saat','karena','sesuai',
        'para','yaitu','maka','serta','apabila','bagi','tersebut','tanpa',
        'nomor','sifat','lampiran','perihal','hal','kepada','yth','terhormat',
        'tempat','hormat','sehubungan','diatas','demikian','disampaikan',
        'perhatian','kerjasamanya','diucapkan','terima','kasih','kami','saya',
        'provinsi','jawa','timur','dinas','kepala','bidang','sekretariat',
        'surat','nota','memo','tanggal','tahun','bulan','hari',
        'menindaklanjuti','sebagaimana','dimaksud','berikut','antara','lain',
        'kegiatan','pelaksanaan','penyusunan','pelaporan','pengelolaan',
        'koordinasi','monitoring','evaluasi','fasilitasi','bimbingan','teknis',
    ];

    public function ekstrakTeksPdf(string $pathRelatif): ?string
    {
        $pathAsli = Storage::disk('public')->path($pathRelatif);

        if (! file_exists($pathAsli)) {
            return null;
        }

        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($pathAsli);
            return $pdf->getText();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Membaca kode klasifikasi dari bagian depan nomor surat, sebelum
     * tanda "/" pertama. Nomor surat sering hanya mencantumkan kode
     * kelompok (mis. "500.3.2"), bukan kode akhir yang sungguhan dipakai
     * mengklasifikasi arsip (mis. "500.3.2.1"). Bila cocok persis tidak
     * ditemukan, dicari semua turunannya.
     */
    public function ambilKodeAcuan(string $nomorSurat): array
    {
        $prefiks = trim(explode('/', $nomorSurat)[0] ?? '');

        if ($prefiks === '') {
            return ['pasti' => null, 'pilihan' => collect(), 'prefiks' => null];
        }

        $pasti = Klasifikasi::where('kode_klasifikasi', $prefiks)->first();

        if ($pasti) {
            return ['pasti' => $pasti, 'pilihan' => collect([$pasti]), 'prefiks' => $prefiks];
        }

        $turunan = Klasifikasi::where('kode_klasifikasi', 'like', "{$prefiks}.%")
            ->orderBy('kode_klasifikasi')->get();

        return [
            'pasti'   => $turunan->count() === 1 ? $turunan->first() : null,
            'pilihan' => $turunan,
            'prefiks' => $turunan->isNotEmpty() ? $prefiks : null,
        ];
    }

    private function bersihkanTeks(string $teks): array
    {
        $teks = mb_strtolower($teks);
        $teks = preg_replace('/[^a-z\s]/', ' ', $teks);
        $kata = preg_split('/\s+/', $teks, -1, PREG_SPLIT_NO_EMPTY);

        $kata = array_filter($kata, fn ($k) => mb_strlen($k) >= 4 && ! in_array($k, self::ABAIKAN, true));

        return array_unique($kata);
    }

    /**
     * Mencari kode klasifikasi yang paling cocok dengan sebuah teks.
     *
     * $prefiksRumpun (opsional): bila diisi, pencarian dibatasi HANYA ke
     * kode yang diawali prefiks itu — bukan sekadar bonus, karena bonus
     * kecil selalu kalah bersaing melawan kode lain yang kebetulan
     * uraiannya panjang. Kelangkaan kata tetap dihitung dari seluruh JRA,
     * supaya bobotnya bermakna meski pencarian dipersempit.
     *
     * Skor dinormalkan terhadap panjang uraian tiap kode, supaya kode
     * berkalimat panjang tidak unggul semata karena lebih banyak peluang
     * kebetulan cocok dibanding kode singkat yang sebenarnya lebih pas.
     */
    public function cariSaran(string $teks, int $batas = 5, ?string $prefiksRumpun = null): array
    {
        $kataTeks = $this->bersihkanTeks($teks);

        if (empty($kataTeks)) {
            return [];
        }

        $semuaKlasifikasi = Klasifikasi::all(['kode_klasifikasi', 'uraian']);
        $dokumenFrekuensi = [];
        $kataPerKlasifikasi = [];

        foreach ($semuaKlasifikasi as $k) {
            $kataUraian = $this->bersihkanTeks($k->uraian);
            $kataPerKlasifikasi[$k->kode_klasifikasi] = $kataUraian;

            foreach ($kataUraian as $kata) {
                $dokumenFrekuensi[$kata] = ($dokumenFrekuensi[$kata] ?? 0) + 1;
            }
        }

        $totalKlasifikasi = $semuaKlasifikasi->count();

        $kandidatDinilai = $prefiksRumpun
            ? collect($kataPerKlasifikasi)->filter(fn ($_, $kode) => str_starts_with($kode, $prefiksRumpun))
            : collect($kataPerKlasifikasi);

        $skor = [];

        foreach ($kandidatDinilai as $kode => $kataUraian) {
            $cocok = array_intersect($kataTeks, $kataUraian);
            if (empty($cocok)) continue;

            $nilai = 0;
            foreach ($cocok as $kata) {
                $nilai += log($totalKlasifikasi / $dokumenFrekuensi[$kata]);
            }

            // Dibagi akar jumlah kata uraian, agar uraian panjang tidak
            // unggul hanya karena lebih banyak kesempatan cocok
            $skor[$kode] = $nilai / sqrt(count($kataUraian));
        }

        arsort($skor);
        $terpilih = array_slice($skor, 0, $batas, true);

        return collect($terpilih)->map(function ($nilai, $kode) use ($semuaKlasifikasi) {
            $k = $semuaKlasifikasi->firstWhere('kode_klasifikasi', $kode);
            return ['kode' => $kode, 'uraian' => $k->uraian, 'skor' => round($nilai, 3)];
        })->values()->all();
    }

    // Menggabungkan acuan nomor surat dan pencocokan isi PDF untuk satu arsip
    public function saranUntukArsip(Arsip $arsip): array
    {
        $acuan = $this->ambilKodeAcuan($arsip->nomor_surat ?? '');
        $teks = $arsip->dokumen_path ? $this->ekstrakTeksPdf($arsip->dokumen_path) : null;

        $kandidat = $teks ? $this->cariSaran($teks, 5, $acuan['prefiks']) : [];

        return [
            'kode_pasti' => $acuan['pasti'] ? [
                'kode'   => $acuan['pasti']->kode_klasifikasi,
                'uraian' => $acuan['pasti']->uraian,
            ] : null,
            'pilihan_acuan' => $acuan['pilihan']->map(fn ($k) => [
                'kode' => $k->kode_klasifikasi, 'uraian' => $k->uraian,
            ])->values()->all(),
            'kandidat' => $kandidat,
        ];
    }
}