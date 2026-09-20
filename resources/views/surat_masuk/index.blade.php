@extends('layouts.app')
@section('judul', 'Buku Agenda Surat Masuk - siarsip')

@push('gaya')
<style>
    .tabel-agenda th, .tabel-agenda td { border: 1px solid #dee2e6; vertical-align: middle; }
    .tabel-agenda thead th { text-align: center; vertical-align: middle; }
</style>
@endpush

@section('isi')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Buku Agenda Surat Masuk</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('surat-masuk.import') }}" class="btn btn-outline-primary btn-sm">Import Excel</a>
            <a href="{{ route('surat-masuk.create') }}" class="btn btn-primary btn-sm">+ Input Surat Masuk</a>
            <a href="{{ route('surat-masuk.import-dokumen') }}" class="btn btn-outline-primary btn-sm">Unggah Dokumen</a>
            <a href="{{ route('surat-masuk.pengodean') }}" class="btn btn-outline-success btn-sm">Pengodean</a>
            <a href="{{ route('surat-masuk.cetak', request()->query()) }}" target="_blank" class="btn btn-outline-secondary btn-sm">Cetak</a>
            <a href="{{ route('surat-masuk.ekspor-excel', request()->query()) }}" class="btn btn-outline-secondary btn-sm">Ekspor Excel</a>

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
        <div class="col-md-3">
            <input type="text" name="q" class="form-control" value="{{ request('q') }}"
                   placeholder="Cari nomor surat, isi ringkas, pengirim, atau kode...">
        </div>
        <div class="col-md-2">
            <select name="dok" class="form-select">
                <option value="">Semua dokumen</option>
                <option value="kosong" @selected(request('dok') === 'kosong')>Belum ada PDF</option>
                <option value="ada" @selected(request('dok') === 'ada')>Sudah ada PDF</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="acuan" class="form-select">
                <option value="terima" @selected($acuan === 'terima')>Tgl Terima</option>
                <option value="surat" @selected($acuan === 'surat')>Tgl Surat</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="tahun" class="form-select">
                <option value="semua" @selected($tahunAktif === 'semua')>Semua tahun</option>
                @foreach($daftarTahun as $th)
                    <option value="{{ $th }}" @selected((string) $tahunAktif === (string) $th)>Tahun {{ $th }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="bulan" class="form-select">
                <option value="">Semua bulan</option>
                @foreach($namaBulan as $no => $nama)
                    <option value="{{ $no }}" @selected((string) $bulanAktif === (string) $no)>{{ $nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-1 d-flex gap-2">
            <button class="btn btn-secondary flex-grow-1">Cari</button>
        </div>
    </form>

    <p class="text-muted small mb-2">
        Menampilkan {{ number_format($arsip->total()) }} arsip
        (acuan: {{ $acuan === 'surat' ? 'tanggal surat' : 'tanggal terima' }})
        @if($tahunAktif !== 'semua')— tahun {{ $tahunAktif }}@endif
        @if($bulanAktif), bulan {{ $namaBulan[(int) $bulanAktif] ?? '' }}@endif
        <a href="{{ route('surat-masuk.index') }}" class="ms-2">Reset filter</a>
    </p>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped table-hover tabel-agenda mb-0" style="font-size: 0.9rem;">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 65px;">No<br>Urut</th>
                        <th style="width: 95px;">Tgl<br>Terima</th>
                        <th style="width: 180px;">Nomor Surat</th>
                        <th>Isi Ringkas</th>
                        <th style="width: 170px;">Dari</th>
                        <th style="width: 190px;">Klasifikasi</th>
                        <th style="width: 110px;">Status</th>
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
                        <td class="text-center">{{ $a->tanggal_penerimaan?->format('d/m/Y') }}</td>
                        <td>{{ $a->nomor_surat }}</td>
                        <td>
                            <a href="{{ route('surat-masuk.show', $a) }}" class="text-decoration-none text-dark">
                                {{ Str::limit($a->isi_ringkas, 100) }}
                            </a>
                        </td>
                        <td>{{ Str::limit($a->dari, 40) }}</td>
                        <td>
                            @if($a->kode_klasifikasi)
                                <span class="fw-bold">{{ $a->kode_klasifikasi }}</span><br>
                                <small class="text-muted">{{ Str::limit($a->klasifikasi?->uraian, 40) }}</small>
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
                                <a href="{{ route('surat-masuk.edit', $a) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('surat-masuk.destroy', $a) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus arsip No Urut {{ $a->no_urut }} tahun {{ $a->tahun }}? Tindakan ini tidak dapat dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </div>

                            <form action="{{ route('surat-masuk.dokumen', $a) }}" method="POST"
                                  enctype="multipart/form-data" class="d-none" id="form-unggah-{{ $a->id }}">
                                @csrf
                                <input type="file" name="dokumen" accept="application/pdf" id="unggah-{{ $a->id }}"
                                       onchange="document.getElementById('form-unggah-{{ $a->id }}').submit()">
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada arsip yang cocok dengan filter ini.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $arsip->links() }}</div>
@endsection

@push('skrip')
<script>
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
            .forEach(el => new bootstrap.Tooltip(el));
</script>
@endpush