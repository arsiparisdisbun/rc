<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use App\Models\JenisNaskah;
use App\Models\UnitPengolah;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SuratKeluarController extends Controller
{
    public function index(Request $request)
    {
        $daftarTahun = Arsip::where('jenis', 'keluar')
            ->distinct()->orderByDesc('tahun')->pluck('tahun');

        $tahunAktif = $request->has('tahun')
            ? $request->input('tahun')
            : ($daftarTahun->first() ?? now()->year);

        $arsip = Arsip::with(['klasifikasi', 'unit', 'jenisNaskah'])
            ->where('jenis', 'keluar')
            // Operator hanya melihat arsip unitnya sendiri
            ->when(! auth()->user()->lihatSemuaUnit(),
                   fn ($q) => $q->where('unit_pengolah', auth()->user()->unit_pengolah))
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->q;
                $query->where(function ($sub) use ($q) {
                    $sub->where('nomor_surat', 'like', "%{$q}%")
                        ->orWhere('isi_ringkas', 'like', "%{$q}%")
                        ->orWhere('kepada', 'like', "%{$q}%")
                        ->orWhere('pembuat', 'like', "%{$q}%")
                        ->orWhere('kode_klasifikasi', 'like', "%{$q}%");
                });
            })
            ->when($tahunAktif !== 'semua' && $tahunAktif !== '',
                   fn ($q) => $q->where('tahun', $tahunAktif))
            ->when($request->filled('unit'), fn ($q) => $q->where('unit_pengolah', $request->unit))
            ->when($request->filled('bulan'), fn ($q) => $q->whereMonth('tanggal_surat', $request->bulan))
            ->when($request->filled('jenis_naskah'), fn ($q) => $q->where('jenis_naskah_id', $request->jenis_naskah))
            ->when($request->input('dok') === 'ada', fn ($q) => $q->whereNotNull('dokumen_path'))
            ->when($request->input('dok') === 'kosong', fn ($q) => $q->whereNull('dokumen_path'))
            ->orderByDesc('tahun')
            ->orderByDesc('no_urut')
            ->paginate(25)
            ->withQueryString();

        return view('surat_keluar.index', [
            'arsip'       => $arsip,
            'daftarTahun' => $daftarTahun,
            'tahunAktif'  => $tahunAktif,
            'daftarUnit'  => auth()->user()->lihatSemuaUnit()
                    ? UnitPengolah::aktif()->get()
                    : UnitPengolah::where('kode', auth()->user()->unit_pengolah)->get(),
            'daftarJenis' => JenisNaskah::aktif()->get(),
        ]);
    }

    public function create()
    {
        return view('surat_keluar.create', [
            'daftarUnit' => auth()->user()->lihatSemuaUnit()
                   ? UnitPengolah::aktif()->get()
                   : UnitPengolah::where('kode', auth()->user()->unit_pengolah)->get(),
            'daftarJenis' => JenisNaskah::aktif()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturan(), $this->pesan());
        // Operator hanya boleh menyimpan untuk unitnya sendiri
        abort_if(
            ! auth()->user()->lihatSemuaUnit() && $data['unit_pengolah'] !== auth()->user()->unit_pengolah,
            403,
            'Anda tidak dapat membuat arsip untuk unit lain.'
        );
        $data = $this->simpanDokumen($request, $data);

        $data['jenis'] = 'keluar';
        // Tahun mengikuti tanggal surat, sama dengan acuan nomor agenda
        $data['tahun'] = Carbon::parse($data['tanggal_surat'])->year;

        $arsip = DB::transaction(function () use ($data, $request) {
            // Nomor boleh ditimpa manual bila TNDE sudah menerbitkan nomornya
            $data['no_urut'] = $request->filled('no_urut')
                ? (int) $request->no_urut
                : Arsip::nomorKeluarBerikutnya($data['unit_pengolah'], $data['tanggal_surat']);

            return Arsip::create($data);
        });

        return redirect()->route('surat-keluar.index')
            ->with('success', "Surat keluar tersimpan dengan nomor agenda {$arsip->no_urut} tahun {$arsip->tahun}.");
    }

    public function show(Arsip $suratKeluar)
    {
        $this->pastikanBoleh($suratKeluar);
        $suratKeluar->load(['klasifikasi', 'unit', 'jenisNaskah']);
        return view('surat_keluar.show', ['arsip' => $suratKeluar]);
    }

    public function edit(Arsip $suratKeluar)
    {
        $this->pastikanBoleh($suratKeluar);
        return view('surat_keluar.edit', [
            'arsip'       => $suratKeluar,
            'daftarUnit' => auth()->user()->lihatSemuaUnit()
                ? UnitPengolah::aktif()->get()
                : UnitPengolah::where('kode', auth()->user()->unit_pengolah)->get(),
            'daftarJenis' => JenisNaskah::aktif()->get(),
        ]);
    }

    public function update(Request $request, Arsip $suratKeluar)
    {
        $this->pastikanBoleh($suratKeluar);
        $data = $request->validate($this->aturan(), $this->pesan());
        $data = $this->simpanDokumen($request, $data, $suratKeluar);

        // Nomor agenda dan tahun tidak diubah agar urutan register tetap utuh
        $suratKeluar->update($data);

        return redirect()->route('surat-keluar.index')
            ->with('success', "Arsip nomor {$suratKeluar->no_urut} tahun {$suratKeluar->tahun} berhasil diperbarui.");
    }

    public function destroy(Arsip $suratKeluar)
    {
        $this->pastikanBoleh($suratKeluar);
        $info = "nomor {$suratKeluar->no_urut} tahun {$suratKeluar->tahun}";

        if ($suratKeluar->dokumen_path) {
            Storage::disk('public')->delete($suratKeluar->dokumen_path);
        }

        $suratKeluar->delete();

        return redirect()->route('surat-keluar.index')->with('success', "Arsip {$info} telah dihapus.");
    }

    // Dipanggil AJAX dari form untuk menampilkan usulan nomor
    public function usulNomor(Request $request)
    {
        $request->validate([
            'unit_pengolah' => ['required', 'exists:unit_pengolah,kode'],
            'tanggal_surat' => ['required', 'date'],
        ]);

        return response()->json([
            'nomor' => Arsip::nomorKeluarBerikutnya($request->unit_pengolah, $request->tanggal_surat),
        ]);
    }

    private function aturan(): array
    {
        return [
            'jenis_naskah_id'      => ['required', 'exists:jenis_naskah,id'],
            'sifat'                => ['required', 'in:Biasa/Terbuka,Terbatas,Rahasia,Sangat Rahasia'],
            'tanggal_upload'       => ['nullable', 'date'],
            'tanggal_verifikasi'   => ['nullable', 'date'],
            'nomor_surat'          => ['required', 'string', 'max:500'],
            'kode_tnde'            => ['nullable', 'string', 'max:50'],
            'kode_klasifikasi'     => ['nullable', 'exists:klasifikasi,kode_klasifikasi'],
            'no_urut'              => ['nullable', 'integer', 'min:1'],
            'tanggal_surat'        => ['required', 'date'],
            'jumlah_lembar'        => ['nullable', 'integer', 'min:1', 'max:9999'],
            'unit_pengolah'        => ['required', 'exists:unit_pengolah,kode'],
            'isi_ringkas'          => ['required', 'string'],
            'kepada'               => ['required', 'string'],
            'tingkat_perkembangan' => ['nullable', 'in:Asli,Salinan'],
            'pembuat'              => ['nullable', 'string'],
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

    private function pesan(): array
    {
        return [
            'kode_klasifikasi.exists' => 'Kode klasifikasi tidak terdaftar dalam JRA.',
            'unit_pengolah.required'  => 'Unit pengolah wajib dipilih karena menentukan nomor agenda.',
        ];
    }

    private function simpanDokumen(Request $request, array $data, ?Arsip $arsip = null): array
    {
        if (! $request->hasFile('dokumen')) {
            unset($data['dokumen']);
            return $data;
        }

        if ($arsip && $arsip->dokumen_path) {
            Storage::disk('public')->delete($arsip->dokumen_path);
        }

        $nama = 'SK_' . Carbon::parse($request->tanggal_surat)->year
              . '_' . str_replace(['/', '\\'], '-', $request->nomor_surat)
              . '_' . now()->format('His') . '.pdf';

        $data['dokumen_path'] = $request->file('dokumen')->storeAs('dokumen_arsip', $nama, 'public');
        unset($data['dokumen']);

        return $data;
    }
        public function unggahDokumen(Request $request, Arsip $suratKeluar)
    {
        $this->pastikanBoleh($suratKeluar);
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
        if ($suratKeluar->dokumen_path) {
            Storage::disk('public')->delete($suratKeluar->dokumen_path);
        }

        $nama = 'SK_' . $suratKeluar->tahun
              . '_' . str_replace('.', '', $suratKeluar->unit_pengolah ?? 'x')
              . '_' . $suratKeluar->no_urut
              . '_' . now()->format('His') . '.pdf';

        $suratKeluar->update([
            'dokumen_path' => $request->file('dokumen')->storeAs('dokumen_arsip', $nama, 'public'),
        ]);

        return back()->with('success', "Dokumen untuk nomor agenda {$suratKeluar->no_urut} berhasil diunggah.");
    }
        // Operator tidak boleh menyentuh arsip unit lain
    private function pastikanBoleh(Arsip $arsip): void
    {
        $user = auth()->user();

        abort_if(
            ! $user->lihatSemuaUnit() && $arsip->unit_pengolah !== $user->unit_pengolah,
            403,
            'Anda tidak memiliki akses ke arsip unit lain.'           
        );
    }
        public function cetak(Request $request)
    {
        $user = auth()->user();

        $arsip = Arsip::with(['klasifikasi', 'unit', 'jenisNaskah'])
            ->where('jenis', 'keluar')
            ->when(! $user->lihatSemuaUnit(), fn ($q) => $q->where('unit_pengolah', $user->unit_pengolah))
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->q;
                $query->where(function ($sub) use ($q) {
                    $sub->where('nomor_surat', 'like', "%{$q}%")
                        ->orWhere('isi_ringkas', 'like', "%{$q}%")
                        ->orWhere('kepada', 'like', "%{$q}%")
                        ->orWhere('pembuat', 'like', "%{$q}%")
                        ->orWhere('kode_klasifikasi', 'like', "%{$q}%");
                });
            })
            ->when($request->filled('unit'), fn ($q) => $q->where('unit_pengolah', $request->unit))
            ->when($request->filled('tahun') && $request->tahun !== 'semua',
                   fn ($q) => $q->where('tahun', $request->tahun))
            ->when($request->filled('bulan'), fn ($q) => $q->whereMonth('tanggal_surat', $request->bulan))
            ->when($request->filled('jenis_naskah'), fn ($q) => $q->where('jenis_naskah_id', $request->jenis_naskah))
            ->when($request->input('dok') === 'ada', fn ($q) => $q->whereNotNull('dokumen_path'))
            ->when($request->input('dok') === 'kosong', fn ($q) => $q->whereNull('dokumen_path'))
            ->orderBy('unit_pengolah')->orderBy('tahun')->orderBy('no_urut')
            ->get();

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $unitKode = $arsip->pluck('unit_pengolah')->unique();

        $namaUnit = $unitKode->count() === 1
            ? optional($arsip->first()->unit)->label
            : 'Semua Unit Pengolah';

        $periode = collect([
            $request->filled('bulan') ? ($namaBulan[(int) $request->bulan] ?? null) : null,
            $request->filled('tahun') && $request->tahun !== 'semua' ? 'Tahun ' . $request->tahun : null,
        ])->filter()->join(' ');

        return view('surat_keluar.cetak', compact('arsip', 'periode', 'namaUnit'));
    }
        use \App\Traits\EksporExcel;

    public function eksporExcel(Request $request)
    {
        $user = auth()->user();

        $arsip = Arsip::with(['klasifikasi', 'unit', 'jenisNaskah'])
            ->where('jenis', 'keluar')
            ->when(! $user->lihatSemuaUnit(), fn ($q) => $q->where('unit_pengolah', $user->unit_pengolah))
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->q;
                $query->where(function ($sub) use ($q) {
                    $sub->where('nomor_surat', 'like', "%{$q}%")
                        ->orWhere('isi_ringkas', 'like', "%{$q}%")
                        ->orWhere('kepada', 'like', "%{$q}%")
                        ->orWhere('pembuat', 'like', "%{$q}%")
                        ->orWhere('kode_klasifikasi', 'like', "%{$q}%");
                });
            })
            ->when($request->filled('unit'), fn ($q) => $q->where('unit_pengolah', $request->unit))
            ->when($request->filled('tahun') && $request->tahun !== 'semua',
                   fn ($q) => $q->where('tahun', $request->tahun))
            ->when($request->filled('bulan'), fn ($q) => $q->whereMonth('tanggal_surat', $request->bulan))
            ->when($request->filled('jenis_naskah'), fn ($q) => $q->where('jenis_naskah_id', $request->jenis_naskah))
            ->when($request->input('dok') === 'ada', fn ($q) => $q->whereNotNull('dokumen_path'))
            ->when($request->input('dok') === 'kosong', fn ($q) => $q->whereNull('dokumen_path'))
            ->orderBy('unit_pengolah')->orderBy('tahun')->orderBy('no_urut')
            ->get();

        $kolom = ['No Agenda', 'Tahun', 'Kode Unit', 'Nama Unit', 'Jenis Naskah', 'Sifat',
                  'Tgl Verifikasi', 'Nomor Surat', 'Kode TNDE', 'Kode Klasifikasi',
                  'Uraian Klasifikasi', 'Tgl Surat', 'Jumlah Lembar', 'Isi Ringkas',
                  'Kepada', 'Tingkat Perkembangan', 'Pembuat', 'Status Retensi', 'Ada Dokumen'];

        $baris = $arsip->map(fn ($a) => [
            $a->no_urut, $a->tahun, $a->unit_pengolah, $a->unit?->nama,
            $a->jenisNaskah?->nama, $a->sifat,
            $a->tanggal_verifikasi?->format('d-m-Y'),
            $a->nomor_surat, $a->kode_tnde, $a->kode_klasifikasi, $a->klasifikasi?->uraian,
            $a->tanggal_surat?->format('d-m-Y'), $a->jumlah_lembar, $a->isi_ringkas,
            $a->kepada, $a->tingkat_perkembangan, $a->pembuat,
            $a->status_retensi, $a->dokumen_path ? 'Ya' : 'Tidak',
        ]);

        return $this->unduhExcel(
            'Surat Keluar', $kolom, $baris,
            'Surat_Keluar_' . now()->format('Y-m-d_His') . '.xlsx',
            ['K' => 35, 'N' => 45]
        );
    }

}