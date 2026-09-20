@extends('layouts.app')
@section('judul', 'Edit Surat Masuk - siarsip')

@push('gaya')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
@endpush

@section('isi')
<div class="mx-auto" style="max-width: 900px;">
    <div class="card shadow-sm">
        <div class="card-header bg-warning">
            <h5 class="mb-0">Edit Surat Masuk — No Urut {{ $arsip->no_urut }} / {{ $arsip->tahun }}</h5>
        </div>
        <div class="card-body">

            <form action="{{ route('surat-masuk.update', $arsip) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">No Urut</label>
                        <input type="text" class="form-control bg-secondary bg-opacity-10"
                               value="{{ $arsip->no_urut }} / {{ $arsip->tahun }}" readonly>
                        <small class="text-muted">Nomor agenda tidak dapat diubah.</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">No TNDE</label>
                        <input type="text" name="no_tnde" class="form-control"
                               value="{{ old('no_tnde', $arsip->no_tnde) }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tanggal Penerimaan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_penerimaan" class="form-control"
                               value="{{ old('tanggal_penerimaan', $arsip->tanggal_penerimaan?->format('Y-m-d')) }}" required>
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
                        <label class="form-label fw-bold">Lampiran</label>
                        <input type="text" name="lampiran" class="form-control"
                               value="{{ old('lampiran', $arsip->lampiran) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Isi Ringkas <span class="text-danger">*</span></label>
                    <textarea name="isi_ringkas" class="form-control" rows="3" required>{{ old('isi_ringkas', $arsip->isi_ringkas) }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Dari <span class="text-danger">*</span></label>
                        <input type="text" name="dari" class="form-control" value="{{ old('dari', $arsip->dari) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Kepada <span class="text-danger">*</span></label>
                        <input type="text" name="kepada" class="form-control" value="{{ old('kepada', $arsip->kepada) }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Unit Pengolah</label>
                    <select name="unit_pengolah" class="form-select">
                        <option value="">— Belum ditentukan —</option>
                        @foreach(\App\Models\UnitPengolah::aktif()->get() as $u)
                            <option value="{{ $u->kode }}"
                                @selected(old('unit_pengolah', $arsip->unit_pengolah) === $u->kode)>
                                {{ $u->label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                                <div class="mb-3">
                    <label class="form-label fw-bold">Kode Klasifikasi</label>
                    <div class="d-flex gap-2 align-items-start">
                        <select name="kode_klasifikasi" class="form-select cari-klasifikasi" id="kode-klasifikasi-field">
                            @php $kodeTerpilih = old('kode_klasifikasi', $arsip->kode_klasifikasi); @endphp
                            @if($kodeTerpilih)
                                <option value="{{ $kodeTerpilih }}" selected>
                                    {{ $kodeTerpilih }} — {{ $arsip->klasifikasi?->uraian }}
                                </option>
                            @endif
                        </select>
                        @if($arsip->dokumen_path)
                            <button type="button" class="btn btn-outline-primary btn-sm text-nowrap" id="btn-saran-klasifikasi"
                                    data-url="{{ route('arsip.saran-klasifikasi', $arsip) }}">
                                Minta Saran
                            </button>
                        @endif
                    </div>
                    <small class="text-muted">Boleh dikosongkan bila belum diklasifikasi.</small>
                    <div id="hasil-saran-klasifikasi" class="mt-2"></div>
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
                    <a href="{{ route('surat-masuk.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-warning flex-grow-1 py-2">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('skrip')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
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
        $('#btn-saran-klasifikasi').on('click', function () {
        const $tombol = $(this);
        const $hasil = $('#hasil-saran-klasifikasi');

        $tombol.prop('disabled', true).text('Membaca dokumen...');
        $hasil.html('');

        $.get($tombol.data('url'))
            .done(function (r) {
                let html = '<div class="card card-body bg-light border py-2 px-3 small">';

                if (r.kode_pasti) {
                    html += `<div class="mb-2"><strong>Kode dari nomor surat:</strong><br>
                        <button type="button" class="btn btn-sm btn-success mt-1 btn-pilih-kode"
                                data-kode="${r.kode_pasti.kode}" data-uraian="${r.kode_pasti.uraian}">
                            ${r.kode_pasti.kode} — ${r.kode_pasti.uraian}
                        </button></div>`;
                } else if (r.pilihan_acuan && r.pilihan_acuan.length > 1) {
                    html += '<div class="mb-2"><strong>Nomor surat menunjuk ke salah satu:</strong><br>';
                    r.pilihan_acuan.forEach(p => {
                        html += `<button type="button" class="btn btn-sm btn-outline-success mt-1 me-1 btn-pilih-kode"
                                    data-kode="${p.kode}" data-uraian="${p.uraian}">
                            ${p.kode} — ${p.uraian}
                        </button>`;
                    });
                    html += '</div>';
                }

                if (r.kandidat && r.kandidat.length) {
                    html += '<div><strong>Kandidat dari isi surat</strong> <small class="text-muted">(diurutkan dari paling cocok)</small><div class="mt-1">';
                    r.kandidat.forEach((k, i) => {
                        const label = i === 0 ? 'Paling disarankan' : `Alternatif ${i + 1}`;
                        const gaya  = i === 0 ? 'btn-primary' : 'btn-outline-secondary';
                        const uraianSingkat = k.uraian.length > 60 ? k.uraian.substring(0, 60) + '…' : k.uraian;
                        html += `<button type="button" class="btn btn-sm ${gaya} mb-1 d-block w-100 text-start btn-pilih-kode"
                                    data-kode="${k.kode}" data-uraian="${k.uraian.replace(/"/g, '&quot;')}">
                            <span class="badge bg-light text-dark me-1">${label}</span>
                            <strong>${k.kode}</strong> — ${uraianSingkat}
                        </button>`;
                    });
                    html += '</div></div>';
                }

                html += '<div class="text-muted mt-2" style="font-size:.75rem;">Ini usulan dari pencocokan kata, bukan keputusan pasti — periksa dulu sebelum dipilih.</div>';
                html += '</div>';

                $hasil.html(html);
            })
            .fail(function (x) {
                $hasil.html('<div class="text-danger small">' + (x.responseJSON?.error || 'Gagal mengambil saran.') + '</div>');
            })
            .always(function () {
                $tombol.prop('disabled', false).text('Minta Saran');
            });
    });

    $(document).on('click', '.btn-pilih-kode', function () {
        const opsi = new Option(`${$(this).data('kode')} — ${$(this).data('uraian')}`, $(this).data('kode'), true, true);
        $('#kode-klasifikasi-field').empty().append(opsi).trigger('change');
    });
});
</script>
@endpush