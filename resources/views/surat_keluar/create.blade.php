<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Surat Keluar - siarsip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5" style="max-width: 900px;">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Form Input Surat Keluar</h4>
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

            <form action="{{ route('surat-keluar.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Unit Pengolah <span class="text-danger">*</span></label>
                        <select name="unit_pengolah" id="unit" class="form-select" required>
                            <option value="">— Pilih unit —</option>
                            @foreach($daftarUnit as $u)
                                <option value="{{ $u->kode }}" @selected(old('unit_pengolah') === $u->kode)>{{ $u->label }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Menentukan nomor agenda.</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Jenis Naskah Dinas <span class="text-danger">*</span></label>
                        <select name="jenis_naskah_id" class="form-select pilih-jenis" required>
                            <option value="">— Pilih jenis —</option>
                            @foreach($daftarJenis as $j)
                                <option value="{{ $j->id }}" @selected((string) old('jenis_naskah_id') === (string) $j->id)>
                                    {{ $j->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tanggal Surat <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_surat" id="tgl_surat" class="form-control"
                               value="{{ old('tanggal_surat', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Nomor Agenda</label>
                        <input type="number" name="no_urut" id="no_urut" class="form-control"
                               value="{{ old('no_urut') }}" placeholder="Terisi otomatis">
                        <small class="text-muted" id="ket-nomor">Pilih unit dan tanggal surat dulu.</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nomor Surat <span class="text-danger">*</span></label>
                    <input type="text" name="nomor_surat" class="form-control"
                           value="{{ old('nomor_surat') }}"
                           placeholder="Contoh: 000.3.3/215021/121.1/2026" required>
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
                        <label class="form-label fw-bold">Jumlah Lembar</label>
                        <input type="number" name="jumlah_lembar" class="form-control" min="1"
                               value="{{ old('jumlah_lembar') }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Isi Ringkas <span class="text-danger">*</span></label>
                    <textarea name="isi_ringkas" class="form-control" rows="3" required>{{ old('isi_ringkas') }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Kepada <span class="text-danger">*</span></label>
                        <input type="text" name="kepada" class="form-control" value="{{ old('kepada') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Pembuat</label>
                        <input type="text" name="pembuat" class="form-control" value="{{ old('pembuat') }}"
                               placeholder="Nama pegawai">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tanggal Upload</label>
                        <input type="date" name="tanggal_upload" class="form-control" value="{{ old('tanggal_upload') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tanggal Verifikasi</label>
                        <input type="date" name="tanggal_verifikasi" class="form-control" value="{{ old('tanggal_verifikasi') }}">
                    </div>
                </div>

                <hr class="my-4">

                <div class="mb-3">
                    <label class="form-label fw-bold">Kode Klasifikasi dari TNDE</label>
                    <input type="text" name="kode_tnde" class="form-control" value="{{ old('kode_tnde') }}"
                           placeholder="Kode asli sebelum koreksi">
                    <small class="text-muted">Disimpan sebagai catatan, tidak dipakai menghitung retensi.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Kode Klasifikasi (hasil koreksi)</label>
                    <select name="kode_klasifikasi" class="form-select cari-klasifikasi">
                        @if(old('kode_klasifikasi'))
                            <option value="{{ old('kode_klasifikasi') }}" selected>{{ old('kode_klasifikasi') }}</option>
                        @endif
                    </select>
                    <small class="text-muted">Inilah yang dipakai menghitung retensi. Boleh dikosongkan.</small>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Tingkat Perkembangan</label>
                        <select name="tingkat_perkembangan" class="form-select">
                            <option value="">— Belum ditentukan —</option>
                            @foreach(['Asli','Salinan'] as $t)
                                <option value="{{ $t }}" @selected(old('tingkat_perkembangan') === $t)>{{ $t }}</option>
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

                <div class="d-flex gap-2">
                    <a href="{{ route('surat-keluar.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary flex-grow-1 py-2">Simpan Surat Keluar</button>
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
        theme: 'bootstrap-5', width: '100%',
        placeholder: 'Ketik kode atau uraian, misal: kepegawaian',
        minimumInputLength: 2,
        ajax: {
            url: '{{ route('klasifikasi.cari') }}', dataType: 'json', delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({ results: data }), cache: true
        }
    });

    $('.pilih-jenis').select2({ theme: 'bootstrap-5', width: '100%' });

    // Usulkan nomor agenda begitu unit dan tanggal surat lengkap
    function usulkanNomor() {
        const unit = $('#unit').val();
        const tgl  = $('#tgl_surat').val();
        if (!unit || !tgl) return;

        $.get('{{ route('surat-keluar.usul-nomor') }}', { unit_pengolah: unit, tanggal_surat: tgl })
            .done(function (r) {
                $('#no_urut').val(r.nomor);
                $('#ket-nomor').text('Usulan sistem: ' + r.nomor + '. Ubah bila TNDE menerbitkan nomor lain.');
            })
            .fail(function () {
                $('#ket-nomor').text('Gagal mengambil usulan nomor. Isi manual.');
            });
    }

    $('#unit, #tgl_surat').on('change', usulkanNomor);
});
</script>
</body>
</html>