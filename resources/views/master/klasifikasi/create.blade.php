@extends('layouts.app')
@section('judul', 'Tambah Kode JRA - siarsip')

@section('isi')
<div class="mx-auto" style="max-width: 700px;">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Tambah Kode Klasifikasi</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('klasifikasi.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-bold">Kode Klasifikasi <span class="text-danger">*</span></label>
                    <input type="text" name="kode_klasifikasi" class="form-control"
                           value="{{ old('kode_klasifikasi') }}" placeholder="Contoh: 500.3.2.5" required autofocus>
                    <small class="text-muted">Tidak dapat diubah setelah disimpan.</small>
                </div>

                @include('master.klasifikasi._form')

                <div class="d-flex gap-2">
                    <a href="{{ route('klasifikasi.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-primary flex-grow-1">Simpan Kode</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection