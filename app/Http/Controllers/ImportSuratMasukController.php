<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use App\Models\Klasifikasi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImportSuratMasukController extends Controller
{
    public function form()
    {
        return view('surat_masuk.import');
    }

    public function proses(Request $request)
    {
        $request->validate([
            'berkas'   => ['required', 'file', 'max:20480'],
            'simulasi' => ['nullable', 'boolean'],
        ]);

        $simulasi = $request->boolean('simulasi');
        $handle = fopen($request->file('berkas')->getRealPath(), 'r');

        // Deteksi pemisah kolom dari baris header
        $isi = file_get_contents($request->file('berkas')->getRealPath());
        $pemisah = substr_count($isi, ';') > substr_count($isi, ',') ? ';' : ',';

        // Lewati baris judul sampai ketemu header yang memuat "no urut"
        $header = null;
        while (($baris = fgetcsv($handle, 0, $pemisah)) !== false) {
            $bersih = array_map(fn ($x) => strtolower(trim((string) $x)), $baris);
            if (in_array('no urut', $bersih, true)) {
                $header = $bersih;
                break;
            }
        }

        if (! $header) {
            fclose($handle);
            return back()->withErrors(['berkas' => 'Baris header "No Urut" tidak ditemukan dalam berkas.']);
        }

        $kol = fn (string $kunci) => $this->indeksKolom($header, $kunci);
        $kodeTersedia = Klasifikasi::pluck('kode_klasifikasi')->flip();

        $stat = ['masuk' => 0, 'lewat_ada' => 0, 'lewat_rusak' => 0, 'tanpa_kode' => 0, 'kode_asing' => 0];
        $catatan = [];

        DB::beginTransaction();

        try {
            while (($baris = fgetcsv($handle, 0, $pemisah)) !== false) {
                $ambil = function (string $kunci) use ($baris, $kol) {
                    $i = $kol($kunci);
                    return $i === null ? null : trim((string) ($baris[$i] ?? ''));
                };

                $noUrut = $ambil('no urut');
                if ($noUrut === null || $noUrut === '' || ! is_numeric($noUrut)) {
                    continue; // baris kosong atau judul
                }

                $tglTerima = $this->parseTanggal($ambil('tanggal penerimaan'));
                $tglSurat  = $this->parseTanggal($ambil('tanggal surat'));

                if (! $tglTerima) {
                    $stat['lewat_rusak']++;
                    $catatan[] = "No Urut {$noUrut}: tanggal penerimaan tidak terbaca.";
                    continue;
                }

                $tahun = $tglTerima->year;

                                // Kode klasifikasi diperiksa lebih dulu agar catatannya tetap muncul
                $kode = $ambil('koreksi kode klasifikasi') ?: $ambil('kode klasifikasi');
                $kode = ($kode === '' || $kode === '-') ? null : $kode;

                if ($kode === null) {
                    $stat['tanpa_kode']++;
                } elseif (! $kodeTersedia->has($kode)) {
                    $catatan[] = "No Urut {$noUrut} (tgl terima {$tglTerima->format('d/m/Y')}): kode \"{$kode}\" tidak terdaftar di JRA.";
                    $stat['kode_asing']++;
                    $kode = null;
                }

                $sudahAda = Arsip::where('jenis', 'masuk')
                    ->where('tahun', $tahun)->where('no_urut', $noUrut)->exists();

                if ($sudahAda) {
                    $stat['lewat_ada']++;
                    continue;
                }

                Arsip::create([
                    'jenis'                => 'masuk',
                    'no_urut'              => (int) $noUrut,
                    'tahun'                => $tahun,
                    'no_tnde'              => $ambil('no tnde') ?: null,
                    'tanggal_penerimaan'   => $tglTerima,
                    'tanggal_surat'        => $tglSurat ?? $tglTerima,
                    'nomor_surat'          => $ambil('nomor surat') ?: null,
                    'sifat'                => $this->normalkanSifat($ambil('sifat')),
                    'lampiran'             => $ambil('lampiran') ?: null,
                    'isi_ringkas'          => $ambil('isi ringkas') ?: '(tidak ada uraian)',
                    'dari'                 => $ambil('dari') ?: null,
                    'kepada'               => $ambil('kepada') ?: null,
                    'tingkat_perkembangan' => $ambil('tingkat perkembangan') ?: null,
                    'kode_klasifikasi'     => $kode,
                ]);

                $stat['masuk']++;
            }

            fclose($handle);

            if ($simulasi) {
                DB::rollBack();
            } else {
                DB::commit();
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);
            return back()->withErrors(['berkas' => 'Gagal: ' . $e->getMessage()]);
        }

        return back()->with([
            'stat'     => $stat,
            'catatan'  => array_slice($catatan, 0, 50),
            'simulasi' => $simulasi,
        ]);
    }

    private function indeksKolom(array $header, string $kunci): ?int
    {
        foreach ($header as $i => $nama) {
            if (str_contains($nama, $kunci)) return $i;
        }
        return null;
    }

    private function parseTanggal($nilai): ?Carbon
    {
        $nilai = trim((string) $nilai);
        if ($nilai === '') return null;

        // Angka murni = nomor seri tanggal bawaan Excel
        if (is_numeric($nilai)) {
            return Carbon::create(1899, 12, 30)->addDays((int) $nilai);
        }

        foreach (['d-m-Y', 'd/m/Y', 'Y-m-d', 'd-m-y', 'd/m/y'] as $format) {
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

    private function normalkanSifat(?string $nilai): string
    {
        $v = strtolower(trim((string) $nilai));

        if (str_contains($v, 'sangat rahasia')) return 'Sangat Rahasia';
        if (str_contains($v, 'rahasia'))        return 'Rahasia';
        if (str_contains($v, 'terbatas'))       return 'Terbatas';

        return 'Biasa/Terbuka';
    }
        public function formDokumen()
    {
        return view('surat_masuk.import_dokumen');
    }

    public function prosesDokumen(Request $request)
    {
        $request->validate([
            'berkas'   => ['required', 'array', 'min:1'],
            'berkas.*' => ['file', 'max:20480'],
        ]);

        $stat = ['cocok' => 0, 'tak_cocok' => 0, 'ganda' => 0, 'bukan_pdf' => 0, 'ditimpa' => 0];
        $catatan = [];

        foreach ($request->file('berkas') as $file) {
            $namaAsli = $file->getClientOriginalName();

            // Pastikan isinya benar-benar PDF
            $handle = fopen($file->getRealPath(), 'rb');
            $awal = fread($handle, 1024);
            fclose($handle);

            if (! str_contains($awal, '%PDF-')) {
                $stat['bukan_pdf']++;
                $catatan[] = "{$namaAsli}: bukan berkas PDF.";
                continue;
            }

            $dasar = pathinfo($namaAsli, PATHINFO_FILENAME);
            $tahun = null;
            $tnde  = null;

            // Pola bawaan TNDE: kode tetap + tahun + No TNDE, mis. "11320261238-1"
            if (preg_match('/^\d+(20\d{2})(\d{3,5})-\d+$/', $dasar, $m)) {
                $tahun = (int) $m[1];
                $tnde  = ltrim($m[2], '0') ?: '0';
            } else {
                // Pola lama: "2026_1091" atau "1091" polos
                preg_match_all('/\d+/', $dasar, $cocok);
                $angka = $cocok[0] ?? [];

                if (empty($angka)) {
                    $stat['tak_cocok']++;
                    $catatan[] = "{$namaAsli}: tidak ada angka pada nama berkas.";
                    continue;
                }

                if (count($angka) >= 2 && strlen($angka[0]) === 4 && (int) $angka[0] > 2000) {
                    $tahun = (int) $angka[0];
                    $tnde  = $angka[1];
                } else {
                    $tnde = $angka[0];
                }
            }

            $query = Arsip::where('jenis', 'masuk')->where('no_tnde', $tnde);
            if ($tahun) {
                $query->where('tahun', $tahun);
            }

            $ditemukan = $query->get();

            if ($ditemukan->isEmpty()) {
                $stat['tak_cocok']++;
                $catatan[] = "{$namaAsli}: tidak ada arsip dengan No TNDE {$tnde}"
                           . ($tahun ? " tahun {$tahun}" : '') . '.';
                continue;
            }

            if ($ditemukan->count() > 1) {
                $stat['ganda']++;
                $catatan[] = "{$namaAsli}: No TNDE {$tnde} ada di "
                           . $ditemukan->count() . ' arsip (tahun '
                           . $ditemukan->pluck('tahun')->join(', ')
                           . '). Lewati — sertakan tahun pada nama berkas.';
                continue;
            }

            $arsip = $ditemukan->first();

            if ($arsip->dokumen_path) {
                Storage::disk('public')->delete($arsip->dokumen_path);
                $stat['ditimpa']++;
            }

            $namaSimpan = 'SM_' . $arsip->tahun . '_TNDE' . $arsip->no_tnde . '_' . now()->format('His') . '.pdf';

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