<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Surat Masuk - siarsip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5" style="max-width: 800px;">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Import Surat Masuk dari Excel</h4>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                </div>
            @endif

            @if(session('stat'))
                @php $s = session('stat'); @endphp
                <div class="alert {{ session('simulasi') ? 'alert-info' : 'alert-success' }}">
                    <h5>{{ session('simulasi') ? 'Hasil Simulasi (tidak disimpan)' : 'Import Selesai' }}</h5>
                    <ul class="mb-0">
                        <li><strong>{{ $s['masuk'] }}</strong> baris {{ session('simulasi') ? 'siap diimpor' : 'berhasil diimpor' }}</li>
                        <li>{{ $s['lewat_ada'] }} dilewati (nomor agenda sudah ada di sistem)</li>
                        <li>{{ $s['lewat_rusak'] }} dilewati (tanggal tidak terbaca)</li>
                        <li>{{ $s['tanpa_kode'] }} tanpa kode klasifikasi</li>
                        <li>{{ $s['kode_asing'] }} kode tidak terdaftar di JRA (dikosongkan)</li>
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
                <strong>Cara menyiapkan berkas:</strong> buka file Excel, lalu
                <em>File → Save As → CSV UTF-8 (Comma delimited)</em>.
                Baris judul di atas tabel boleh dibiarkan, sistem akan mencari baris
                header "No Urut" secara otomatis.
            </div>

            <form action="{{ route('surat-masuk.import.proses') }}" method="POST" enctype="multipart/form-data">
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
                    <a href="{{ route('surat-masuk.index') }}" class="btn btn-outline-secondary">Kembali</a>
                    <button class="btn btn-primary flex-grow-1">Proses Berkas</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>