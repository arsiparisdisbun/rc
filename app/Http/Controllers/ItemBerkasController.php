<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use App\Models\Berkas;
use App\Models\ItemBerkas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemBerkasController extends Controller
{
    // Halaman memilih arsip yang sudah tercatat di buku agenda
    public function pilih(Request $request, Berkas $berka)
    {
        $this->pastikanBoleh($berka);

        // Arsip yang belum masuk berkas mana pun
        $sudahDipakai = ItemBerkas::whereNotNull('arsip_id')->pluck('arsip_id');

        $arsip = Arsip::with('klasifikasi')
            ->whereNotIn('id', $sudahDipakai)
            ->where(function ($q) use ($berka) {
                // Surat keluar milik unit berkas, surat masuk hanya untuk Sekretariat
                $q->where(fn ($s) => $s->where('jenis', 'keluar')->where('unit_pengolah', $berka->unit_pengolah));

                if ($berka->unit_pengolah === '121.1') {
                    $q->orWhere('jenis', 'masuk');
                }
            })
            ->when($request->filled('q'), function ($q) use ($request) {
                $cari = $request->q;
                $q->where(fn ($s) => $s->where('isi_ringkas', 'like', "%{$cari}%")
                                       ->orWhere('nomor_surat', 'like', "%{$cari}%")
                                       ->orWhere('kode_klasifikasi', 'like', "%{$cari}%"));
            })
            ->when($request->filled('jenis'), fn ($q) => $q->where('jenis', $request->jenis))
            ->when($request->filled('kode'), fn ($q) => $q->where('kode_klasifikasi', $request->kode))
            ->orderByDesc('tanggal_surat')
            ->paginate(30)->withQueryString();

        return view('item_berkas.pilih', ['berkas' => $berka, 'arsip' => $arsip]);
    }

    // Menambahkan satu atau banyak arsip sekaligus ke berkas
    public function tambahDariArsip(Request $request, Berkas $berka)
    {
        $this->pastikanBoleh($berka);

        $data = $request->validate([
            'arsip_id'   => ['required', 'array', 'min:1'],
            'arsip_id.*' => ['exists:arsip,id'],
        ], [
            'arsip_id.required' => 'Pilih minimal satu arsip.',
        ]);

        $masuk = 0;

        DB::transaction(function () use ($data, $berka, &$masuk) {
            $nomor = ItemBerkas::nomorBerikutnya($berka->id);

            foreach ($data['arsip_id'] as $id) {
                // Lewati arsip yang sudah masuk berkas lain
                if (ItemBerkas::where('arsip_id', $id)->exists()) continue;

                ItemBerkas::create([
                    'berkas_id'  => $berka->id,
                    'nomor_item' => $nomor++,
                    'arsip_id'   => $id,
                    'skkad'      => $berka->skkad,
                ]);

                $masuk++;
            }
        });

        $this->perbaruiKurunWaktu($berka);

        return redirect()->route('berkas.show', $berka)
            ->with('success', "{$masuk} arsip ditambahkan ke berkas {$berka->label}.");
    }

    public function create(Berkas $berka)
    {
        $this->pastikanBoleh($berka);

        return view('item_berkas.create', ['berkas' => $berka]);
    }

    public function store(Request $request, Berkas $berka)
    {
        $this->pastikanBoleh($berka);

        $data = $request->validate($this->aturan(), $this->pesan());

        DB::transaction(function () use ($data, $berka) {
            $data['berkas_id']  = $berka->id;
            $data['nomor_item'] = ItemBerkas::nomorBerikutnya($berka->id);

            ItemBerkas::create($data);
        });

        $this->perbaruiKurunWaktu($berka);

        return redirect()->route('berkas.show', $berka)
            ->with('success', 'Item berhasil ditambahkan ke berkas.');
    }

    public function edit(Berkas $berka, ItemBerkas $item)
    {
        $this->pastikanBoleh($berka);
        $this->pastikanMilikBerkas($berka, $item);

        abort_if($item->dariArsip(), 403,
            'Item yang berasal dari buku agenda diubah melalui buku agenda, bukan dari sini.');

        return view('item_berkas.edit', ['berkas' => $berka, 'item' => $item]);
    }

    public function update(Request $request, Berkas $berka, ItemBerkas $item)
    {
        $this->pastikanBoleh($berka);
        $this->pastikanMilikBerkas($berka, $item);

        abort_if($item->dariArsip(), 403, 'Item dari buku agenda tidak dapat diubah dari sini.');

        $item->update($request->validate($this->aturan(), $this->pesan()));

        $this->perbaruiKurunWaktu($berka);

        return redirect()->route('berkas.show', $berka)->with('success', 'Item berhasil diperbarui.');
    }

    public function destroy(Berkas $berka, ItemBerkas $item)
    {
        $this->pastikanBoleh($berka);
        $this->pastikanMilikBerkas($berka, $item);

        $nomor = $item->nomor_item;
        $item->delete();

        $this->rapikanNomor($berka);
        $this->perbaruiKurunWaktu($berka);

        return back()->with('success', "Item {$nomor} dikeluarkan dari berkas.");
    }

    // ---------- Pembantu ----------

    private function aturan(): array
    {
        return [
            'nomor_surat'      => ['nullable', 'string', 'max:500'],
            'uraian'           => ['required', 'string'],
            'tanggal'          => ['nullable', 'date'],
            'tahun'            => ['nullable', 'integer', 'min:1900', 'max:' . (now()->year + 1)],
            'jumlah'           => ['nullable', 'integer', 'min:1'],
            'satuan'           => ['required', 'in:Berkas,Lembar,Sampul'],
            'skkad'            => ['required', 'in:Biasa/Terbuka,Terbatas,Rahasia,Sangat Rahasia'],
            'kode_klasifikasi' => ['nullable', 'exists:klasifikasi,kode_klasifikasi'],
            'keterangan'       => ['nullable', 'string'],
        ];
    }

    private function pesan(): array
    {
        return [
            'uraian.required'         => 'Uraian informasi arsip wajib diisi.',
            'kode_klasifikasi.exists' => 'Kode klasifikasi tidak terdaftar dalam JRA.',
        ];
    }

    // Kurun waktu diusulkan dari tahun item tertua dan termuda.
    // Arsiparis tetap bisa menimpanya lewat form ubah berkas.
    private function perbaruiKurunWaktu(Berkas $berkas): void
    {
        $tahun = $berkas->item()->with('arsip')->get()
            ->map(fn ($it) => $it->tahun_item)
            ->filter()
            ->values();

        if ($tahun->isEmpty()) return;

        $berkas->update([
            'tahun_mulai'   => $tahun->min(),
            'tahun_selesai' => $tahun->max(),
        ]);
    }

    // Nomor item dirapikan agar tidak berlubang setelah ada yang dikeluarkan
    private function rapikanNomor(Berkas $berkas): void
    {
        $nomor = 1;

        foreach ($berkas->item()->orderBy('nomor_item')->get() as $it) {
            if ($it->nomor_item !== $nomor) {
                $it->update(['nomor_item' => $nomor]);
            }
            $nomor++;
        }
    }

    private function pastikanBoleh(Berkas $berkas): void
    {
        $user = auth()->user();

        abort_if(
            ! $user->lihatSemuaUnit() && $berkas->unit_pengolah !== $user->unit_pengolah,
            403, 'Anda tidak memiliki akses ke berkas unit lain.'
        );

        abort_unless(
            $berkas->bolehDiubahOleh($user),
            403, 'Berkas sudah terverifikasi. Hubungi Unit Kearsipan untuk membuka kuncinya.'
        );
    }

    private function pastikanMilikBerkas(Berkas $berkas, ItemBerkas $item): void
    {
        abort_if($item->berkas_id !== $berkas->id, 404, 'Item tidak ditemukan dalam berkas ini.');
    }
}