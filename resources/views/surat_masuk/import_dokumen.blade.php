@extends('layouts.app')
@section('judul', 'Unggah Dokumen Massal - siarsip')

@section('isi')
<div class="mx-auto" style="max-width: 800px;">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Unggah Dokumen Massal — Surat Masuk</h5>
        </div>
        <div class="card-body">

            @if(session('stat_dok'))
                @php $s = session('stat_dok'); @endphp
                <div class="alert alert-success">
                    <h6 class="fw-bold">Proses Selesai</h6>
                    <ul class="mb-0">
                        <li><strong>{{ number_format($s['cocok']) }}</strong> dokumen berhasil dilampirkan</li>
                        <li>{{ $s['ditimpa'] }} di antaranya menimpa dokumen lama</li>
                        <li>{{ $s['tak_cocok'] }} tidak menemukan arsip yang cocok</li>
                        <li>{{ $s['ganda'] }} dilewati karena No TNDE ganda</li>
                        <li>{{ $s['bukan_pdf'] }} bukan berkas PDF</li>
                    </ul>
                </div>

                @if(count(session('catatan', [])))
                    <details class="mb-3">
                        <summary class="text-muted">Lihat catatan berkas bermasalah</summary>
                        <ul class="small mt-2">
                            @foreach(session('catatan') as $c)<li>{{ $c }}</li>@endforeach
                        </ul>
                    </details>
                @endif
            @endif

            <div class="alert alert-warning small">
                <strong>Aturan penamaan berkas:</strong>
                <ul class="mb-0 mt-1">
                    <li><code>11320261238-1.pdf</code> — nama bawaan hasil unduhan dari TNDE, No TNDE dibaca otomatis (disarankan)</li>
                    <li><code>2026_1091.pdf</code> — tahun dan No TNDE</li>
                    <li><code>1091.pdf</code> — hanya No TNDE</li>
                </ul>
                Teks lain pada nama berkas diabaikan, jadi <code>TNDE-1091-scan.pdf</code> juga terbaca.
                Dokumen yang sudah ada akan diganti.
            </div>

            <form action="{{ route('surat-masuk.import-dokumen.proses') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="form-label fw-bold">Pilih berkas PDF (bisa banyak sekaligus)</label>
                    <input type="file" name="berkas[]" class="form-control" accept="application/pdf" multiple required>
                    <small class="text-muted">Tahan Ctrl untuk memilih banyak berkas, atau Ctrl+A untuk memilih semua isi folder.</small>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('surat-masuk.index') }}" class="btn btn-outline-secondary">Kembali</a>
                    <button class="btn btn-primary flex-grow-1">Proses Berkas</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection