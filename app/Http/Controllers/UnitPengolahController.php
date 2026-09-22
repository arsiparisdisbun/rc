<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use App\Models\Berkas;
use App\Models\Boks;
use App\Models\Pegawai;
use App\Models\UnitPengolah;
use App\Models\User;
use Illuminate\Http\Request;

class UnitPengolahController extends Controller
{
    public function index()
    {
        // Diurutkan mengikuti urutan tampil, bukan kode
        $unit = UnitPengolah::orderBy('urutan')->orderBy('kode')->get();

        // Kode unit tersimpan sebagai teks di banyak tabel, jadi dihitung
        // per tabel lalu digabung untuk tahu apakah unit masih terpakai
        $pemakaian = [];

        foreach ([Arsip::class, Berkas::class, Boks::class, Pegawai::class, User::class] as $model) {
            $hitung = $model::selectRaw('unit_pengolah, COUNT(*) AS jumlah')
                ->whereNotNull('unit_pengolah')
                ->groupBy('unit_pengolah')
                ->pluck('jumlah', 'unit_pengolah');

            foreach ($hitung as $kode => $jumlah) {
                $pemakaian[$kode] = ($pemakaian[$kode] ?? 0) + $jumlah;
            }
        }

        return view('master.unit_pengolah.index', compact('unit', 'pemakaian'));
    }

    public function create()
    {
        return view('master.unit_pengolah.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturan(), $this->pesan());

        // Checkbox yang tidak dicentang tidak ikut terkirim, jadi dibaca terpisah
        $data['aktif']  = $request->boolean('aktif');
        $data['urutan'] = $data['urutan'] ?: (UnitPengolah::max('urutan') + 1);

        UnitPengolah::create($data);

        return redirect()->route('unit-pengolah.index')
            ->with('success', "Unit {$data['kode']} berhasil ditambahkan.");
    }

    // Kode unit berupa teks bertitik (mis. 121.6.1), jadi dicari manual
    // daripada mengandalkan route model binding
    public function edit(string $kode)
    {
        $unit = UnitPengolah::findOrFail($kode);

        return view('master.unit_pengolah.edit', compact('unit'));
    }

    public function update(Request $request, string $kode)
    {
        $unit = UnitPengolah::findOrFail($kode);

        $aturan = $this->aturan();

        // Kode dikunci karena dipakai sebagai penanda di arsip, berkas,
        // boks, pegawai, dan akun — mengubahnya akan memutus kaitan itu
        unset($aturan['kode']);

        $data = $request->validate($aturan, $this->pesan());
        $data['aktif'] = $request->boolean('aktif');

        $unit->update($data);

        return redirect()->route('unit-pengolah.index')
            ->with('success', "Unit {$unit->kode} berhasil diperbarui.");
    }

    public function destroy(string $kode)
    {
        $unit = UnitPengolah::findOrFail($kode);

        $dipakai = 0;

        foreach ([Arsip::class, Berkas::class, Boks::class, Pegawai::class, User::class] as $model) {
            $dipakai += $model::where('unit_pengolah', $unit->kode)->count();
        }

        if ($dipakai > 0) {
            return back()->withErrors([
                'hapus' => "Unit {$unit->kode} masih dipakai {$dipakai} data, sehingga tidak dapat dihapus. Nonaktifkan saja bila unit ini sudah tidak berlaku.",
            ]);
        }

        $kodeUnit = $unit->kode;
        $unit->delete();

        return back()->with('success', "Unit {$kodeUnit} dihapus.");
    }

    private function aturan(): array
    {
        return [
            'kode'   => ['required', 'string', 'max:20', 'unique:unit_pengolah,kode'],
            'nama'   => ['required', 'string', 'max:255'],
            'urutan' => ['nullable', 'integer', 'min:1'],
            'aktif'  => ['nullable', 'boolean'],
        ];
    }

    private function pesan(): array
    {
        return ['kode.unique' => 'Kode unit itu sudah terdaftar.'];
    }
}