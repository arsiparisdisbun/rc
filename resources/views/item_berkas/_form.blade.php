<div class="row">
    <div class="col-md-7 mb-3">
        <label class="form-label fw-bold">Nomor Surat / Dokumen</label>
        <input type="text" name="nomor_surat" class="form-control"
               value="{{ old('nomor_surat', $item?->nomor_surat) }}"
               placeholder="Kosongkan bila dokumen tidak bernomor">
    </div>
    <div class="col-md-5 mb-3">
        <label class="form-label fw-bold">Kode Klasifikasi</label>
        <select name="kode_klasifikasi" class="form-select cari-klasifikasi">
            @php $kodeTerpilih = old('kode_klasifikasi', $item?->kode_klasifikasi ?? $berkas->kode_klasifikasi); @endphp
            @if($kodeTerpilih)
                <option value="{{ $kodeTerpilih }}" selected>{{ $kodeTerpilih }}</option>
            @endif
        </select>
    </div>
</div>

<div class="mb-3">
    <label class="form-label fw-bold">Uraian Informasi Arsip <span class="text-danger">*</span></label>
    <textarea name="uraian" class="form-control" rows="2" required>{{ old('uraian', $item?->uraian) }}</textarea>
</div>

<div class="row">
    <div class="col-md-3 mb-3">
        <label class="form-label fw-bold">Tanggal</label>
        <input type="date" name="tanggal" class="form-control"
               value="{{ old('tanggal', isset($item) ? $item->tanggal?->format('Y-m-d') : '') }}">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label fw-bold">Atau Tahun Saja</label>
        <input type="number" name="tahun" class="form-control" min="1900" max="{{ now()->year + 1 }}"
               value="{{ old('tahun', $item?->tahun) }}"
               placeholder="Bila tanggal tidak diketahui">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label fw-bold">Jumlah</label>
        <input type="number" name="jumlah" class="form-control" min="1"
               value="{{ old('jumlah', $item?->jumlah) }}">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label fw-bold">Satuan <span class="text-danger">*</span></label>
        <select name="satuan" class="form-select" required>
            @foreach(['Lembar','Berkas','Sampul'] as $s)
                <option value="{{ $s }}" @selected(old('satuan', $item?->satuan ?? 'Lembar') === $s)>{{ $s }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="mb-3">
    <label class="form-label fw-bold">SKKAD <span class="text-danger">*</span></label>
    <select name="skkad" class="form-select" required>
        @foreach(['Biasa/Terbuka','Terbatas','Rahasia','Sangat Rahasia'] as $s)
            <option value="{{ $s }}" @selected(old('skkad', $item?->skkad ?? $berkas->skkad) === $s)>{{ $s }}</option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label fw-bold">Keterangan</label>
    <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $item?->keterangan) }}</textarea>
</div>

@push('gaya')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
@endpush

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
});
</script>
@endpush