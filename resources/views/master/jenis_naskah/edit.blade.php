@extends('layouts.app')
@section('judul', 'Ubah Jenis Naskah - siarsip')

@section('isi')
<div class="mx-auto" style="max-width: 650px;">
    <div class="card shadow-sm">
        <div class="card-header bg-warning">
            <h5 class="mb-0">Ubah Jenis Naskah</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('jenis-naskah.update', $jenis) }}" method="POST">
                @csrf
                @method('PUT')
                @include('master.jenis_naskah._form')

                <div class="d-flex gap-2">
                    <a href="{{ route('jenis-naskah.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-warning flex-grow-1">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection