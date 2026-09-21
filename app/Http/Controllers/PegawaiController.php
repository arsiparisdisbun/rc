<?php

namespace App\Http\Controllers;

use App\Models\Berkas;
use App\Models\Pegawai;
use App\Models\UnitPengolah;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $this->pastikanBoleh();

        $pegawai = Pegawai::withCount('berkas')
            ->when($request->filled('q'), function ($q) use ($request) {
                $cari = $request->q;
                $q->where(fn ($s) => $s->where('nama', 'like', "%{$cari}%")
                                       ->orWhere('nip', 'like', "%{$cari}%")
                                       ->orWhere('jabatan', 'like', "%{$cari}%"));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->orderBy('nama')
            ->paginate(25)->withQueryString();

        return view('pegawai.index', compact('pegawai'));
    }

    public function create()
    {
        $this->pastikanBoleh();

        return view('pegawai.create', ['daftarUnit' => UnitPengolah::aktif()->get()]);
    }

    public function store(Request $request)
    {
        $this->pastikanBoleh();

        $data = $request->validate($this->aturan());

        Pegawai::create($data);

        return redirect()->route('pegawai.index')->with('success', "Data pegawai {$data['nama']} berhasil ditambahkan.");
    }

    public function show(Pegawai $pegawai)
    {
        $this->pastikanBoleh();

        $pegawai->load('unit');

        $berkas = Berkas::with(['klasifikasi', 'boks'])
            ->withCount('item')
            ->where('pegawai_id', $pegawai->id)
            ->orderByDesc('tahun')
            ->get();

        return view('pegawai.show', compact('pegawai', 'berkas'));
    }

    public function edit(Pegawai $pegawai)
    {
        $this->pastikanBoleh();

        return view('pegawai.edit', [
            'pegawai'    => $pegawai,
            'daftarUnit' => UnitPengolah::aktif()->get(),
        ]);
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $this->pastikanBoleh();

        $aturan = $this->aturan();
        $aturan['nip'] = ['nullable', 'string', 'max:30', 'unique:pegawai,nip,' . $pegawai->id];

        $data = $request->validate($aturan);

        $pegawai->update($data);

        return redirect()->route('pegawai.show', $pegawai)->with('success', "Data pegawai {$pegawai->nama} berhasil diperbarui.");
    }

    public function destroy(Pegawai $pegawai)
    {
        $this->pastikanBoleh();

        abort_if(
            $pegawai->berkas()->exists(),
            422,
            'Pegawai ini masih memiliki berkas. Pindahkan atau hapus berkasnya terlebih dahulu.'
        );

        $nama = $pegawai->nama;
        $pegawai->delete();

        return redirect()->route('pegawai.index')->with('success', "Data pegawai {$nama} telah dihapus.");
    }

    private function aturan(): array
    {
        return [
            'nip'            => ['nullable', 'string', 'max:30', 'unique:pegawai,nip'],
            'nama'           => ['required', 'string', 'max:255'],
            'jabatan'        => ['nullable', 'string', 'max:255'],
            'unit_pengolah'  => ['nullable', 'exists:unit_pengolah,kode'],
            'status'         => ['required', 'in:' . implode(',', array_keys(Pegawai::STATUS))],
            'tanggal_status' => ['nullable', 'date'],
            'keterangan'     => ['nullable', 'string'],
        ];
    }

    // Data kepegawaian dibatasi seperti surat masuk — hanya Sekretariat
    // (pemilik sub bagian Umum dan Kepegawaian) dan Unit Kearsipan.
    private function pastikanBoleh(): void
    {
        $user = auth()->user();

        abort_unless(
            $user->lihatSemuaUnit() || $user->unit_pengolah === '121.1',
            403,
            'Data kepegawaian hanya dapat diakses oleh Sekretariat dan Unit Kearsipan.'
        );
    }
}