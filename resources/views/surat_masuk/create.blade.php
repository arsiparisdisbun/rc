<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Surat Masuk - siarsip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5" style="max-width: 900px;">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Form Input Surat Masuk</h4>
        </div>
        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <strong>Berhasil!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>Periksa kembali isian berikut:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('surat-masuk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">No Urut</label>
                        <input type="text" class="form-control bg-secondary bg-opacity-10"
                               value="{{ $nextNoUrut }}" readonly>
                        <small class="text-muted">Otomatis, direset tiap awal tahun.</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">No TNDE</label>
                        <input type="text" name="no_tnde" class="form-control"
                               value="{{ old('no_tnde') }}" placeholder="Contoh: 1091">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tanggal Penerimaan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_penerimaan" class="form-control"
                               value="{{ old('tanggal_penerimaan', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tanggal Surat <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_surat" class="form-control"
                               value="{{ old('tanggal_surat') }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nomor Surat <span class="text-danger">*</span></label>
                    <input type="text" name="nomor_surat" class="form-control"
                           value="{{ old('nomor_surat') }}"
                           placeholder="Contoh: 400.14.1/26819/033.3/2026" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Sifat <span class="text-danger">*</span></label>
                        <select name="sifat" class="form-select" required>
                            @foreach(['Biasa/Terbuka','Terbatas','Rahasia','Sangat Rahasia'] as $s)
                                <option value="{{ $s }}" @selected(old('sifat') === $s)>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Lampiran</label>
                        <input type="text" name="lampiran" class="form-control"
                               value="{{ old('lampiran') }}" placeholder="Contoh: 1 lembar">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Isi Ringkas <span class="text-danger">*</span></label>
                    <textarea name="isi_ringkas" class="form-control" rows="3" required>{{ old('isi_ringkas') }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Dari <span class="text-danger">*</span></label>
                        <input type="text" name="dari" class="form-control" value="{{ old('dari') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Kepada <span class="text-danger">*</span></label>
                        <input type="text" name="kepada" class="form-control" value="{{ old('kepada') }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Unit Pengolah</label>
                    <select name="unit_pengolah" class="form-select">
                        @foreach(\App\Models\UnitPengolah::aktif()->get() as $u)
                            <option value="{{ $u->kode }}" @selected(old('unit_pengolah', '121.1') === $u->kode)>
                                {{ $u->label }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Surat masuk diagendakan oleh Sekretariat.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Kode Klasifikasi</label>
                    <select name="kode_klasifikasi" class="form-select cari-klasifikasi" >
                        @if(old('kode_klasifikasi'))
                            <option value="{{ old('kode_klasifikasi') }}" selected>{{ old('kode_klasifikasi') }}</option>
                        @endif
                    </select>
                    <small class="text-muted">Ketik kode atau uraian masalah, lalu pilih dari daftar JRA.</small>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Tingkat Perkembangan</label>
                        <select name="tingkat_perkembangan" class="form-select">
                            @foreach(['Asli','Copy','Salinan','Tembusan'] as $t)
                                <option value="{{ $t }}" @selected(old('tingkat_perkembangan', 'Copy') === $t)>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label fw-bold">Lokasi Simpan Fisik</label>
                        <input type="text" name="lokasi_simpan" class="form-control"
                               value="{{ old('lokasi_simpan') }}" placeholder="Contoh: Lemari A, Boks 2">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Unggah Dokumen (PDF, maks 20 MB)</label>
                    <input type="file" name="dokumen" class="form-control" accept="application/pdf">
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2">Simpan Surat Masuk</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function () {
    $('.cari-klasifikasi').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Ketik kode atau uraian, misal: kepegawaian',
        minimumInputLength: 2,
        ajax: {
            url: '{{ route('klasifikasi.cari') }}',
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({ results: data }),
            cache: true
        }
    });
});
</script>
</body>
</html>