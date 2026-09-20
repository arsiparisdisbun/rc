<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use App\Services\SaranKlasifikasi;

class SaranKlasifikasiController extends Controller
{
    public function untukArsip(Arsip $arsip, SaranKlasifikasi $layanan)
    {
        $user = auth()->user();

        if ($arsip->jenis === 'masuk') {
            abort_unless($user->bolehSuratMasuk(), 403);
        } else {
            abort_if(
                ! $user->lihatSemuaUnit() && $arsip->unit_pengolah !== $user->unit_pengolah,
                403
            );
        }

        if (! $arsip->dokumen_path) {
            return response()->json([
                'error' => 'Arsip ini belum punya dokumen PDF, sehingga tidak dapat dibaca isinya.',
            ], 422);
        }

        $hasil = $layanan->saranUntukArsip($arsip);

        if (empty($hasil['kandidat']) && empty($hasil['pilihan_acuan'])) {
            return response()->json([
                'error' => 'Tidak ditemukan saran. Kemungkinan dokumen ini hasil pindaian tanpa lapisan teks.',
            ], 422);
        }

        return response()->json($hasil);
    }
}