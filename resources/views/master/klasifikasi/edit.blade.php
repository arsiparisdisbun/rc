@extends('layouts.app')
@section('judul', 'Ubah Kode JRA - siarsip')

@section('isi')
<div class="mx-auto" style="max-width: 700px;">
    <div class="card shadow-sm">
        <div class="card-header bg-warning">
            <h5 class="mb-0">Ubah Kode {{ $klasifikasi->kode_klasifikasi }}</h5>
        </div>
        <div class="card-body">

            @if($pemakaian > 0)
                <div class="alert alert-danger small">
                    <strong>Kode ini dipakai {{ number_format($pemakaian) }} arsip dan berkas.</strong>
                    Mengubah retensi akan langsung menggeser status penyimpanan semuanya —
                    ada yang bisa berubah dari Aktif menjadi Siap Musnah, atau sebaliknya.
                    Pastikan perubahan ini memang mengikuti peraturan JRA yang baru.
                </div>
            @endif

            <form action="{{ route('klasifikasi.update', $klasifikasi->kode_klasifikasi) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-bold">Kode Klasifikasi</label>
                    <input type="text" class="form-control bg-secondary bg-opacity-10"
                           value="{{ $klasifikasi->kode_klasifikasi }}" readonly>
                    <small class="text-muted">Kode tidak dapat diubah karena dipakai sebagai penanda di arsip dan berkas.</small>
                </div>

                @include('master.klasifikasi._form')

                <div class="d-flex gap-2">
                    <a href="{{ route('klasifikasi.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-warning flex-grow-1">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection