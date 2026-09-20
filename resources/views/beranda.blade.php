@extends('layouts.app')
@section('judul', 'Beranda - siarsip')

@section('isi')
<div class="bingkai-ringkas">

@php
    $user = auth()->user();
    $kearsipan = $user->lihatSemuaUnit();
@endphp

<h4 class="mb-1">Selamat datang, {{ $user->name }}</h4>
<p class="text-muted">
    {{ $user->unit?->label ?? match($user->peran) {
        'superadmin' => 'Administrator Sistem',
        'kearsipan'  => 'Unit Kearsipan',
        default      => '',
    } }}
</p>

@php
    $adaTugas = $tugas['berkas_diajukan'] > 0 || $tugas['siap_pindah'] > 0 || $tugas['pemindahan_diajukan'] > 0;
@endphp

@if($adaTugas)
    <div class="card shadow-sm mb-4 border-warning">
        <div class="card-header bg-warning fw-bold">Perlu Ditindaklanjuti</div>
        <div class="list-group list-group-flush">
            @if($tugas['berkas_diajukan'] > 0)
                <a href="{{ route('berkas.index', ['status' => 'diajukan']) }}"
                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <span>
                        <span class="titik-hidup"></span>
                        <strong>{{ $tugas['berkas_diajukan'] }} berkas</strong>
                        {{ $kearsipan ? 'menunggu verifikasi Anda' : 'sedang menunggu verifikasi Unit Kearsipan' }}
                    </span>
                    <span class="badge bg-warning rounded-pill">{{ $tugas['berkas_diajukan'] }}</span>
                </a>
            @endif

            @if($tugas['pemindahan_diajukan'] > 0)
                <a href="{{ route('pemindahan.index', ['status' => 'diajukan']) }}"
                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <span>
                        <span class="titik-hidup"></span>
                        <strong>{{ $tugas['pemindahan_diajukan'] }} pengajuan penyerahan</strong>
                        {{ $kearsipan ? 'menunggu diterima' : 'sedang diproses Unit Kearsipan' }}
                    </span>
                    <span class="badge bg-warning rounded-pill">{{ $tugas['pemindahan_diajukan'] }}</span>
                </a>
            @endif

            @if($tugas['siap_pindah'] > 0)
                <a href="{{ $kearsipan ? route('berkas.index') : route('pemindahan.create') }}"
                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <span>
                        <span class="titik-hidup"></span>
                        <strong>{{ $tugas['siap_pindah'] }} berkas</strong>
                        sudah jatuh tempo inaktif dan belum diusulkan pindah
                    </span>
                    <span class="badge bg-danger rounded-pill">{{ $tugas['siap_pindah'] }}</span>
                </a>
            @endif
        </div>
    </div>
@endif

<h6 class="text-muted text-uppercase small fw-bold mb-2">Buku Agenda</h6>
<div class="row g-3 mb-4">
    @if($user->bolehSuratMasuk())
        <div class="col-md-6">
            <div class="card shadow-sm kartu-aksen h-100">
                <div class="card-header fw-bold">Surat Masuk</div>
                <div class="card-body">
                    <div class="angka-besar mb-3">{{ number_format($surat['masuk_total']) }}</div>
                    <p class="mb-1 small">
                        Belum diklasifikasi:
                        <strong>{{ number_format($surat['masuk_tanpa_kode']) }}</strong>
                        @if($surat['masuk_tanpa_kode'] > 0)
                            <a href="{{ route('surat-masuk.pengodean') }}" class="ms-1">kerjakan</a>
                        @endif
                    </p>
                    <p class="mb-3 small">
                        Belum ada PDF:
                        <strong>{{ number_format($surat['masuk_tanpa_pdf']) }}</strong>
                        @if($surat['masuk_tanpa_pdf'] > 0)
                            <a href="{{ route('surat-masuk.index', ['dok' => 'kosong']) }}" class="ms-1">lihat</a>
                        @endif
                    </p>
                    <a href="{{ route('surat-masuk.index') }}" class="btn btn-sm btn-primary">Buka Buku Agenda</a>
                </div>
            </div>
        </div>
    @endif

    <div class="col-md-6">
        <div class="card shadow-sm kartu-aksen h-100">
            <div class="card-header fw-bold">Surat Keluar</div>
            <div class="card-body">
                <div class="angka-besar mb-3">{{ number_format($surat['keluar_total']) }}</div>
                <p class="mb-1 small">
                    Belum diklasifikasi: <strong>{{ number_format($surat['keluar_tanpa_kode']) }}</strong>
                </p>
                <p class="mb-3 small">
                    Belum ada PDF:
                    <strong>{{ number_format($surat['keluar_tanpa_pdf']) }}</strong>
                    @if($surat['keluar_tanpa_pdf'] > 0)
                        <a href="{{ route('surat-keluar.index', ['dok' => 'kosong']) }}" class="ms-1">lihat</a>
                    @endif
                </p>
                <a href="{{ route('surat-keluar.index') }}" class="btn btn-sm btn-primary">Buka Buku Agenda</a>
            </div>
        </div>
    </div>
</div>

<h6 class="text-muted text-uppercase small fw-bold mb-2">Pemberkasan</h6>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm kartu-aksen h-100">
            <div class="card-header fw-bold">Status Verifikasi</div>
            <div class="card-body">
                <div class="angka-besar mb-3">
                    {{ number_format($berkas['total']) }} <small class="text-muted fs-6 fw-normal">berkas</small>
                </div>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <a href="{{ route('berkas.index', ['status' => 'draf']) }}" class="text-decoration-none">
                        <span class="badge bg-secondary">Draf: {{ $berkas['draf'] }}</span>
                    </a>
                    <a href="{{ route('berkas.index', ['status' => 'diajukan']) }}" class="text-decoration-none">
                        <span class="badge bg-warning">Menunggu: {{ $berkas['diajukan'] }}</span>
                    </a>
                    <a href="{{ route('berkas.index', ['status' => 'terverifikasi']) }}" class="text-decoration-none">
                        <span class="badge bg-success">Terverifikasi: {{ $berkas['terverifikasi'] }}</span>
                    </a>
                </div>
                <p class="mb-1 small">Belum diklasifikasi: <strong>{{ $berkas['tanpa_kode'] }}</strong></p>
                <p class="mb-3 small">Belum ditentukan boksnya: <strong>{{ $berkas['tanpa_boks'] }}</strong></p>
                <a href="{{ route('berkas.index') }}" class="btn btn-sm btn-primary">Buka Daftar Berkas</a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm kartu-aksen h-100">
            <div class="card-header fw-bold">Status Penyimpanan</div>
            <div class="card-body">
                @php $totalPenyimpanan = $penyimpanan->sum(); @endphp

                @if($totalPenyimpanan > 0)
                    <div class="bilah-retensi mb-2" role="img" aria-label="Sebaran status penyimpanan berkas">
                        @foreach($penyimpanan as $status => $jumlah)
                            @php
                                $persen = round($jumlah / $totalPenyimpanan * 100, 1);
                                $warna = match(true) {
                                    $status === 'Kerja'   => 'var(--tahap-kerja)',
                                    $status === 'Aktif'   => 'var(--tahap-aktif)',
                                    $status === 'Inaktif' => 'var(--tahap-inaktif)',
                                    $status === 'Tidak diketahui' => 'var(--tahap-selesai)',
                                    default => 'var(--tahap-akhir)',
                                };
                            @endphp
                                <a href="{{ route('berkas.index', ['penyimpanan' => $status]) }}"
                               style="flex: {{ $jumlah }} 0 0; background-color: {{ $warna }};"
                               title="{{ $status }}: {{ $jumlah }} berkas ({{ $persen }}%) — klik untuk membuka daftar"></a>
                        @endforeach
                    </div>

                    <div class="retensi-legenda mb-3">
                        @foreach($penyimpanan as $status => $jumlah)
                            @php
                                $warna = match(true) {
                                    $status === 'Kerja'   => 'var(--tahap-kerja)',
                                    $status === 'Aktif'   => 'var(--tahap-aktif)',
                                    $status === 'Inaktif' => 'var(--tahap-inaktif)',
                                    $status === 'Tidak diketahui' => 'var(--tahap-selesai)',
                                    default => 'var(--tahap-akhir)',
                                };
                            @endphp
                                <a href="{{ route('berkas.index', ['penyimpanan' => $status]) }}"
                               class="text-decoration-none" style="color: inherit;">
                                <span><i style="background-color: {{ $warna }};"></i>{{ $status }} ({{ $jumlah }})</span>
                                </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted small mb-3">Belum ada berkas yang dapat dihitung retensinya.</p>
                @endif

                <p class="small text-muted mb-0">
                    Dihitung otomatis dari kurun waktu berkas dan Jadwal Retensi Arsip.
                </p>
            </div>
        </div>
    </div>
</div>

@if($sistem)
    <h6 class="text-muted text-uppercase small fw-bold mb-2">Sistem</h6>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card shadow-sm kartu-aksen">
                <div class="card-body">
                    <p class="text-muted small mb-1">Akun Pengguna</p>
                    <div class="angka-besar mb-2" style="font-size: 1.6rem;">
                        {{ $sistem['akun_aktif'] }} <small class="text-muted fs-6 fw-normal">dari {{ $sistem['akun'] }} aktif</small>
                    </div>
                    <a href="{{ route('akun.index') }}" class="btn btn-sm btn-outline-primary">Kelola Akun</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm kartu-aksen">
                <div class="card-body">
                    <p class="text-muted small mb-1">Boks Arsip</p>
                    <div class="angka-besar mb-2" style="font-size: 1.6rem;">{{ $sistem['boks'] }}</div>
                    <a href="{{ route('boks.index') }}" class="btn btn-sm btn-outline-primary">Kelola Boks</a>
                </div>
            </div>
        </div>
    </div>
@endif

</div>
@endsection