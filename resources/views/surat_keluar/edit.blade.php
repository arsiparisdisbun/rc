<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Surat Keluar - siarsip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5" style="max-width: 900px;">
    <div class="card shadow-sm">
        <div class="card-header bg-warning">
            <h4 class="mb-0">Edit Surat Keluar — Nomor Agenda {{ $arsip->no_urut }} / {{ $arsip->tahun }}</h4>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>Periksa kembali isian berikut:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('surat-keluar.update', $arsip) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Unit Pengolah <span class="text-danger">*</span></label>
                        <select name="unit_pengolah" class="form-select" required>
                            @foreach($daftarUnit as $u)
                                <option value="{{ $u->kode }}" @selected(old('unit_pengolah', $arsip->unit_pengolah) === $u->kode)>
                                    {{ $u->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Jenis Naskah Dinas <span class="text-danger">*</span></label>
                        <select name="jenis_naskah_id" class="form-select pilih-jenis" required>
                            @foreach($daftarJenis as $j)
                                <option value="{{ $j->id }}" @selected((string) old('jenis_naskah_id', $arsip->jenis_naskah_id) === (string) $j->id)>
                                    {{ $j->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Nomor Agenda</label>
                        <input type="text" class="form-control bg-secondary bg-opacity-10"
                               value="{{ $arsip->no_urut }} / {{ $arsip->tahun }}" readonly>
                        <small class="text-muted">Nomor agenda tidak dapat diubah.</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tanggal Surat <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_surat" class="form-control"
                               value="{{ old('tanggal_surat', $arsip->tanggal_surat?->format('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nomor Surat <span class="text-danger">*</span></label>
                    <input type="text" name="nomor_surat" class="form-control"
                           value="{{ old('nomor_surat', $arsip->nomor_surat) }}" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Sifat <span class="text-danger">*</span></label>
                        <select name="sifat" class="form-select" required>
                            @foreach(['Biasa/Terbuka','Terbatas','Rahasia','Sangat Rahasia'] as $s)
                                <option value="{{ $s }}" @selected(old('sifat', $arsip->sifat) === $s)>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Jumlah Lembar</label>
                        <input type="number" name="jumlah_lembar" class="form-control" min="1"
                               value="{{ old('jumlah_lembar', $arsip->jumlah_lembar) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Isi Ringkas <span class="text-danger">*</span></label>
                    <textarea name="isi_ringkas" class="form-control" rows="3" required>{{ old('isi_ringkas', $arsip->isi_ringkas) }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Kepada <span class="text-danger">*</span></label>
                        <input type="text" name="kepada" class="form-control" value="{{ old('kepada', $arsip->kepada) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Pembuat</label>
                        <input type="text" name="pembuat" class="form-control" value="{{ old('pembuat', $arsip->pembuat) }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tanggal Upload</label>
                        <input type="date" name="tanggal_upload" class="form-control"
                               value="{{ old('tanggal_upload', $arsip->tanggal_upload?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tanggal Verifikasi</label>
                        <input type="date" name="tanggal_verifikasi" class="form-control"
                               value="{{ old('tanggal_verifikasi', $arsip->tanggal_verifikasi?->format('Y-m-d')) }}">
                    </div>
                </div>

                <hr class="my-4">

                <div class="mb-3">
                    <label class="form-label fw-bold">Kode Klasifikasi dari TNDE</label>
                    <input type="text" name="kode_tnde" class="form-control" value="{{ old('kode_tnde', $arsip->kode_tnde) }}">
                    <small class="text-muted">Disimpan sebagai catatan, tidak dipakai menghitung retensi.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Kode Klasifikasi (hasil koreksi)</label>
                    <select name="kode_klasifikasi" class="form-select cari-klasifikasi">
                        @php $kodeTerpilih = old('kode_klasifikasi', $arsip->kode_klasifikasi); @endphp
                        @if($kodeTerpilih)
                            <option value="{{ $kodeTerpilih }}" selected>
                                {{ $kodeTerpilih }} — {{ $arsip->klasifikasi?->uraian }}
                            </option>
                        @endif
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Tingkat Perkembangan</label>
                        <select name="tingkat_perkembangan" class="form-select">
                            <option value="">— Belum ditentukan —</option>
                            @foreach(['Asli','Salinan'] as $t)
                                <option value="{{ $t }}" @selected(old('tingkat_perkembangan', $arsip->tingkat_perkembangan) === $t)>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label fw-bold">Lokasi Simpan Fisik</label>
                        <input type="text" name="lokasi_simpan" class="form-control"
                               value="{{ old('lokasi_simpan', $arsip->lokasi_simpan) }}">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Dokumen PDF</label>
                    @if($arsip->dokumen_path)
                        <div class="mb-2">
                            <a href="{{ asset('storage/' . $arsip->dokumen_path) }}" target="_blank"
                               class="btn btn-sm btn-outline-danger">Lihat dokumen saat ini</a>
                        </div>
                    @endif
                    <input type="file" name="dokumen" class="form-control" accept="application/pdf">
                    <small class="text-muted">Kosongkan jika tidak ingin mengganti dokumen.</small>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('surat-keluar.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-warning flex-grow-1 py-2">Simpan Perubahan</button>
                </div>
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
        theme: 'bootstrap-5', width: '100%', minimumInputLength: 2,
        placeholder: 'Ketik kode atau uraian...',
        ajax: {
            url: '{{ route('klasifikasi.cari') }}', dataType: 'json', delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({ results: data }), cache: true
        }
    });
    $('.pilih-jenis').select2({ theme: 'bootstrap-5', width: