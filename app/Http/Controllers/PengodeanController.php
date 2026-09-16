<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use Illuminate\Http\Request;

class PengodeanController extends Controller
{
    public function index(Request $request)
    {
        $total     = Arsip::where('jenis', 'masuk')->count();
        $berkode   = Arsip::where('jenis', 'masuk')->whereNotNull('kode_klasifikasi')->count();
        $belum     = $total - $berkode;
        $persen    = $total > 0 ? round($berkode / $total * 100, 1) : 0;

        $arsip = Arsip::where('jenis', 'masuk')
            ->whereNull('kode_klasifikasi')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->q;
                $query->where(function ($sub) use ($q) {
                    $sub->where('isi_ringkas', 'like', "%{$q}%")
                        ->orWhere('dari', 'like', "%{$q}%")
                        ->orWhere('nomor_surat', 'like', "%{$q}%");
                });
            })
            ->orderBy('tahun')
            ->orderBy('no_urut')
            ->paginate(25)
            ->withQueryString();

        return view('surat_masuk.pengodean', compact('arsip', 'total', 'berkode', 'belum', 'persen'));
    }

    // Menyimpan kode satu arsip lewat AJAX, tanpa memuat ulang halaman
    public function simpan(Request $request, Arsip $arsip)
    {
        $data = $request->validate([
            'kode_klasifikasi' => ['required', 'exists:klasifikasi,kode_klasifikasi'],
        ], [
            'kode_klasifikasi.exists' => 'Kode tidak terdaftar dalam JRA.',
        ]);

        $arsip->update($data);
        $arsip->load('klasifikasi');

        return response()->json([
            'ok'      => true,
            'uraian'  => $arsip->klasifikasi?->uraian,
            'status'  => $arsip->status_retensi,
            'inaktif' => $arsip->jatuh_tempo_inaktif?->format('Y'),
            'akhir'   => $arsip->jatuh_tempo_akhir?->format('Y'),
            'nasib'   => $arsip->klasifikasi?->nasib_akhir,
        ]);
    }
        
}