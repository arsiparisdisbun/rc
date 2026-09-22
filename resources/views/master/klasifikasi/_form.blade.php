<div class="mb-3">
    <label class="form-label fw-bold">Uraian Masalah <span class="text-danger">*</span></label>
    <textarea name="uraian" class="form-control" rows="3" required>{{ old('uraian', $klasifikasi->uraian ?? '') }}</textarea>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold">Retensi Aktif <span class="text-danger">*</span></label>
        <div class="input-group">
            <input type="number" name="retensi_aktif" class="form-control" min="0" max="100"
                   value="{{ old('retensi_aktif', $klasifikasi->retensi_aktif ?? '') }}" required>
            <span class="input-group-text">tahun</span>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold">Retensi Inaktif <span class="text-danger">*</span></label>
        <div class="input-group">
            <input type="number" name="retensi_inaktif" class="form-control" min="0" max="100"
                   value="{{ old('retensi_inaktif', $klasifikasi->retensi_inaktif ?? '') }}" required>
            <span class="input-group-text">tahun</span>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold">Nasib Akhir <span class="text-danger">*</span></label>
        <input type="text" name="nasib_akhir" class="form-control" list="daftar-nasib"
               value="{{ old('nasib_akhir', $klasifikasi->nasib_akhir ?? '') }}" required>
        <datalist id="daftar-nasib">
            @foreach($daftarNasib as $n)
                <option value="{{ $n }}">
            @endforeach
        </datalist>
    </div>
</div>