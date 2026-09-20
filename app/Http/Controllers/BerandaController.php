<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use App\Models\Berkas;
use App\Models\Boks;
use App\Models\Pemindahan;
use App\Models\User;

class BerandaController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // ---------- Surat ----------
        $keluar = Arsip::where('jenis', 'keluar')
            ->when(! $user->lihatSemuaUnit(), fn ($q) => $q->where('unit_pengolah', $user->unit_pengolah));

        $surat = [
            'keluar_total'      => (clone $keluar)->count(),
            'keluar_tanpa_kode' => (clone $keluar)->whereNull('kode_klasifikasi')->count(),
            'keluar_tanpa_pdf'  => (clone $keluar)->whereNull('dokumen_path')->count(),
        ];

        if ($user->bolehSuratMasuk()) {
            $surat['masuk_total']      = Arsip::where('jenis', 'masuk')->count();
            $surat['masuk_tanpa_kode'] = Arsip::where('jenis', 'masuk')->whereNull('kode_klasifikasi')->count();
            $surat['masuk_tanpa_pdf']  = Arsip::where('jenis', 'masuk')->whereNull('dokumen_path')->count();
        }

        // ---------- Berkas ----------
        $berkasDasar = fn () => Berkas::query()
            ->when(! $user->lihatSemuaUnit(), fn ($q) => $q->where('unit_pengolah', $user->unit_pengolah));

        $berkas = [
            'total'         => $berkasDasar()->count(),
            'draf'          => $berkasDasar()->where('status', 'draf')->count(),
            'diajukan'      => $berkasDasar()->where('status', 'diajukan')->count(),
            'terverifikasi' => $berkasDasar()->where('status', 'terverifikasi')->count(),
            'tanpa_kode'    => $berkasDasar()->whereNull('kode_klasifikasi')->count(),
            'tanpa_boks'    => $berkasDasar()->whereNull('boks_id')->count(),
        ];

        // Status penyimpanan dihitung dari JRA, jadi harus dievaluasi di PHP
        $semuaBerkas = $berkasDasar()->with('klasifikasi')->get();

        $penyimpanan = $semuaBerkas->groupBy(fn ($b) => $b->status_penyimpanan)
            ->map->count()
            ->sortKeys();

        // Berkas terverifikasi yang sudah jatuh tempo tapi belum diusulkan pindah
        $siapPindah = $semuaBerkas
            ->where('status', 'terverifikasi')
            ->whereNull('pemindahan_id')
            ->filter(fn ($b) => $b->status_penyimpanan === 'Inaktif'
                             || str_starts_with($b->status_penyimpanan, 'Siap'));

        // ---------- Pemindahan ----------
        $pemindahan = Pemindahan::query()
            ->when(! $user->lihatSemuaUnit(), fn ($q) => $q->where('unit_pengolah', $user->unit_pengolah));

        $tugas = [
            'berkas_diajukan'     => $berkas['diajukan'],
            'siap_pindah'         => $siapPindah->count(),
            'pemindahan_diajukan' => (clone $pemindahan)->where('status', 'diajukan')->count(),
        ];

        // ---------- Khusus superadmin ----------
        $sistem = null;

        if ($user->superadmin()) {
            $sistem = [
                'akun'       => User::count(),
                'akun_aktif' => User::where('aktif', true)->count(),
                'boks'       => Boks::count(),
            ];
        }

        return view('beranda', compact('surat', 'berkas', 'penyimpanan', 'tugas', 'sistem'));
    }
}