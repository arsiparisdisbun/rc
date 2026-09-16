<?php

namespace App\Http\Controllers;

use App\Models\Berkas;
use App\Models\Boks;
use App\Models\UnitPengolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BerkasController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $berkas = Berkas::with(['unit', 'klasifikasi', 'boks'])
            ->withCount('item')
            ->when(! $user->lihatSemuaUnit(), fn ($q) => $q->where('unit_pengolah', $user->unit_pengolah))
            ->when($request->filled('unit'), fn ($q) => $q->where('unit_pengolah', $request->unit))
            ->when($request->filled('tahun'), fn ($q) => $q->where('tahun', $request->tahun))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('q'), function ($q) use ($request) {
                $cari = $request->q;
                $q->where(fn ($s) => $s->where('uraian', 'like', "%{$cari}%")
                                       ->orWhere('kode_klasifikasi', 'like', "%{$cari}%"));
            })
            ->orderByDesc('tahun')->orderByDesc('no_berkas')
            ->paginate(25)->withQueryString();

        return view('berkas.index', [
            'berkas'      => $berkas,
            'daftarUnit'  => $this->unitTersedia(),
            'daftarTahun' => Berkas::distinct()->orderByDesc('tahun')->pluck('tahun'),
        ]);
    }

    public function show(Berkas $berka)
    {
        $this->pastikanBolehLihat($berka);

        $berka->load(['unit', 'klasifikasi', 'boks', 'verifikator', 'item.arsip', 'item.klasifikasi']);

        return view('berkas.show', ['berkas' => $berka]);
    }

    public function create()
    {
        return view('berkas.create', ['daftarUnit' => $this->unitTersedia()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturan(), $this->pesan());
        $this->pastikanUnitBoleh($data['unit_pengolah']);

        if ($data['unit_pengolah'] !== '121.1') {
            $data['sub_bagian'] = null;
        }

        $data['status'] = 'draf';

        $berkas = DB::transaction(function () use ($data) {
            $data['no_berkas'] = Berkas::nomorBerikutnya($data['unit_pengolah'], $data['tahun']);
            return Berkas::create($data);
        });

        return redirect()->route('berkas.show', $berkas)
            ->with('success', "Berkas {$berkas->label} dibuat. Silakan tambahkan item ke dalamnya.");
    }

    public function edit(Berkas $berka)
    {
        $this->pastikanBolehUbah($berka);

        return view('berkas.edit', [
            'berkas'     => $berka,
            'daftarUnit' => $this->unitTersedia(),
            'daftarBoks' => Boks::where('unit_pengolah', $berka->unit_pengolah)
                                ->orderBy('jenis')->orderBy('nomor')->get(),
        ]);
    }

    public function update(Request $request, Berkas $berka)
    {
        $this->pastikanBolehUbah($berka);

        $aturan = $this->aturan();

        // Unit dan tahun dikunci agar nomor berkas tetap sah
        unset($aturan['unit_pengolah'], $aturan['tahun']);

        $aturan['boks_id']       = ['nullable', 'exists:boks,id'];
        $aturan['lokasi_simpan'] = ['nullable', 'string', 'max:255'];

        $data = $request->validate($aturan, $this->pesan());

        if ($berka->unit_pengolah !== '121.1') {
            $data['sub_bagian'] = null;
        }

        $berka->update($data);

        return redirect()->route('berkas.show', $berka)
            ->with('success', "Berkas {$berka->label} berhasil diperbarui.");
    }

    public function destroy(Berkas $berka)
    {
        $this->pastikanBolehUbah($berka);

        abort_if(
            $berka->item()->exists() && ! auth()->user()->lihatSemuaUnit(),
            403,
            'Berkas yang sudah berisi item hanya dapat dihapus oleh Unit Kearsipan.'
        );

        $label = $berka->label;
        $berka->delete();

        return redirect()->route('berkas.index')->with('success', "Berkas {$label} telah dihapus.");
    }

    // ---------- Cetak ----------

    // Daftar Berkas mengikuti filter yang sedang aktif di halaman daftar
    public function cetakDaftar(Request $request)
    {
        $user = auth()->user();

        $berkas = Berkas::with(['unit', 'klasifikasi', 'boks'])
            ->withCount('item')
            ->when(! $user->lihatSemuaUnit(), fn ($q) => $q->where('unit_pengolah', $user->unit_pengolah))
            ->when($request->filled('unit'), fn ($q) => $q->where('unit_pengolah', $request->unit))
            ->when($request->filled('tahun'), fn ($q) => $q->where('tahun', $request->tahun))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->orderBy('unit_pengolah')->orderBy('tahun')->orderBy('no_berkas')
            ->get();

        // Nama unit untuk kepala dokumen; bila lintas unit, tulis gabungan
        $unitKode = $berkas->pluck('unit_pengolah')->unique();

        $namaUnit = $unitKode->count() === 1
            ? optional($berkas->first()->unit)->label
            : 'Semua Unit Pengolah';

        return view('berkas.cetak_daftar', compact('berkas', 'namaUnit'));
    }

    // Daftar Isi Berkas — satu berkas
    public function cetakIsi(Berkas $berka)
    {
        $this->pastikanBolehLihat($berka);

        $berka->load(['unit', 'klasifikasi', 'boks', 'item.arsip', 'item.klasifikasi']);

        return view('berkas.cetak_isi', ['berkas' => $berka]);
    }

    // ---------- Alur verifikasi ----------

    public function ajukan(Berkas $berka)
    {
        $this->pastikanBolehUbah($berka);

        abort_unless($berka->item()->exists(), 422, 'Berkas kosong tidak dapat diajukan.');

        $berka->update([
            'status'        => 'diajukan',
            'diajukan_pada' => now(),
        ]);

        return back()->with('success', "Berkas {$berka->label} diajukan untuk diverifikasi.");
    }

    public function verifikasi(Request $request, Berkas $berka)
    {
        abort_unless(auth()->user()->lihatSemuaUnit(), 403, 'Hanya Unit Kearsipan yang dapat memverifikasi.');

        $data = $request->validate([
            'keputusan' => ['required', 'in:terima,kembalikan'],
            'catatan'   => ['nullable', 'string', 'required_if:keputusan,kembalikan'],
        ], [
            'catatan.required_if' => 'Catatan wajib diisi saat mengembalikan berkas.',
        ]);

        if ($data['keputusan'] === 'terima') {
            $berka->update([
                'status'             => 'terverifikasi',
                'diverifikasi_pada'  => now(),
                'diverifikasi_oleh'  => auth()->id(),
                'catatan_verifikasi' => $data['catatan'] ?? null,
            ]);

            $pesan = "Berkas {$berka->label} telah diverifikasi.";
        } else {
            $berka->update([
                'status'             => 'draf',
                'diajukan_pada'      => null,
                'catatan_verifikasi' => $data['catatan'],
            ]);

            $pesan = "Berkas {$berka->label} dikembalikan ke unit untuk diperbaiki.";
        }

        return back()->with('success', $pesan);
    }

    // Membuka kunci berkas terverifikasi atas izin unit kearsipan
    public function bukaKunci(Request $request, Berkas $berka)
    {
        abort_unless(auth()->user()->lihatSemuaUnit(), 403, 'Hanya Unit Kearsipan yang dapat membuka kunci berkas.');

        $data = $request->validate([
            'catatan' => ['required', 'string'],
        ], [
            'catatan.required' => 'Alasan pembukaan kunci wajib dicatat.',
        ]);

        $berka->update([
            'status'             => 'draf',
            'diajukan_pada'      => null,
            'diverifikasi_pada'  => null,
            'diverifikasi_oleh'  => null,
            'catatan_verifikasi' => $data['catatan'],
        ]);

        return back()->with('success', "Kunci berkas {$berka->label} dibuka. Unit dapat mengubahnya kembali.");
    }

    // ---------- Pembantu ----------

    private function aturan(): array
    {
        return [
            'unit_pengolah'    => ['required', 'exists:unit_pengolah,kode'],
            'sub_bagian'       => ['nullable', 'in:' . implode(',', Berkas::SUB_BAGIAN)],
            'tahun'            => ['required', 'integer', 'min:1990', 'max:' . (now()->year + 1)],
            'kode_klasifikasi' => ['nullable', 'exists:klasifikasi,kode_klasifikasi'],
            'uraian'           => ['required', 'string'],
            'tahun_mulai'      => ['nullable', 'integer', 'min:1900', 'max:' . (now()->year + 1)],
            'tahun_selesai'    => ['nullable', 'integer', 'min:1900', 'max:' . (now()->year + 1), 'gte:tahun_mulai'],
            'jumlah_fisik'     => ['nullable', 'integer', 'min:1'],
            'satuan'           => ['required', 'in:Berkas,Lembar,Sampul'],
            'skkad'            => ['required', 'in:Biasa/Terbuka,Terbatas,Rahasia,Sangat Rahasia'],
            'keterangan'       => ['nullable', 'string'],
        ];
    }

    private function pesan(): array
    {
        return [
            'tahun.required'          => 'Tahun berkas wajib diisi karena menentukan nomor berkas.',
            'tahun_selesai.gte'       => 'Tahun akhir kurun waktu tidak boleh lebih kecil dari tahun awal.',
            'kode_klasifikasi.exists' => 'Kode klasifikasi tidak terdaftar dalam JRA.',
        ];
    }

    private function unitTersedia()
    {
        $user = auth()->user();

        return $user->lihatSemuaUnit()
            ? UnitPengolah::aktif()->get()
            : UnitPengolah::where('kode', $user->unit_pengolah)->get();
    }

    private function pastikanUnitBoleh(string $unit): void
    {
        abort_if(
            ! auth()->user()->lihatSemuaUnit() && $unit !== auth()->user()->unit_pengolah,
            403,
            'Anda tidak dapat membuat berkas untuk unit lain.'
        );
    }

    private function pastikanBolehLihat(Berkas $berkas): void
    {
        abort_if(
            ! auth()->user()->lihatSemuaUnit() && $berkas->unit_pengolah !== auth()->user()->unit_pengolah,
            403,
            'Anda tidak memiliki akses ke berkas unit lain.'
        );
    }

    private function pastikanBolehUbah(Berkas $berkas): void
    {
        $this->pastikanBolehLihat($berkas);

        abort_unless(
            $berkas->bolehDiubahOleh(auth()->user()),
            403,
            'Berkas sudah terverifikasi. Hubungi Unit Kearsipan untuk membuka kuncinya.'
        );
    }
}