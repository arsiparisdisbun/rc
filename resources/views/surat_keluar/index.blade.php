<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Agenda Surat Keluar - siarsip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .tabel-agenda th, .tabel-agenda td { border: 1px solid #dee2e6; vertical-align: middle; }
        .tabel-agenda thead th { text-align: center; vertical-align: middle; }
        .pagination svg { width: 1rem; height: 1rem; }
    </style>
</head>
<body class="bg-light">
<div class="container-fluid px-4 my-4">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Buku Agenda Surat Keluar</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('surat-masuk.index') }}" class="btn btn-outline-secondary btn-sm">Surat Masuk</a>
            <a href="{{ route('surat-keluar.import') }}" class="btn btn-outline-primary btn-sm">Import Excel</a>
            <a href="{{ route('surat-keluar.create') }}" class="btn btn-primary btn-sm">+ Input Surat Keluar</a>
            <a href="{{ route('surat-keluar.import-dokumen') }}" class="btn btn-outline-primary btn-sm">Unggah Dokumen</a>
        </div>
    </div>

    @php
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
    @endphp

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-2">
            <input type="text" name="q" class="form-control" value="{{ request('q') }}"
                   placeholder="Cari nomor, isi ringkas, tujuan, pembuat...">
        </div>
        <div class="col-md-2">
            <select name="dok" class="form-select">
                <option value="">Semua dokumen</option>
                <option value="kosong" @selected(request('dok') === 'kosong')>Belum ada PDF</option>
                <option value="ada" @selected(request('dok') === 'ada')>Sudah ada PDF</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="unit" class="form-select">
                <option value="">Semua unit</option>
                @foreach($daftarUnit as $u)
                    <option value="{{ $u->kode }}" @selected(request('unit') === $u->kode)>{{ $u->kode }} — {{ Str::limit($u->nama, 22) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="jenis_naskah" class="form-select">
                <option value="">Semua jenis</option>
                @foreach($daftarJenis as $j)
                    <option value="{{ $j->id }}" @selected((string) request('jenis_naskah') === (string) $j->id)>{{ $j->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-1">
            <select name="tahun" class="form-select">
                <option value="semua" @selected($tahunAktif === 'semua')>Semua</option>
                @foreach($daftarTahun as $th)
                    <option value="{{ $th }}" @selected((string) $tahunAktif === (string) $th)>{{ $th }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="bulan" class="form-select">
                <option value="">Semua bulan</option>
                @foreach($namaBulan as $no => $nama)
                    <option value="{{ $no }}" @selected((string) request('bulan') === (string) $no)>{{ $nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-secondary flex-grow-1">Cari</button>
            <a href="{{ route('surat-keluar.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <p class="text-muted small mb-2">
        Menampilkan {{ number_format($arsip->total()) }} arsip
        @if($tahunAktif !== 'semua')— tahun {{ $tahunAktif }}@endif
        <span class="ms-2">Filter bulan mengacu pada tanggal surat.</span>
    </p>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped table-hover tabel-agenda mb-0" style="font-size: 0.9rem;">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 80px;">Nomor<br>Agenda</th>
                        <th style="width: 75px;">Unit</th>
                        <th style="width: 95px;">Tgl<br>Surat</th>
                        <th style="width: 175px;">Nomor Surat</th>
                        <th style="width: 120px;">Jenis</th>
                        <th>Isi Ringkas</th>
                        <th style="width: 150px;">Kepada</th>
                        <th style="width: 170px;">Klasifikasi</th>
                        <th style="width: 105px;">Status</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($arsip as $a)
                    <tr>
                        <td class="fw-bold text-center">
                            {{ $a->no_urut }}
                            @if($tahunAktif === 'semua')
                                <br><small class="text-muted fw-normal">{{ $a->tahun }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ $a->unit_pengolah ?: '-' }}</td>
                        <td class="text-center">{{ $a->tanggal_surat?->format('d/m/Y') }}</td>
                        <td>{{ $a->nomor_surat }}</td>
                        <td><small>{{ $a->jenisNaskah?->nama ?: '-' }}</small></td>
                        <td>
                            <a href="{{ route('surat-keluar.show', $a) }}" class="text-decoration-none text-dark">
                                {{ Str::limit($a->isi_ringkas, 90) }}
                            </a>
                        </td>
                        <td><small>{{ Str::limit($a->kepada, 35) }}</small></td>
                        <td>
                            @if($a->kode_klasifikasi)
                                <span class="fw-bold">{{ $a->kode_klasifikasi }}</span><br>
                                <small class="text-muted">{{ Str::limit($a->klasifikasi?->uraian, 35) }}</small>
                            @else
                                <span class="badge bg-light text-dark border">Belum diklasifikasi</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @php
                                $status = $a->status_retensi;
                                $warna = match(true) {
                                    $status === 'Kerja' => 'info',
                                    $status === 'Aktif' => 'success',
                                    $status === 'Siap inaktif' => 'warning',
                                    $status === 'Tidak diketahui' => 'secondary',
                                    default => 'danger',
                                };
                                $tip = $a->klasifikasi
                                    ? 'Mulai ' . ($a->awal_retensi?->format('Y') ?? '-')
                                      . ' · Inaktif ' . ($a->jatuh_tempo_inaktif?->format('Y') ?? '-')
                                      . ' · ' . $a->klasifikasi->nasib_akhir . ' ' . ($a->jatuh_tempo_akhir?->format('Y') ?? '-')
                                    : 'Belum diklasifikasi';
                            @endphp
                            <span class="badge bg-{{ $warna }}" data-bs-toggle="tooltip" title="{{ $tip }}">{{ $status }}</span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex flex-wrap gap-1 justify-content-center">
                                @if($a->dokumen_path)
                                    <a href="{{ asset('storage/' . $a->dokumen_path) }}" target="_blank"
                                       class="btn btn-sm btn-outline-danger" title="Buka PDF">PDF</a>
                                @else
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                            onclick="document.getElementById('unggah-{{ $a->id }}').click()"
                                            title="Unggah PDF">↑</button>
                                @endif
                                <a href="{{ route('surat-keluar.edit', $a) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('surat-keluar.destroy', $a) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus arsip nomor {{ $a->no_urut }} tahun {{ $a->tahun }}? Tindakan ini tidak dapat dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </div>

                            <form action="{{ route('surat-keluar.dokumen', $a) }}" method="POST"
                                  enctype="multipart/form-data" class="d-none" id="form-unggah-{{ $a->id }}">
                                @csrf
                                <input type="file" name="dokumen" accept="application/pdf" id="unggah-{{ $a->id }}"
                                       onchange="document.getElementById('form-unggah-{{ $a->id }}').submit()">
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="text-center py-4 text-muted">Belum ada data surat keluar.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $arsip->links() }}</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
            .forEach(el => new bootstrap.Tooltip(el));
</script>
</body>
</html>