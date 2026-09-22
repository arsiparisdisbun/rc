<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use App\Models\Berkas;
use App\Models\Pengaturan;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index(string $kelompok)
    {
        $this->pastikanKelompokSah($kelompok);

        $daftar = Pengaturan::where('kelompok', $kelompok)
            ->orderBy('urutan')->orderBy('nilai')
            ->get();

        // Dihitung sekali untuk semua baris, bukan satu query per baris
        $pemakaian = $this->hitungPemakaian($kelompok);

        return view('master.pengaturan.index', [
            'kelompok'  => $kelompok,
            'info'      => Pengaturan::KELOMPOK[$kelompok],
            'daftar'    => $daftar,
            'pemakaian' => $pemakaian,
        ]);
    }

    public function store(Request $request, string $kelompok)
    {
        $this->pastikanKelompokSah($kelompok);

        $data = $request->validate([
            'nilai' => ['required', 'string', 'max:100'],
        ]);

        $sudahAda = Pengaturan::where('kelompok', $kelompok)
            ->where('nilai', $data['nilai'])->exists();

        if ($sudahAda) {
            return back()->withErrors(['nilai' => "\"{$data['nilai']}\" sudah ada dalam daftar."]);
        }

        Pengaturan::create([
            'kelompok' => $kelompok,
            'nilai'    => $data['nilai'],
            'urutan'   => (Pengaturan::where('kelompok', $kelompok)->max('urutan') ?? 0) + 1,
        ]);

        return back()->with('success', "\"{$data['nilai']}\" ditambahkan.");
    }

    public function destroy(string $kelompok, Pengaturan $pengaturan)
    {
        $this->pastikanKelompokSah($kelompok);

        abort_unless($pengaturan->kelompok === $kelompok, 404);

        $dipakai = $this->hitungPemakaian($kelompok)[$pengaturan->nilai] ?? 0;

        if ($dipakai > 0) {
            return back()->withErrors([
                'hapus' => "\"{$pengaturan->nilai}\" masih dipakai {$dipakai} data, sehingga tidak dapat dihapus.",
            ]);
        }

        $nilai = $pengaturan->nilai;
        $pengaturan->delete();

        return back()->with('success', "\"{$nilai}\" dihapus dari daftar.");
    }

    /**
     * Menghitung berapa data yang memakai tiap nilai. Nilai ini tersimpan
     * sebagai teks di kolom arsip/berkas, bukan relasi, jadi dihitung
     * dengan mengelompokkan kolom yang bersangkutan.
     */
    private function hitungPemakaian(string $kelompok): array
    {
        $sumber = match ($kelompok) {
            'sub_bagian'        => [Berkas::class, 'sub_bagian'],
            'kategori_keuangan' => [Berkas::class, 'kategori_keuangan'],
            'satuan'            => [Berkas::class, 'satuan'],
            'skkad'             => null, // dipakai dua tabel, ditangani di bawah
        };

        if ($kelompok === 'skkad') {
            $dariBerkas = Berkas::selectRaw('skkad AS nilai, COUNT(*) AS jumlah')
                ->whereNotNull('skkad')->groupBy('skkad')->pluck('jumlah', 'nilai')->all();

            $dariArsip = Arsip::selectRaw('sifat AS nilai, COUNT(*) AS jumlah')
                ->whereNotNull('sifat')->groupBy('sifat')->pluck('jumlah', 'nilai')->all();

            $gabungan = $dariBerkas;
            foreach ($dariArsip as $nilai => $jumlah) {
                $gabungan[$nilai] = ($gabungan[$nilai] ?? 0) + $jumlah;
            }

            return $gabungan;
        }

        [$model, $kolom] = $sumber;

        return $model::selectRaw("{$kolom} AS nilai, COUNT(*) AS jumlah")
            ->whereNotNull($kolom)
            ->groupBy($kolom)
            ->pluck('jumlah', 'nilai')->all();
    }

    private function pastikanKelompokSah(string $kelompok): void
    {
        abort_unless(isset(Pengaturan::KELOMPOK[$kelompok]), 404, 'Kelompok pengaturan tidak dikenal.');
    }
}