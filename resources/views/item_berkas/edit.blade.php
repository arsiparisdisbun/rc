@extends('layouts.app')
@section('judul', "Ubah Item - Berkas {$berkas->label}")

@section('isi')
<div class="mx-auto" style="max-width: 800px;">
    <div class="card shadow-sm">
        <div class="card-header bg-warning">
            <h5 class="mb-0">Ubah Item {{ $item->nomor_item }} — Berkas {{ $berkas->label }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('item-berkas.update', [$berkas, $item]) }}" method="POST">
                @csrf
                @method('PUT')
                @include('item_berkas._form')

                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('berkas.show', $berkas) }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-warning flex-grow-1">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection