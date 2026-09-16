<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Arsip No {{ $arsip->no_urut }}/{{ $arsip->tahun }} - siarsip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .tabel-detail th { width: 220px; background: #f8f9fa; font-weight: 600; }
        .tabel-detail th, .tabel-detail td { border: 1px solid #dee2e6; padding: .6rem .75rem; }
    </style>
</head>
<body class="bg-light">
<div class="container my-4" style="max-width: 950px;">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Detail Arsip Surat Masuk</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('surat-masuk.index') }}" class="btn btn-outline-secondary">Kembali</a>
            <a href="{{ route('surat-masuk.edit', $arsip) }}" class="btn btn-warning">Edit</a>
        </div>
    </div>

    @php
        $status = $arsip->status_retensi;
        $warna = match(true) {
            $status === 'Kerja' => 'info',
            $status === 'Aktif' => 'success',
            $status === 'Siap inaktif' => 'warning',
            $status === 'Tidak diketahui' => 'secondary',
            default => 'danger',
        };
    @endphp

    <div class="card shadow-sm mb-3">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <span class="fw-bold">No Urut {{ $arsip->no_urut }} / Tahun {{ $arsip->tahun }}</span>
            <span class="badge bg-{{ $warna }} fs-6">{{ $status }}</span>
        </div>
        <div class="card-body p-0">
            <table class="table tabel-detail mb-0">
                <tr><th>No TNDE</th><td>{{ $arsip->no_tnde ?: '-' }}</td></tr>
                <tr><th>Tanggal Penerimaan</th><td>{{ $arsip->tanggal_penerimaan?->translatedFormat('d F Y') ?: '-' }}</td></tr>
                <tr><th>Tanggal Surat</th><td>{{ $arsip->tanggal_surat?->translatedFormat('d F Y') ?: '-' }}</td></tr>
                <tr><th>Nomor Surat</th><td>{{ $arsip->nomor_surat ?: '-' }}</td></tr>
                <tr><th>Sifat</th><td>{{ $arsip->sifat }}</td></tr>
                <tr><th>Lampiran</th><td>{{ $arsip->lampiran ?: '-' }}</td></tr>
                <tr><th>Isi Ringkas</th><td>{{ $arsip->isi_ringkas }}</td></tr>
                <tr><th>Dari</th><td>{{ $arsip->dari ?: '-' }}</td></tr>
                <tr><th>Kepada</th><td>{{ $arsip->kepada ?: '-' }}</td></tr>
                <tr><th>Tingkat Perkembangan</th><td>{{ $arsip->tingkat_perkembangan ?: '-' }}</td></tr>
                <tr><th>Unit Pengolah</th><td>{{ $arsip->unit?->label ?: '-' }}</td></tr>
                <tr><th>Lokasi Simpan Fisik</th><td>{{ $arsip->lokasi_simpan ?: '-' }}</td></tr>
                <tr>
                    <th>Dokumen Digital</th>
                    <td>
                        @if($arsip->dokumen_path)
                            <a href="{{ asset('storage/' . $arsip->dokumen_path) }}" target="_blank"
                               class="btn btn-sm btn-outline-danger">Buka PDF</a>
                        @else
                            <span class="text-muted">Belum ada dokumen</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header fw-bold">Klasifikasi & Jadwal Retensi Arsip</div>
        <div class="card-body p-0">
            @if($arsip->klasifikasi)
                <table class="table tabel-detail mb-0">
                    <tr><th>Kode Klasifikasi</th><td class="fw-bold">{{ $arsip->kode_klasifikasi }}</td></tr>
                    <tr><th>Uraian</th><td>{{ $arsip->klasifikasi->uraian }}</td></tr>
                    <tr><th>Retensi Aktif</th><td>{{ $arsip->klasifikasi->retensi_aktif }} tahun</td></tr>
                    <tr><th>Retensi Inaktif</th><td>{{ $arsip->klasifikasi->retensi_inaktif }} tahun</td></tr>
                    <tr><th>Nasib Akhir</th><td>{{ $arsip->klasifikasi->nasib_akhir }}</td></tr>
                    <tr class="table-light">
                        <th>Perhitungan mulai</th>
                        <td>1 Januari {{ $arsip->awal_retensi?->format('Y') ?? '-' }}
                            <small class="text-muted">(setahun setelah tahun surat)</small></td>
                    </tr>
                    <tr><th>Masa aktif berakhir</th><td>{{ $arsip->jatuh_tempo_inaktif?->translatedFormat('d F Y') ?? '-' }}</td></tr>
                    <tr><th>{{ $arsip->klasifikasi->nasib_akhir }} pada</th><td>{{ $arsip->jatuh_tempo_akhir?->translatedFormat('d F Y') ?? '-' }}</td></tr>
                </table>
            @else
                <div class="p-4 text-center">
                    <p class="text-muted mb-3">Arsip ini belum diklasifikasi, sehingga retensinya belum dapat dihitung.</p>
                    <a href="{{ route('surat-masuk.edit', $arsip) }}" class="btn btn-primary">Tentukan Kode Klasifikasi</a>
                </div>
            @endif
        </div>
    </div>
</div>
</body>
</html>