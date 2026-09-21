<?php

namespace App\Http\Controllers;

use App\Models\Berkas;
use App\Models\UnitPengolah;
use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $berkas = Berkas::with(['unit', 'klasifikasi', 'boks'])
            ->withCount('item')
            ->whereNotNull('kategori_keuangan')
            ->when(! $user->lihatSemuaUnit(), fn ($q) => $q->where('unit_pengolah', $user->unit_pengolah))
            ->when($request->filled('unit'), fn ($q) => $q->where('unit_pengolah', $request->unit))
            ->when($request->filled('kategori'), fn ($q) => $q->where('kategori_keuangan', $request->kategori))
            ->when($request->filled('tahun'), fn ($q) => $q->where('tahun', $request->tahun))
            ->when($request->filled('q'), function ($q) use ($request) {
                $cari = $request->q;
                $q->where(fn ($s) => $s->where('uraian', 'like', "%{$cari}%")
                                       ->orWhere('kode_klasifikasi', 'like', "%{$cari}%"));
            })
            ->orderByDesc('tahun')->orderByDesc('no_berkas')
            ->paginate(25)->withQueryString();

        // Ringkasan dan daftar tahun mengikuti cakupan yang sama dengan yang
        // boleh dilihat pengguna — per unit untuk operator, semua untuk Kearsipan
        $cakupan = Berkas::whereNotNull('kategori_keuangan')
            ->when(! $user->lihatSemuaUnit(), fn ($q) => $q->where('unit_pengolah', $user->unit_pengolah));

        $ringkasan = (clone $cakupan)
            ->selectRaw('kategori_keuangan, COUNT(*) as jumlah')
            ->groupBy('kategori_keuangan')
            ->pluck('jumlah', 'kategori_keuangan');

        $daftarTahun = (clone $cakupan)->distinct()->orderByDesc('tahun')->pluck('tahun');

        // Filter unit hanya untuk Kearsipan/Superadmin — operator selalu
        // melihat unitnya sendiri saja, tidak perlu memilih
        $daftarUnit = $user->lihatSemuaUnit() ? UnitPengolah::aktif()->get() : null;

        return view('keuangan.index', compact('berkas', 'ringkasan', 'daftarTahun', 'daftarUnit'));
    }
}