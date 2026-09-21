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

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="{{ route('beranda') }}">siarsip</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav me-auto">
                @if(auth()->user()->bolehSuratMasuk())
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
                @if(auth()->user()->lihatSemuaUnit() || auth()->user()->unit_pengolah === '121.1')
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
                @if(auth()->user()->lihatSemuaUnit())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('penyusutan.index') }}">Penyusutan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('jejak.index') }}">Riwayat</a>
                    </li>
                @endif
                @can('kelola-akun')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('akun.index') }}">Kelola Akun</a>
                    </li>
                @endcan
            </ul>

            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        {{ auth()->user()->name }}
                        <span class="badge bg-secondary ms-1">
                            {{ auth()->user()->unit_pengolah ?? ucfirst(auth()->user()->peran) }}
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <span class="dropdown-item-text small text-muted">
                                {{ auth()->user()->unit?->nama ?? match(auth()->user()->peran) {
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