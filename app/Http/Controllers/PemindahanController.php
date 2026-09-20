<?php

namespace App\Http\Controllers;

use App\Models\Berkas;
use App\Models\Boks;
use App\Models\Pemindahan;
use App\Models\UnitPengolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemindahanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $pemindahan = Pemindahan::with(['unit', 'pengaju', 'pemroses'])
            ->withCount('berkas')
            ->when(! $user->lihatSemuaUnit(), fn ($q) => $q->where('unit_pengolah', $user->unit_pengolah))
            ->when($request->filled('unit'), fn ($q) => $q->where('unit_pengolah', $request->unit))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at')
            ->paginate(25)->withQueryString();

        return view('pemindahan.index', [
            'pemindahan' => $pemindahan,
            'daftarUnit' => $this->unitTersedia(),
        ]);
    }

    // Halaman unit memilih berkas inaktif yang akan diserahkan
    public function create()
    {
        $user = auth()->user();

        abort_if($user->kearsipan(), 403,
            'Pengajuan penyerahan dibuat oleh unit pengolah, bukan Unit Kearsipan.');

        $unit = $user->lihatSemuaUnit() ? request('unit') : $user->unit_pengolah;

        // Berkas yang sudah terverifikasi, sudah jatuh tempo inaktif, dan belum pernah diserahkan
        $kandidat = Berkas::with(['klasifikasi', 'boks'])
            ->where('status', 'terverifikasi')
            ->whereNull('pemindahan_id')
            ->when($unit, fn ($q) => $q->where('unit_pengolah', $unit))
            ->orderBy('tahun')->orderBy('no_berkas')
            ->get()
            ->filter(fn ($b) => in_array($b->status_penyimpanan, ['Inaktif'], true)
                             || str_starts_with($b->status_penyimpanan, 'Siap'));

        return view('pemindahan.create', [
            'kandidat'   => $kandidat,
            'unit'       => $unit,
            'daftarUnit' => $this->unitTersedia(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'unit_pengolah'  => ['required', 'exists:unit_pengolah,kode'],
            'berkas_id'      => ['required', 'array', 'min:1'],
            'berkas_id.*'    => ['exists:berkas,id'],
            'nomor_ba'       => ['nullable', 'string', 'max:100'],
            'tanggal_ba'     => ['nullable', 'date'],
            'pihak1_nama'    => ['required', 'string', 'max:255'],
            'pihak1_nip'     => ['nullable', 'string', 'max:30'],
            'pihak1_pangkat' => ['nullable', 'string', 'max:255'],
            'pihak1_jabatan' => ['required', 'string', 'max:255'],
            'catatan'        => ['nullable', 'string'],
        ], [
            'berkas_id.required'      => 'Pilih minimal satu berkas untuk diserahkan.',
            'pihak1_nama.required'    => 'Nama penanda tangan unit wajib diisi.',
            'pihak1_jabatan.required' => 'Jabatan penanda tangan unit wajib diisi.',
        ]);

        $this->pastikanUnitBoleh($data['unit_pengolah']);

        $pemindahan = DB::transaction(function () use ($data) {
            $berkasId = $data['berkas_id'];
            unset($data['berkas_id']);

            $data['status']        = 'diajukan';
            $data['diajukan_pada'] = now();
            $data['diajukan_oleh'] = auth()->id();

            $pemindahan = Pemindahan::create($data);

            // Hanya berkas milik unit ini dan belum pernah diserahkan
            Berkas::whereIn('id', $berkasId)
                ->where('unit_pengolah', $pemindahan->unit_pengolah)
                ->whereNull('pemindahan_id')
                ->update(['pemindahan_id' => $pemindahan->id]);

            return $pemindahan;
        });

        return redirect()->route('pemindahan.show', $pemindahan)
            ->with('success', "Pengajuan penyerahan dibuat dengan {$pemindahan->berkas()->count()} berkas.");
    }

    public function show(Pemindahan $pemindahan)
    {
        $this->pastikanBolehLihat($pemindahan);

        $pemindahan->load(['unit', 'pengaju', 'pemroses', 'berkas.klasifikasi', 'berkas.boks']);

        // Boks inaktif milik Unit Kearsipan, karena arsip inaktif disimpan di record center
        $boksInaktif = Boks::where('jenis', 'inaktif')
            ->orderBy('nomor')->get();

        return view('pemindahan.show', compact('pemindahan', 'boksInaktif'));
    }

    // Unit Kearsipan menerima penyerahan: tiap berkas ditentukan boks inaktifnya
    public function terima(Request $request, Pemindahan $pemindahan)
    {
        abort_unless(auth()->user()->lihatSemuaUnit(), 403,
            'Hanya Unit Kearsipan yang dapat menerima penyerahan.');

        abort_unless($pemindahan->diajukan(), 422, 'Penyerahan ini sudah diproses.');

        $data = $request->validate([
            'boks'           => ['required', 'array'],
            'boks.*'         => ['nullable', 'exists:boks,id'],
            'pihak2_nama'    => ['required', 'string', 'max:255'],
            'pihak2_nip'     => ['nullable', 'string', 'max:30'],
            'pihak2_pangkat' => ['nullable', 'string', 'max:255'],
            'pihak2_jabatan' => ['required', 'string', 'max:255'],
            'tanggal_terima' => ['required', 'date'],
        ], [
            'pihak2_nama.required'    => 'Nama penanda tangan Unit Kearsipan wajib diisi.',
            'pihak2_jabatan.required' => 'Jabatan penanda tangan Unit Kearsipan wajib diisi.',
        ]);

        // Semua berkas harus punya boks tujuan sebelum penyerahan diterima
        $belumBerboks = collect($data['boks'])->filter(fn ($v) => blank($v))->keys();

        if ($belumBerboks->isNotEmpty()) {
            return back()->withErrors([
                'boks' => 'Masih ada berkas yang belum ditentukan boks inaktifnya.',
            ])->withInput();
        }

        DB::transaction(function () use ($data, $pemindahan) {
            foreach ($data['boks'] as $berkasId => $boksId) {
                Berkas::where('id', $berkasId)
                    ->where('pemindahan_id', $pemindahan->id)
                    ->update([
                        'boks_id'          => $boksId,
                        'dipindahkan_pada' => $data['tanggal_terima'],
                    ]);
            }

            $pemindahan->update([
                'status'         => 'diterima',
                'diproses_pada'  => now(),
                'diproses_oleh'  => auth()->id(),
                'pihak2_nama'    => $data['pihak2_nama'],
                'pihak2_nip'     => $data['pihak2_nip'] ?? null,
                'pihak2_pangkat' => $data['pihak2_pangkat'] ?? null,
                'pihak2_jabatan' => $data['pihak2_jabatan'],
            ]);
        });

        return back()->with('success', 'Penyerahan diterima. Berkas telah dipindahkan ke boks inaktif.');
    }

    public function tolak(Request $request, Pemindahan $pemindahan)
    {
        abort_unless(auth()->user()->lihatSemuaUnit(), 403,
            'Hanya Unit Kearsipan yang dapat menolak penyerahan.');

        abort_unless($pemindahan->diajukan(), 422, 'Penyerahan ini sudah diproses.');

        $data = $request->validate([
            'catatan' => ['required', 'string'],
        ], [
            'catatan.required' => 'Alasan penolakan wajib diisi.',
        ]);

        DB::transaction(function () use ($data, $pemindahan) {
            // Berkas dilepas kembali agar bisa diajukan ulang
            Berkas::where('pemindahan_id', $pemindahan->id)->update(['pemindahan_id' => null]);

            $pemindahan->update([
                'status'        => 'ditolak',
                'diproses_pada' => now(),
                'diproses_oleh' => auth()->id(),
                'catatan'       => $data['catatan'],
            ]);
        });

        return back()->with('success', 'Penyerahan ditolak. Berkas dikembalikan ke unit.');
    }

    public function cetakBa(Pemindahan $pemindahan)
    {
        $this->pastikanBolehLihat($pemindahan);

        $pemindahan->load(['unit', 'berkas.klasifikasi', 'berkas.boks']);

        return view('pemindahan.cetak_ba', compact('pemindahan'));
    }

    // ---------- Pembantu ----------

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
            403, 'Anda tidak dapat mengajukan penyerahan untuk unit lain.'
        );
    }

    private function pastikanBolehLihat(Pemindahan $pemindahan): void
    {
        abort_if(
            ! auth()->user()->lihatSemuaUnit() && $pemindahan->unit_pengolah !== auth()->user()->unit_pengolah,
            403, 'Anda tidak memiliki akses ke penyerahan unit lain.'
        );
    }
}