@extends('layouts.app')
@section('judul', 'Import Buku Agenda Surat Keluar - siarsip')

@section('isi')
<div class="mx-auto" style="max-width: 800px;">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Import Buku Agenda Surat Keluar</h5>
        </div>
        <div class="card-body">

            @if(session('stat'))
                @php $s = session('stat'); @endphp
                <div class="alert {{ session('simulasi') ? 'alert-info' : 'alert-success' }}">
                    <h6 class="fw-bold">{{ session('simulasi') ? 'Hasil Simulasi (tidak disimpan)' : 'Import Selesai' }}</h6>
                    <ul class="mb-0">
                        <li><strong>{{ number_format($s['masuk']) }}</strong> baris {{ session('simulasi') ? 'siap diimpor' : 'berhasil diimpor' }}</li>
                        <li>{{ number_format($s['tanpa_verif']) }} dilewati (belum ada tanggal verifikasi)</li>
                        <li>{{ $s['tanpa_nomor'] }} dilewati (tanpa nomor agenda)</li>
                        <li>{{ number_format($s['sudah_ada']) }} dilewati (sudah ada di sistem)</li>
                        <li>{{ $s['unit_asing'] }} dilewati (unit tidak terdaftar)</li>
                        <li>{{ $s['jenis_asing'] }} jenis naskah tidak dikenal (tetap diimpor)</li>
                        <li>{{ $s['kode_asing'] }} kode tidak ada di JRA (dikosongkan)</li>
                    </ul>
                </div>

                @if(count(session('catatan', [])))
                    <details class="mb-3">
                        <summary class="text-muted">Lihat catatan ({{ count(session('catatan')) }} baris pertama)</summary>
                        <ul class="small mt-2">
                            @foreach(session('catatan') as $c)<li>{{ $c }}</li>@endforeach
                        </ul>
                    </details>
                @endif
            @endif

            <div class="alert alert-warning small">
                <strong>Cara menyiapkan berkas:</strong> buka file register (.xls) di Excel, lalu
                <em>File → Save As → CSV UTF-8 (Comma delimited)</em>.
                Unggah satu berkas per proses; ulangi untuk tiap file register.
                <hr class="my-2">
                Hanya baris yang <strong>sudah punya tanggal verifikasi</strong> dan
                <strong>punya nomor agenda</strong> yang akan diimpor.
            </div>

            <form action="{{ route('surat-keluar.import.proses') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">Berkas CSV</label>
                    <input type="file" name="berkas" class="form-control" accept=".csv,text/csv" required>
                </div>

                <div class="form-check mb-4">
                    <input type="checkbox" name="simulasi" value="1" class="form-check-input" id="sim" checked>
                    <label class="form-check-label" for="sim">
                        Jalankan sebagai simulasi dulu (tidak menyimpan apa pun)
                    </label>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('surat-keluar.index') }}" class="btn btn-outline-secondary">Kembali</a>
                    <button class="btn btn-primary flex-grow-1">Proses Berkas</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection