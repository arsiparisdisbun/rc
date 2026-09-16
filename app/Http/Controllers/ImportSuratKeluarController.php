<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use App\Models\JenisNaskah;
use App\Models\Klasifikasi;
use App\Models\UnitPengolah;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportSuratKeluarController extends Controller
{
    public function form()
    {
        return view('surat_keluar.import');
    }

    public function proses(Request $request)
    {
        $request->validate([
            'berkas'   => ['required', 'file', 'max:51200'],
            'simulasi' => ['nullable', 'boolean'],
        ]);

        $simulasi = $request->boolean('simulasi');
        $path = $request->file('berkas')->getRealPath();

        $isi = file_get_contents($path);
        $pemisah = substr_count($isi, ';') > substr_count($isi, ',') ? ';' : ',';

        $handle = fopen($path, 'r');

        // Cari baris header yang memuat kolom NOMOR_SURAT
        $header = null;
        while (($baris = fgetcsv($handle, 0, $pemisah)) !== false) {
            $bersih = array_map(fn ($x) => strtolower(trim((string) $x)), $baris);
            if (in_array('nomor_surat', $bersih, true)) {
                $header = $bersih;
                break;
            }
        }

        if (! $header) {
            fclose($handle);
            return back()->withErrors(['berkas' => 'Baris header dengan kolom NOMOR_SURAT tidak ditemukan.']);
        }

        $kodeJra   = Klasifikasi::pluck('kode_klasifikasi')->flip();
        $unitAda   = UnitPengolah::pluck('kode')->flip();
        $jenisPeta = JenisNaskah::pluck('id', 'nama')
            ->mapWithKeys(fn ($id, $nama) => [strtolower($nama) => $id]);

        $stat = [
            'masuk' => 0, 'tanpa_verif' => 0, 'tanpa_nomor' => 0,
            'sudah_ada' => 0, 'unit_asing' => 0, 'jenis_asing' => 0, 'kode_asing' => 0,
        ];
        $catatan = [];

        DB::beginTransaction();

        try {
            while (($baris = fgetcsv($handle, 0, $pemisah)) !== false) {
                $ambil = function (string $kunci) use ($baris, $header) {
                    $i = array_search($kunci, $header, true);
                    return $i === false ? '' : trim((string) ($baris[$i] ?? ''));
                };

                if ($ambil('nomor_surat') === '') {
                    continue; // baris kosong
                }

                // Aturan 1: hanya yang sudah terverifikasi
                $tglVerif = $this->parseTanggal($ambil('tanggal_verif'));
                if (! $tglVerif) {
                    $stat['tanpa_verif']++;
                    continue;
                }

                // Aturan 2: harus punya nomor agenda
                $nomor = preg_replace('/[^0-9]/', '', $ambil('nomor'));
                if ($nomor === '') {
                    $stat['tanpa_nomor']++;
                    continue;
                }
                $nomor = (int) $nomor;

                $tglSurat = $this->parseTanggal($ambil('tanggal surat'));
                if (! $tglSurat) {
                    $catatan[] = "Nomor {$nomor}: tanggal surat tidak terbaca, dilewati.";
                    continue;
                }
                $tahun = $tglSurat->year;

                $unit = $this->normalkanUnit($ambil('unit kerja'));
                if (! $unitAda->has($unit)) {
                    $stat['unit_asing']++;
                    $catatan[] = "Nomor {$nomor}: unit \"{$unit}\" tidak terdaftar, dilewati.";
                    continue;
                }

                if (Arsip::where('jenis', 'keluar')->where('unit_pengolah', $unit)
                        ->where('tahun', $tahun)->where('no_urut', $nomor)->exists()) {
                    $stat['sudah_ada']++;
                    continue;
                }

                // Jenis naskah dicocokkan tanpa membedakan huruf besar/kecil
                $namaJenis = strtolower($ambil('jenis'));
                $jenisId = $jenisPeta[$namaJenis] ?? null;
                if ($namaJenis !== '' && ! $jenisId) {
                    $stat['jenis_asing']++;
                    $catatan[] = "Nomor {$nomor}: jenis naskah \"{$ambil('jenis')}\" tidak dikenal.";
                }

                // Kode TNDE disimpan apa adanya; koreksi dipakai menghitung retensi
                $kodeTnde = $this->normalkanKode($ambil('kode klasifikasi'));
                $koreksi  = $this->normalkanKode($ambil('koreksi'));
                $kodePakai = $koreksi ?: $kodeTnde;

                if ($kodePakai && ! $kodeJra->has($kodePakai)) {
                    $stat['kode_asing']++;
                    $catatan[] = "Nomor {$nomor}: kode \"{$kodePakai}\" tidak ada di JRA, dikosongkan.";
                    $kodePakai = null;
                }

                $lembar = preg_replace('/[^0-9]/', '', $ambil('jumlah lembar'));

                Arsip::create([
                    'jenis'                => 'keluar',
                    'unit_pengolah'        => $unit,
                    'jenis_naskah_id'      => $jenisId,
                    'no_urut'              => $nomor,
                    'tahun'                => $tahun,
                    'nomor_surat'          => $ambil('nomor_surat'),
                    'tanggal_surat'        => $tglSurat,
                    'tanggal_upload'       => $this->parseTanggal($ambil('tanggal_upload')),
                    'tanggal_verifikasi'   => $tglVerif,
                    'sifat'                => $this->normalkanSifat($ambil('sifat')),
                    'jumlah_lembar'        => $lembar !== '' ? (int) $lembar : null,
                    'isi_ringkas'          => $ambil('isi ringkas') ?: '(tidak ada uraian)',
                    'kepada'               => $ambil('kepada') ?: null,
                    'pembuat'              => $ambil('pembuat') ?: null,
                    'tingkat_perkembangan' => $this->normalkanTingkat($ambil('ket')),
                    'kode_tnde'            => $kodeTnde ?: null,
                    'kode_klasifikasi'     => $kodePakai,
                ]);

                $stat['masuk']++;
            }

            fclose($handle);

            $simulasi ? DB::rollBack() : DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);
            return back()->withErrors(['berkas' => 'Gagal: ' . $e->getMessage()]);
        }

        return back()->with([
            'stat'     => $stat,
            'catatan'  => array_slice($catatan, 0, 100),
            'simulasi' => $simulasi,
        ]);
    }

    private function parseTanggal($nilai): ?Carbon
    {
        $nilai = trim((string) $nilai);
        if ($nilai === '' || $nilai === '-') return null;

        // Buang bagian jam bila ada: "2026-08-20 14:09:26"
        $nilai = explode(' ', $nilai)[0];

        foreach (['d-m-Y', 'd/m/Y', 'Y-m-d'] as $format) {
            try {
                $t = Carbon::createFromFormat($format, $nilai);
                if ($t && $t->format($format) === $nilai) return $t->startOfDay();
            } catch (\Throwable $e) {
            }
        }

        try {
            return Carbon::parse($nilai)->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }
    }

    // Excel kerap membaca 121.1 sebagai angka, sehingga muncul 121.10 atau 121.1000001
    private function normalkanUnit($nilai): string
    {
        $v = trim((string) $nilai);
        if ($v === '') return '';

        if (is_numeric($v) && substr_count($v, '.') === 1) {
            $v = rtrim(rtrim(number_format((float) $v, 4, '.', ''), '0'), '.');
        }

        return $v;
    }

    private function normalkanKode($nilai): string
    {
        $v = trim((string) $nilai);
        if ($v === '' || $v === '-') return '';

        if (is_numeric($v) && substr_count($v, '.') === 1) {
            $v = rtrim(rtrim(number_format((float) $v, 6, '.', ''), '0'), '.');
        }

        return $v;
    }

    private function normalkanSifat(?string $nilai): string
    {
        $v = strtolower(trim((string) $nilai));

        if (str_contains($v, 'sangat rahasia')) return 'Sangat Rahasia';
        if (str_contains($v, 'rahasia'))        return 'Rahasia';
        if (str_contains($v, 'terbatas'))       return 'Terbatas';

        return 'Biasa/Terbuka';
    }

    // "Copy" menjadi Salinan; "-" dibiarkan kosong agar tidak menebak
    private function normalkanTingkat(?string $nilai): ?string
    {
        $v = strtolower(trim((string) $nilai));

        if (str_contains($v, 'asli'))    return 'Asli';
        if (str_contains($v, 'salinan')) return 'Salinan';
        if (str_contains($v, 'copy'))    return 'Salinan';

        return null;
    }
        public function formDokumen()
    {
        return view('surat_keluar.import_dokumen');
    }

    public function prosesDokumen(Request $request)
    {
        $request->validate([
            'berkas'   => ['required', 'array', 'min:1'],
            'berkas.*' => ['file', 'max:20480'],
        ]);

        // Peta kode unit tanpa titik: "1211" => "121.1", "12161" => "121.6.1"
        $petaUnit = UnitPengolah::pluck('kode')
            ->mapWithKeys(fn ($kode) => [preg_replace('/\D/', '', $kode) => $kode]);

        $stat = ['cocok' => 0, 'tak_cocok' => 0, 'pola_gagal' => 0, 'bukan_pdf' => 0, 'ditimpa' => 0];
        $catatan = [];

        foreach ($request->file('berkas') as $file) {
            $namaAsli = $file->getClientOriginalName();

            $handle = fopen($file->getRealPath(), 'rb');
            $awal = fread($handle, 1024);
            fclose($handle);

            if (! str_contains($awal, '%PDF-')) {
                $stat['bukan_pdf']++;
                $catatan[] = "{$namaAsli}: bukan berkas PDF.";
                continue;
            }

            // Pola nama: Jenis-xxxxx-NOMOR-UNIT-TAHUN-tanggal-timestamp
            // Unit dikenali dari kode tanpa titik, lalu nomor diambil dari
            // ruas sebelumnya dan tahun dari ruas sesudahnya.
            $ruas = explode('-', pathinfo($namaAsli, PATHINFO_FILENAME));

            $unit = null; $nomor = null; $tahun = null;

            foreach ($ruas as $i => $r) {
                $r = trim($r);
                if ($petaUnit->has($r) && $i > 0 && isset($ruas[$i + 1])) {
                    $unit  = $petaUnit[$r];
                    $nomor = preg_replace('/\D/', '', $ruas[$i - 1]);
                    $tahun = preg_replace('/\D/', '', $ruas[$i + 1]);
                    break;
                }
            }

            if (! $unit || $nomor === '' || strlen($tahun) !== 4) {
                $stat['pola_gagal']++;
                $catatan[] = "{$namaAsli}: pola nama berkas tidak dikenali.";
                continue;
            }

            $arsip = Arsip::where('jenis', 'keluar')
                ->where('unit_pengolah', $unit)
                ->where('tahun', (int) $tahun)
                ->where('no_urut', (int) $nomor)
                ->first();

            if (! $arsip) {
                $stat['tak_cocok']++;
                $catatan[] = "{$namaAsli}: tidak ada arsip nomor {$nomor} unit {$unit} tahun {$tahun}.";
                continue;
            }

            if ($arsip->dokumen_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($arsip->dokumen_path);
                $stat['ditimpa']++;
            }

            $namaSimpan = 'SK_' . $tahun . '_' . str_replace('.', '', $unit) . '_' . $nomor
                        . '_' . now()->format('His') . '.pdf';

            $arsip->update([
                'dokumen_path' => $file->storeAs('dokumen_arsip', $namaSimpan, 'public'),
            ]);

            $stat['cocok']++;
        }

        return back()->with([
            'stat_dok' => $stat,
            'catatan'  => array_slice($catatan, 0, 100),
        ]);
    }
}