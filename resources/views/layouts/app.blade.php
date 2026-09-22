<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('judul', 'siarsip')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('gaya')
    <link href="{{ asset('css/siarsip.css') }}" rel="stylesheet">
</head>
<body class="bg-light">

@php
    $user = auth()->user();
    $superadmin = $user->can('kelola-master');
    $kearsipan  = $user->lihatSemuaUnit();
@endphp

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="{{ route('beranda') }}">siarsip</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav me-auto">
                @if($user->bolehSuratMasuk())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('surat-masuk.index') }}">Surat Masuk</a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('surat-keluar.index') }}">Surat Keluar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('berkas.index') }}">Berkas</a>
                </li>
                @if($kearsipan || $user->unit_pengolah === '121.1')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pegawai.index') }}">Kepegawaian</a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('keuangan.index') }}">Keuangan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('boks.index') }}">Boks</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('pemindahan.index') }}">Pemindahan</a>
                </li>
                @if($kearsipan)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('penyusutan.index') }}">Penyusutan</a>
                    </li>
                @endif

                {{-- Master: superadmin melihat semuanya, kearsipan cuma Riwayat --}}
                @if($superadmin)
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Master</a>
                        <ul class="dropdown-menu">
                            <li><h6 class="dropdown-header">Data Acuan</h6></li>
                            <li><a class="dropdown-item" href="{{ route('klasifikasi.index') }}">Jadwal Retensi Arsip</a></li>
                            <li><a class="dropdown-item" href="{{ route('unit-pengolah.index') }}">Unit Pengolah</a></li>
                            <li><a class="dropdown-item" href="{{ route('jenis-naskah.index') }}">Jenis Naskah</a></li>

                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">Daftar Pilihan</h6></li>
                            <li><a class="dropdown-item" href="{{ route('pengaturan.index', 'sub_bagian') }}">Sub Bagian</a></li>
                            <li><a class="dropdown-item" href="{{ route('pengaturan.index', 'kategori_keuangan') }}">Kategori Keuangan</a></li>
                            <li><a class="dropdown-item" href="{{ route('pengaturan.index', 'satuan') }}">Satuan Berkas</a></li>
                            <li><a class="dropdown-item" href="{{ route('pengaturan.index', 'skkad') }}">SKKAD</a></li>

                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('jejak.index') }}">Riwayat Perubahan</a></li>
                            <li><a class="dropdown-item" href="{{ route('akun.index') }}">Kelola Akun</a></li>
                        </ul>
                    </li>
                @elseif($kearsipan)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('jejak.index') }}">Riwayat</a>
                    </li>
                @endif
            </ul>

            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        {{ $user->name }}
                        <span class="badge bg-secondary ms-1">
                            {{ $user->unit_pengolah ?? ucfirst($user->peran) }}
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <span class="dropdown-item-text small text-muted">
                                {{ $user->unit?->nama ?? match($user->peran) {
                                    'superadmin' => 'Administrator Sistem',
                                    'kearsipan'  => 'Unit Kearsipan',
                                    default      => '-',
                                } }}
                            </span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('keluar') }}" method="POST">
                                @csrf
                                <button class="dropdown-item text-danger">Keluar</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid px-4 my-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    @yield('isi')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Tinggi navbar diukur otomatis agar header tabel yang melekat berhenti tepat di bawahnya
    function aturTinggiNavbar() {
        const nav = document.querySelector('.navbar');
        if (nav) document.documentElement.style.setProperty('--altura-navbar', nav.offsetHeight + 'px');
    }
    window.addEventListener('load', aturTinggiNavbar);
    window.addEventListener('resize', aturTinggiNavbar);
</script>
@stack('skrip')
</body>
</html>