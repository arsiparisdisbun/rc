<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Unit Pengolah <span class="text-danger">*</span></label>
        @if(isset($berkas))
            <input type="text" class="form-control bg-secondary bg-opacity-10"
                   value="{{ $berkas->unit?->label }}" readonly>
        @else
            <select name="unit_pengolah" id="unit" class="form-select" required>
                @foreach($daftarUnit as $u)
                    <option value="{{ $u->kode }}" @selected(old('unit_pengolah') === $u->kode)>{{ $u->label }}</option>
                @endforeach
            </select>
        @endif
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Tahun Berkas <span class="text-danger">*</span></label>
        @if(isset($berkas))
            <input type="text" class="form-control bg-secondary bg-opacity-10"
                   value="{{ $berkas->tahun }} (No. {{ $berkas->no_berkas }})" readonly>
        @else
            <input type="number" name="tahun" class="form-control" min="1990" max="{{ now()->year + 1 }}"
                   value="{{ old('tahun', now()->year) }}" required>
            <small class="text-muted">Ubah bila mendaftarkan berkas tahun sebelumnya.</small>
        @endif
    </div>
</div>

<div class="mb-3" id="kotak-subbagian">
    <label class="form-label fw-bold">Sub Bagian</label>
    <select name="sub_bagian" class="form-select">
        <option value="">— Tidak ditentukan —</option>
        @foreach(\App\Models\Berkas::SUB_BAGIAN as $sb)
            <option value="{{ $sb }}" @selected(old('sub_bagian', $berkas->sub_bagian ?? '') === $sb)>{{ $sb }}</option>
        @endforeach
    </select>
    <small class="text-muted">Hanya untuk Sekretariat.</small>
</div>

<div class="mb-3">
    <label class="form-label fw-bold">Uraian Informasi Berkas <span class="text-danger">*</span></label>
    <textarea name="uraian" class="form-control" rows="2" required>{{ old('uraian', $berkas->uraian ?? '') }}</textarea>
    <small class="text-muted">Contoh: Perjalanan Dinas Pegawai Triwulan I</small>
</div>

<div class="mb-3">
    <label class="form-label fw-bold">Kode Klasifikasi</label>
    <select name="kode_klasifikasi" class="form-select cari-klasifikasi">
        @php $kodeTerpilih = old('kode_klasifikasi', $berkas->kode_klasifikasi ?? ''); @endphp
        @if($kodeTerpilih)
            <option value="{{ $kodeTerpilih }}" selected>
                {{ $kodeTerpilih }} @isset($berkas) — {{ $berkas->klasifikasi?->uraian }} @endisset
            </option>
        @endif
    </select>
    <small class="text-muted">Menentukan retensi dan nasib akhir berkas.</small>
</div>

<div class="row">
    <div class="col-md-3 mb-3">
        <label class="form-label fw-bold">Kurun Waktu Awal</label>
        <input type="number" name="tahun_mulai" class="form-control" min="1900" max="{{ now()->year + 1 }}"
               value="{{ old('tahun_mulai', $berkas->tahun_mulai ?? '') }}">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label fw-bold">Kurun Waktu Akhir</label>
        <input type="number" name="tahun_selesai" class="form-control" min="1900" max="{{ now()->year + 1 }}"
               value="{{ old('tahun_selesai', $berkas->tahun_selesai ?? '') }}">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label fw-bold">Jumlah Fisik</label>
        <input type="number" name="jumlah_fisik" class="form-control" min="1"
               value="{{ old('jumlah_fisik', $berkas->jumlah_fisik ?? '') }}">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label fw-bold">Satuan <span class="text-danger">*</span></label>
        <select name="satuan" class="form-select" required>
            @foreach(['Berkas','Lembar','Sampul'] as $s)
                <option value="{{ $s }}" @selected(old('satuan', $berkas->satuan ?? 'Berkas') === $s)>{{ $s }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="mb-3">
    <label class="form-label fw-bold">Klasifikasi Keamanan &amp; Akses (SKKAD) <span class="text-danger">*</span></label>
    <select name="skkad" class="form-select" required>
        @foreach(['Biasa/Terbuka','Terbatas','Rahasia','Sangat Rahasia'] as $s)
            <option value="{{ $s }}" @selected(old('skkad', $berkas->skkad ?? 'Biasa/Terbuka') === $s)>{{ $s }}</option>
        @endforeach
    </select>
</div>

@isset($berkas)
    <hr class="my-4">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Boks Penyimpanan</label>
            <select name="boks_id" class="form-select">
                <option value="">— Belum ditentukan —</option>
                @foreach($daftarBoks as $bk)
                    <option value="{{ $bk->id }}" @selected((string) old('boks_id', $berkas->boks_id) === (string) $bk->id)>
                        {{ $bk->label }} {{ $bk->lokasi ? '— ' . $bk->lokasi : '' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Lokasi Simpan</label>
            <input type="text" name="lokasi_simpan" class="form-control"
                   value="{{ old('lokasi_simpan', $berkas->lokasi_simpan) }}"
                   placeholder="Contoh: Filing Cabinet 2 Laci 3">
        </div>
    </div>
@endisset

<div class="mb-3">
    <label class="form-label fw-bold">Keterangan</label>
    <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $berkas->keterangan ?? '') }}</textarea>
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
        placeholder: 'Ketik kode atau uraian masalah...',
        ajax: {
            url: '{{ route('klasifikasi.cari') }}', dataType: 'json', delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({ results: data }), cache: true
        }
    });

    // Sub bagian hanya berlaku untuk Sekretariat
    const unit = document.getElementById('unit');
    const kotakSub = document.getElementById('kotak-subbagian');
    const unitTetap = @json($berkas->unit_pengolah ?? null);

    function aturSub() {
        const nilai = unit ? unit.value : unitTetap;
        kotakSub.style.display = nilai === '121.1' ? '' : 'none';
    }

    if (unit) unit.addEventListener('change', aturSub);
    aturSub();
});
</script>
@endpush