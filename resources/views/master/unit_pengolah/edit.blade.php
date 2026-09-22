@extends('layouts.app')
@section('judul', 'Ubah Unit Pengolah - siarsip')

@section('isi')
<div class="mx-auto" style="max-width: 600px;">
    <div class="card shadow-sm">
        <div class="card-header bg-warning">
            <h5 class="mb-0">Ubah Unit {{ $unit->kode }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('unit-pengolah.update', $unit->kode) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-bold">Kode Unit</label>
                    <input type="text" class="form-control bg-secondary bg-opacity-10"
                           value="{{ $unit->kode }}" readonly>
                    <small class="text-muted">Kode tidak dapat diubah karena dipakai sebagai penanda di banyak data.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Unit <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control"
                           value="{{ old('nama', $unit->nama) }}" required autofocus>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Urutan Tampil</label>
                    <input type="number" name="urutan" class="form-control" min="1"
                           value="{{ old('urutan', $unit->urutan) }}"
                           placeholder="Kosongkan untuk ditaruh di urutan terakhir">
                    <small class="text-muted">Menentukan posisi dalam daftar pilihan unit di seluruh form.</small>
                </div>

                <div class="form-check mb-4">
                    <input type="checkbox" name="aktif" value="1" class="form-check-input" id="aktif"
                           @checked(old('aktif', $unit->aktif))>
                    <label class="form-check-label" for="aktif">Aktif</label>
                    <br><small class="text-muted">Unit nonaktif tidak muncul sebagai pilihan saat membuat data baru, tapi data lamanya tetap terbaca.</small>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('unit-pengolah.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-warning flex-grow-1">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection