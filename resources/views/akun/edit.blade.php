@extends('layouts.app')
@section('judul', 'Ubah Akun - siarsip')

@section('isi')
<div class="mx-auto" style="max-width: 640px;">
    <div class="card shadow-sm">
        <div class="card-header bg-warning"><h5 class="mb-0">Ubah Akun — {{ $akun->username }}</h5></div>
        <div class="card-body">
            @if($akun->id === auth()->id())
                <div class="alert alert-info small">
                    Ini akun Anda sendiri. Peran dan statusnya tidak dapat diubah dari sini,
                    agar Anda tidak kehilangan akses.
                </div>
            @endif

            <form action="{{ route('akun.update', $akun) }}" method="POST">
                @csrf
                @method('PUT')
                @include('akun._form')
                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('akun.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-warning flex-grow-1">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection