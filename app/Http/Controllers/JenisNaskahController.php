<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use App\Models\JenisNaskah;
use Illuminate\Http\Request;

class JenisNaskahController extends Controller
{
    public function index()
    {
        // Diurutkan mengikuti urutan baku tata naskah dinas, bukan abjad
        $jenis = JenisNaskah::orderBy('urutan')->get();

        $pemakaian = Arsip::selectRaw('jenis_naskah_id, COUNT(*) AS jumlah')
            ->whereNotNull('jenis_naskah_id')
            ->groupBy('jenis_naskah_id')
            ->pluck('jumlah', 'jenis_naskah_id');

        return view('master.jenis_naskah.index', compact('jenis', 'pemakaian'));
    }

    public function create()
    {
        return view('master.jenis_naskah.create', $this->pilihan());
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturan(), $this->pesan());

        // Checkbox yang tidak dicentang tidak ikut terkirim, jadi dibaca terpisah
        $data['aktif']  = $request->boolean('aktif');
        $data['urutan'] = $data['urutan'] ?: (JenisNaskah::max('urutan') + 1);

        JenisNaskah::create($data);

        return redirect()->route('jenis-naskah.index')
            ->with('success', "Jenis naskah \"{$data['nama']}\" ditambahkan.");
    }

    public function edit(JenisNaskah $jenisNaskah)
    {
        return view('master.jenis_naskah.edit', array_merge(
            ['jenis' => $jenisNaskah],
            $this->pilihan()
        ));
    }

    public function update(Request $request, JenisNaskah $jenisNaskah)
    {
        $aturan = $this->aturan();
        $aturan['nama'] = ['required', 'string', 'max:255', 'unique:jenis_naskah,nama,' . $jenisNaskah->id];

        $data = $request->validate($aturan, $this->pesan());
        $data['aktif'] = $request->boolean('aktif');

        $jenisNaskah->update($data);

        return redirect()->route('jenis-naskah.index')
            ->with('success', "Jenis naskah \"{$data['nama']}\" diperbarui.");
    }

    public function destroy(JenisNaskah $jenisNaskah)
    {
        $dipakai = Arsip::where('jenis_naskah_id', $jenisNaskah->id)->count();

        if ($dipakai > 0) {
            return back()->withErrors([
                'hapus' => "Jenis naskah \"{$jenisNaskah->nama}\" masih dipakai {$dipakai} surat, sehingga tidak dapat dihapus. Nonaktifkan saja bila sudah tidak dipakai lagi.",
            ]);
        }

        $nama = $jenisNaskah->nama;
        $jenisNaskah->delete();

        return back()->with('success', "Jenis naskah \"{$nama}\" dihapus.");
    }

    private function aturan(): array
    {
        return [
            'nama'         => ['required', 'string', 'max:255', 'unique:jenis_naskah,nama'],
            'kelompok'     => ['nullable', 'string', 'max:100'],
            'sub_kelompok' => ['nullable', 'string', 'max:100'],
            'urutan'       => ['nullable', 'integer', 'min:1'],
            'aktif'        => ['nullable', 'boolean'],
        ];
    }

    private function pesan(): array
    {
        return ['nama.unique' => 'Jenis naskah dengan nama itu sudah ada.'];
    }

    // Kelompok dan sub kelompok yang sudah terpakai, jadi bahan isian cepat
    private function pilihan(): array
    {
        return [
            'daftarKelompok' => JenisNaskah::whereNotNull('kelompok')
                ->distinct()->orderBy('kelompok')->pluck('kelompok'),
            'daftarSub' => JenisNaskah::whereNotNull('sub_kelompok')
                ->distinct()->orderBy('sub_kelompok')->pluck('sub_kelompok'),
        ];
    }
}