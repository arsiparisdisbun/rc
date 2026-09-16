<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use Illuminate\Support\Facades\Auth;

class BerandaController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $keluar = Arsip::where('jenis', 'keluar')
            ->when(! $user->lihatSemuaUnit(), fn ($q) => $q->where('unit_pengolah', $user->unit_pengolah));

        $ringkas = [
            'keluar_total'  => (clone $keluar)->count(),
            'keluar_tanpa_kode' => (clone $keluar)->whereNull('kode_klasifikasi')->count(),
            'keluar_tanpa_pdf'  => (clone $keluar)->whereNull('dokumen_path')->count(),
        ];

        if ($user->bolehSuratMasuk()) {
            $ringkas['masuk_total']      = Arsip::where('jenis', 'masuk')->count();
            $ringkas['masuk_tanpa_kode'] = Arsip::where('jenis', 'masuk')->whereNull('kode_klasifikasi')->count();
            $ringkas['masuk_tanpa_pdf']  = Arsip::where('jenis', 'masuk')->whereNull('dokumen_path')->count();
        }

        return view('beranda', compact('ringkas'));
    }
}