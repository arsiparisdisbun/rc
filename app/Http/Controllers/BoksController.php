<?php

namespace App\Http\Controllers;

use App\Models\Boks;
use App\Models\UnitPengolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoksController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $boks = Boks::with('unit')
            ->when(! $user->lihatSemuaUnit(), fn ($q) => $q->where('unit_pengolah', $user->unit_pengolah))
            ->when($request->filled('unit'), fn ($q) => $q->where('unit_pengolah', $request->unit))
            ->when($request->filled('jenis'), fn ($q) => $q->where('jenis', $request->jenis))
            ->when($request->filled('q'), function ($q) use ($request) {
                $cari = $request->q;
                $q->where(fn ($s) => $s->where('lokasi', 'like', "%{$cari}%")
                                       ->orWhere('keterangan', 'like', "%{$cari}%"));
            })
            ->orderBy('unit_pengolah')->orderBy('jenis')->orderBy('nomor')
            ->paginate(30)->withQueryString();

        return view('boks.index', [
            'boks'       => $boks,
            'daftarUnit' => $user->lihatSemuaUnit()
                                ? UnitPengolah::aktif()->get()
                                : UnitPengolah::where('kode', $user->unit_pengolah)->get(),
        ]);
    }

    public function create()
    {
        return view('boks.create', ['daftarUnit' => $this->unitTersedia()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturan(), $this->pesan());
        $this->pastikanUnitBoleh($data['unit_pengolah']);

        $data['terpakai'] = $request->boolean('terpakai', true);

        // Nomor dikunci saat disimpan agar dua orang tidak dapat nomor sama
        $boks = DB::transaction(function () use ($data) {
            $data['nomor'] = Boks::nomorBerikutnya($data['unit_pengolah'], $data['jenis']);
            return Boks::create($data);
        });

        return redirect()->route('boks.index')
            ->with('success', "{$boks->label} untuk unit {$boks->unit_pengolah} berhasil dibuat.");
    }

    public function edit(Boks $bok)
    {
        $this->pastikanUnitBoleh($bok->unit_pengolah);

        return view('boks.edit', ['boks' => $bok, 'daftarUnit' => $this->unitTersedia()]);
    }

    public function update(Request $request, Boks $bok)
    {
        $this->pastikanUnitBoleh($bok->unit_pengolah);

        // Unit dan jenis tidak diubah agar nomor boks tetap sah
        $data = $request->validate([
            'lokasi'     => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $data['terpakai'] = $request->boolean('terpakai');

        $bok->update($data);

        return redirect()->route('boks.index')->with('success', "{$bok->label} berhasil diperbarui.");
    }

    public function destroy(Boks $bok)
    {
        $this->pastikanUnitBoleh($bok->unit_pengolah);

        $label = $bok->label;
        $bok->delete();

        return redirect()->route('boks.index')->with('success', "{$label} telah dihapus.");
    }

    private function aturan(): array
    {
        return [
            'unit_pengolah' => ['required', 'exists:unit_pengolah,kode'],
            'jenis'         => ['required', 'in:aktif,inaktif'],
            'lokasi'        => ['nullable', 'string', 'max:255'],
            'keterangan'    => ['nullable', 'string'],
        ];
    }

    private function pesan(): array
    {
        return ['unit_pengolah.required' => 'Unit pengolah wajib dipilih.'];
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
        $user = auth()->user();

        abort_if(
            ! $user->lihatSemuaUnit() && $unit !== $user->unit_pengolah,
            403,
            'Anda tidak memiliki akses ke boks unit lain.'
        );
    }
        public function show(Boks $bok)
    {
        $this->pastikanUnitBoleh($bok->unit_pengolah);

        $bok->load(['unit', 'berkas.klasifikasi']);

        return view('boks.show', ['boks' => $bok]);
    }

    // Label untuk ditempel di boks fisik. Bisa satu boks, bisa banyak sekaligus.
    public function cetakLabel(Request $request)
    {
        $user = auth()->user();

        $boks = Boks::with('unit')
            ->when(! $user->lihatSemuaUnit(), fn ($q) => $q->where('unit_pengolah', $user->unit_pengolah))
            ->when($request->filled('id'), fn ($q) => $q->whereIn('id', (array) $request->id))
            ->when($request->filled('unit'), fn ($q) => $q->where('unit_pengolah', $request->unit))
            ->when($request->filled('jenis'), fn ($q) => $q->where('jenis', $request->jenis))
            ->orderBy('unit_pengolah')->orderBy('jenis')->orderBy('nomor')
            ->get();

        return view('boks.label', ['boks' => $boks]);
    }
}