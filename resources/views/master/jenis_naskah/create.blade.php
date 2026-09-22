@extends('layouts.app')
@section('judul', 'Tambah Jenis Naskah - siarsip')

@section('isi')
<div class="mx-auto" style="max-width: 650px;">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Tambah Jenis Naskah</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('jenis-naskah.store') }}" method="POST">
                @csrf
                @include('master.jenis_naskah._form')

                <div class="d-flex gap-2">
                    <a href="{{ route('jenis-naskah.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-primary flex-grow-1">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection