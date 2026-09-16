@extends('layouts.app')
@section('judul', 'Ubah Boks - siarsip')

@section('isi')
<div class="mx-auto" style="max-width: 640px;">
    <div class="card shadow-sm">
        <div class="card-header bg-warning">
            <h5 class="mb-0">Ubah {{ $boks->label }} — {{ $boks->unit_pengolah }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('boks.update', $boks) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Unit Pengolah</label>
                        <input type="text" class="form-control bg-secondary bg-opacity-10"
                               value="{{ $boks->unit?->label }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nomor &amp; Jenis</label>
                        <input type="text" class="form-control bg-secondary bg-opacity-10"
                               value="{{ $boks->label }}" readonly>
                    </div>
                </div>

                <div class="alert alert-info small">
                    Unit, nomor, dan jenis boks tidak dapat diubah agar penomorannya tetap sah.
                    Untuk memindahkan berkas ke boks lain, ubah dari halaman berkas.
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Lokasi</label>
                    <input type="text" name="lokasi" class="form-control" value="{{ old('lokasi', $boks->lokasi) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $boks->keterangan) }}</textarea>
                </div>

                <div class="form-check">
                    <input type="checkbox" name="terpakai" value="1" class="form-check-input" id="terpakai"
                           @checked(old('terpakai', $boks->terpakai))>
                    <label class="form-check-label" for="terpakai">Boks sedang dipakai</label>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('boks.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-warning flex-grow-1">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection