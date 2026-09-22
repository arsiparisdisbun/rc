<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use App\Models\Berkas;
use App\Models\Klasifikasi;
use Illuminate\Http\Request;

class KlasifikasiController extends Controller
{
    public function index(Request $request)
    {
        $klasifikasi = Klasifikasi::when($request->filled('q'), function ($query) use ($request) {
                $cari = $request->q;
                $query->where(fn ($s) => $s->where('kode_klasifikasi', 'like', "%{$cari}%")
                                           ->orWhere('uraian', 'like', "%{$cari}%"));
            })
            ->when($request->filled('nasib'), fn ($q) => $q->where('nasib_akhir', $request->nasib))
            ->orderBy('kode_klasifikasi')
            ->paginate(50)->withQueryString();

        return view('master.klasifikasi.index', [
            'klasifikasi' => $klasifikasi,
            'total'       => Klasifikasi::count(),
            'daftarNasib' => Klasifikasi::distinct()->orderBy('nasib_akhir')->pluck('nasib_akhir'),
        ]);
    }

    public function create()
    {
        return view('master.klasifikasi.create', ['daftarNasib' => $this->nasibTerpakai()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturan(), $this->pesan());

        Klasifikasi::create($data);

        return redirect()->route('klasifikasi.index', ['q' => $data['kode_klasifikasi']])
            ->with('success', "Kode {$data['kode_klasifikasi']} berhasil ditambahkan.");
    }

    // Kode klasifikasi berupa teks bertitik (mis. 500.3.2.1), jadi dicari
    // manual daripada mengandalkan route model binding
    public function edit(string $kode)
    {
        $klasifikasi = Klasifikasi::findOrFail($kode);

        return view('master.klasifikasi.edit', [
            'klasifikasi' => $klasifikasi,
            'daftarNasib' => $this->nasibTerpakai(),
            'pemakaian'   => $this->hitungPemakaian($kode),
        ]);
    }

    public function update(Request $request, string $kode)
    {
        $klasifikasi = Klasifikasi::findOrFail($kode);

        $aturan = $this->aturan();

        // Kode dikunci karena tersimpan sebagai teks di arsip, berkas, dan
        // item berkas — mengubahnya akan memutus perhitungan retensi mereka
        unset($aturan['kode_klasifikasi']);

        $data = $request->validate($aturan, $this->pesan());

        $klasifikasi->update($data);

        return redirect()->route('klasifikasi.index', ['q' => $kode])
            ->with('success', "Kode {$kode} berhasil diperbarui.");
    }

    private function hitungPemakaian(string $kode): int
    {
        return Arsip::where('kode_klasifikasi', $kode)->count()
             + Berkas::where('kode_klasifikasi', $kode)->count();
    }

    private function nasibTerpakai()
    {
        return Klasifikasi::distinct()->orderBy('nasib_akhir')->pluck('nasib_akhir');
    }

    private function aturan(): array
    {
        return [
            'kode_klasifikasi' => ['required', 'string', 'max:50', 'unique:klasifikasi,kode_klasifikasi'],
            'uraian'           => ['required', 'string'],
            'retensi_aktif'    => ['required', 'integer', 'min:0', 'max:100'],
            'retensi_inaktif'  => ['required', 'integer', 'min:0', 'max:100'],
            'nasib_akhir'      => ['required', 'string', 'max:50'],
        ];
    }

    private function pesan(): array
    {
        return [
            'kode_klasifikasi.unique' => 'Kode klasifikasi itu sudah terdaftar dalam JRA.',
            'retensi_aktif.required'  => 'Retensi aktif wajib diisi karena menentukan kapan arsip pindah inaktif.',
            'retensi_inaktif.required'=> 'Retensi inaktif wajib diisi karena menentukan kapan arsip disusutkan.',
        ];
    }
}