@extends('layouts.app')
@section('judul', 'Beranda - siarsip')

@section('isi')
    <h4 class="mb-1">Selamat datang, {{ auth()->user()->name }}</h4>
    <p class="text-muted">
        {{ auth()->user()->unit?->label ?? match(auth()->user()->peran) {
            'superadmin' => 'Administrator Sistem',
            'kearsipan'  => 'Unit Kearsipan',
            default      => '',
        } }}
    </p>

    <div class="row g-3 mt-2">
        @if(auth()->user()->bolehSuratMasuk())
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header fw-bold">Surat Masuk</div>
                    <div class="card-body">
                        <h2 class="mb-3">{{ number_format($ringkas['masuk_total']) }}</h2>
                        <p class="mb-1 small">Belum diklasifikasi: <strong>{{ number_format($ringkas['masuk_tanpa_kode']) }}</strong></p>
                        <p class="mb-3 small">Belum ada PDF: <strong>{{ number_format($ringkas['masuk_tanpa_pdf']) }}</strong></p>
                        <a href="{{ route('surat-masuk.index') }}" class="btn btn-sm btn-primary">Buka Buku Agenda</a>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header fw-bold">Surat Keluar</div>
                <div class="card-body">
                    <h2 class="mb-3">{{ number_format($ringkas['keluar_total']) }}</h2>
                    <p class="mb-1 small">Belum diklasifikasi: <strong>{{ number_format($ringkas['keluar_tanpa_kode']) }}</strong></p>
                    <p class="mb-3 small">Belum ada PDF: <strong>{{ number_format($ringkas['keluar_tanpa_pdf']) }}</strong></p>
                    <a href="{{ route('surat-keluar.index') }}" class="btn btn-sm btn-primary">Buka Buku Agenda</a>
                </div>
            </div>
        </div>
    </div>
@endsection