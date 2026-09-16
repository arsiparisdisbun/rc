@extends('layouts.app')
@section('judul', 'Tambah Boks - siarsip')

@section('isi')
<div class="mx-auto" style="max-width: 640px;">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white"><h5 class="mb-0">Tambah Boks</h5></div>
        <div class="card-body">
            <form action="{{ route('boks.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-bold">Unit Pengolah <span class="text-danger">*</span></label>
                    <select name="unit_pengolah" class="form-select" required>
                        @foreach($daftarUnit as $u)
                            <option value="{{ $u->kode }}" @selected(old('unit_pengolah') === $u->kode)>{{ $u->label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Jenis Boks <span class="text-danger">*</span></label>
                    <select name="jenis" class="form-select" required>
                        <option value="aktif" @selected(old('jenis', 'aktif') === 'aktif')>Aktif — disimpan di filing cabinet</option>
                        <option value="inaktif" @selected(old('jenis') === 'inaktif')>Inaktif — disimpan di record center</option>
                    </select>
                    <small class="text-muted">Nomor boks dihitung terpisah untuk aktif dan inaktif.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Lokasi</label>
                    <input type="text" name="lokasi" class="form-control" value="{{ old('lokasi') }}"
                           placeholder="Contoh: Lemari A Rak 2, atau Record Center Rak 5">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea>
                </div>

                <div class="form-check">
                    <input type="checkbox" name="terpakai" value="1" class="form-check-input" id="terpakai"
                           @checked(old('terpakai', true))>
                    <label class="form-check-label" for="terpakai">Boks sedang dipakai</label>
                </div>

                <div class="alert alert-info small mt-3 mb-0">
                    Nomor boks diberikan otomatis, melanjutkan nomor terakhir milik unit dan jenis yang dipilih.
                </div>

                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('boks.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-primary flex-grow-1">Simpan Boks</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection