@extends('layouts.app')
@section('judul', 'Buat Berkas - siarsip')

@section('isi')
<div class="mx-auto" style="max-width: 800px;">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white"><h5 class="mb-0">Buat Berkas Baru</h5></div>
        <div class="card-body">
            <form action="{{ route('berkas.store') }}" method="POST">
                @csrf
                @include('berkas._form')

                <div class="alert alert-info small">
                    Nomor berkas diberikan otomatis, melanjutkan urutan terakhir pada unit dan tahun yang dipilih.
                    Boks penyimpanan diisi nanti setelah berkas lengkap.
                </div>

                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('berkas.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-primary flex-grow-1">Simpan Berkas</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection