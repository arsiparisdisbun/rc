@extends('layouts.app')
@section('judul', 'Tambah Akun - siarsip')

@section('isi')
<div class="mx-auto" style="max-width: 640px;">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white"><h5 class="mb-0">Tambah Akun</h5></div>
        <div class="card-body">
            <form action="{{ route('akun.store') }}" method="POST">
                @csrf
                @include('akun._form', ['akun' => null])
                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('akun.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-primary flex-grow-1">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection