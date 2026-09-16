<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SuratMasukController extends Controller
{
    
    public function index(Request $request)
    {
        // Acuan filter: tanggal penerimaan (urutan agenda) atau tanggal surat (kaidah retensi)
        $acuan = $request->input('acuan') === 'surat' ? 'tanggal_surat' : 'tanggal_penerimaan';

        $daftarTahun = Arsip::where('jenis', 'masuk')
            ->selectRaw("DISTINCT YEAR({$acuan}) as th")
            ->orderByDesc('th')
            ->pluck('th');

        $tahunAktif = $request->has('tahun')
            ? $request->input('tahun')
            : ($daftarTahun->first() ?? now()->year);

        $bulanAktif = $request->input('bulan');

        $arsip = Arsip::with('klasifikasi')
            ->where('jenis', 'masuk')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->q;
                $query->where(function ($sub) use ($q) {
                    $sub->where('nomor_surat', 'like', "%{$q}%")
                        ->orWhere('isi_ringkas', 'like', "%{$q}%")
                        ->orWhere('dari', 'like', "%{$q}%")
                        ->orWhere('kode_klasifikasi', 'like', "%{$q}%");
                });
            })
            ->when($tahunAktif !== 'semua' && $tahunAktif !== '',
                   fn ($q) => $q->whereYear($acuan, $tahunAktif))
            ->when($bulanAktif, fn ($q) => $q->whereMonth($acuan, $bulanAktif))
            ->when($request->input('dok') === 'ada', fn ($q) => $q->whereNotNull('dokumen_path'))
            ->when($request->input('dok') === 'kosong', fn ($q) => $q->whereNull('dokumen_path'))
            ->orderByDesc('tahun')
            ->orderByDesc('no_urut')
            ->paginate(20)
            ->withQueryString();

        return view('surat_masuk.index', compact('arsip', 'daftarTahun', 'tahunAktif', 'bulanAktif'))
            ->with('acuan', $request->input('acuan') === 'surat' ? 'surat' : 'terima');
    }
    public function create()
    {
        $tahun = Carbon::now()->year;
        $nextNoUrut = Arsip::where('jenis', 'masuk')->where('tahun', $tahun)->max('no_urut') + 1;

        return view('surat_masuk.create', compact('nextNoUrut'));
    }

        public function store(Request $request)
    {
        $data = $request->validate($this->aturanValidasi(), $this->pesanValidasi());
        $data = $this->simpanDokumen($request, $data);

        $data['jenis'] = 'masuk';
        $data['tahun'] = Carbon::parse($data['tanggal_penerimaan'])->year;

        $arsip = DB::transaction(function () use ($data) {
            $last = Arsip::where('jenis', 'masuk')
                ->where('tahun', $data['tahun'])
                ->lockForUpdate()
                ->max('no_urut');

            $data['no_urut'] = $last + 1;

            return Arsip::create($data);
        });

        return redirect()->route('surat-masuk.index')->with(
            'success',
            "Surat masuk tersimpan dengan No Urut {$arsip->no_urut} tahun {$arsip->tahun}."
        );
    }
        public function edit(Arsip $suratMasuk)
    {
        return view('surat_masuk.edit', ['arsip' => $suratMasuk]);
    }

    public function update(Request $request, Arsip $suratMasuk)
    {
        $data = $request->validate($this->aturanValidasi(), $this->pesanValidasi());
        $data = $this->simpanDokumen($request, $data, $suratMasuk);

        // No urut dan tahun sengaja tidak diubah agar urutan agenda tetap utuh
        $suratMasuk->update($data);

        return redirect()->route('surat-masuk.index')
            ->with('success', "Arsip No Urut {$suratMasuk->no_urut} tahun {$suratMasuk->tahun} berhasil diperbarui.");
    }

    public function destroy(Arsip $suratMasuk)
    {
        $info = "No Urut {$suratMasuk->no_urut} tahun {$suratMasuk->tahun}";

        if ($suratMasuk->dokumen_path) {
            Storage::disk('public')->delete($suratMasuk->dokumen_path);
        }

        $suratMasuk->delete();

        return redirect()->route('surat-masuk.index')
            ->with('success', "Arsip {$info} telah dihapus.");
    }

    public function cariKlasifikasi(Request $request)
    {
        $q = $request->input('q', '');

        return \App\Models\Klasifikasi::where('kode_klasifikasi', 'like', "{$q}%")
            ->orWhere('uraian', 'like', "%{$q}%")
            ->orderBy('kode_klasifikasi')
            ->limit(20)
            ->get()
            ->map(fn ($k) => [
                'id'   => $k->kode_klasifikasi,
                'text' => "{$k->kode_klasifikasi} — {$k->uraian} (aktif {$k->retensi_aktif} th, {$k->nasib_akhir})",
            ]);
    }

                private function aturanValidasi(): array
    {
        return [
            'no_tnde'              => ['nullable', 'string', 'max:50'],
            'tanggal_penerimaan'   => ['required', 'date', 'before_or_equal:today'],
            'tanggal_surat'        => ['required', 'date', 'before_or_equal:tanggal_penerimaan'],
            'nomor_surat'          => ['required', 'string', 'max:500'],
            'sifat'                => ['required', 'in:Biasa/Terbuka,Terbatas,Rahasia,Sangat Rahasia'],
            'lampiran'             => ['nullable', 'string', 'max:100'],
            'isi_ringkas'          => ['required', 'string'],
            'dari'                 => ['required', 'string'],
            'kepada'               => ['required', 'string'],
            'tingkat_perkembangan' => ['nullable', 'in:Asli,Copy,Salinan,Tembusan'],
            'unit_pengolah'        => ['nullable', 'exists:unit_pengolah,kode'],
            'kode_klasifikasi'     => ['nullable', 'exists:klasifikasi,kode_klasifikasi'],
            'lokasi_simpan'        => ['nullable', 'string', 'max:255'],
            'dokumen'              => ['nullable', 'file', 'max:20480', function ($attribute, $value, $fail) {
                $handle = fopen($value->getRealPath(), 'rb');
                $awal = fread($handle, 1024);
                fclose($handle);

                if (! str_contains($awal, '%PDF-')) {
                    $fail('File yang diunggah bukan dokumen PDF yang valid.');
                }
            }],
        ];
    }

    private function pesanValidasi(): array
    {
        return [
            'kode_klasifikasi.exists'       => 'Kode klasifikasi tidak terdaftar dalam JRA.',
            'tanggal_surat.before_or_equal' => 'Tanggal surat tidak boleh setelah tanggal penerimaan.',
        ];
    }

    private function simpanDokumen(Request $request, array $data, ?Arsip $arsip = null): array
    {
        if (! $request->hasFile('dokumen')) {
            unset($data['dokumen']);
            return $data;
        }

        // Hapus file lama agar tidak menumpuk di storage
        if ($arsip && $arsip->dokumen_path) {
            Storage::disk('public')->delete($arsip->dokumen_path);
        }

        $nama = 'SM_' . Carbon::parse($request->tanggal_penerimaan)->year
              . '_' . str_replace(['/', '\\'], '-', $request->nomor_surat)
              . '_' . now()->format('His') . '.pdf';

        $data['dokumen_path'] = $request->file('dokumen')->storeAs('dokumen_arsip', $nama, 'public');
        unset($data['dokumen']);

        return $data;
    }
        public function unggahDokumen(Request $request, Arsip $suratMasuk)
    {
        $request->validate([
            'dokumen' => ['required', 'file', 'max:20480', function ($attribute, $value, $fail) {
                $handle = fopen($value->getRealPath(), 'rb');
                $awal = fread($handle, 1024);
                fclose($handle);

                if (! str_contains($awal, '%PDF-')) {
                    $fail('File yang diunggah bukan dokumen PDF yang valid.');
                }
            }],
        ]);

        // Ganti file lama agar tidak menumpuk di storage
        if ($suratMasuk->dokumen_path) {
            Storage::disk('public')->delete($suratMasuk->dokumen_path);
        }

        $nama = 'SM_' . $suratMasuk->tahun
              . '_' . str_replace(['/', '\\'], '-', $suratMasuk->nomor_surat ?? 'tanpa-nomor')
              . '_' . now()->format('His') . '.pdf';

        $suratMasuk->update([
            'dokumen_path' => $request->file('dokumen')->storeAs('dokumen_arsip', $nama, 'public'),
        ]);

        return back()->with('success', "Dokumen untuk No Urut {$suratMasuk->no_urut} tahun {$suratMasuk->tahun} berhasil diunggah.");
    }
        public function show(Arsip $suratMasuk)
    {
        $suratMasuk->load(['klasifikasi', 'unit']);
        return view('surat_masuk.show', ['arsip' => $suratMasuk]);
    }
        
}