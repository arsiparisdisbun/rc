@extends('layouts.app')
@section('judul', 'Tambah Unit Pengolah - siarsip')

@section('isi')
<div class="mx-auto" style="max-width: 600px;">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Tambah Unit Pengolah</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('unit-pengolah.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-bold">Kode Unit <span class="text-danger">*</span></label>
                    <input type="text" name="kode" class="form-control" value="{{ old('kode') }}"
                           placeholder="Contoh: 121.7" required autofocus>
                    <small class="text-muted">Tidak dapat diubah setelah disimpan.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Unit <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama') }}"
                           placeholder="Contoh: Bidang Sarana dan Prasarana" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Urutan Tampil</label>
                    <input type="number" name="urutan" class="form-control" min="1"
                           value="{{ old('urutan') }}"
                           placeholder="Kosongkan untuk ditaruh di urutan terakhir">
                    <small class="text-muted">Menentukan posisi dalam daftar pilihan unit di seluruh form.</small>
                </div>

                <div class="form-check mb-4">
                    <input type="checkbox" name="aktif" value="1" class="form-check-input" id="aktif"
                           @checked(old('aktif', true))>
                    <label class="form-check-label" for="aktif">Aktif</label>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('unit-pengolah.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-primary flex-grow-1">Simpan Unit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection