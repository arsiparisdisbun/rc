@extends('layouts.app')
@section('judul', "Tambah Item - Berkas {$berkas->label}")

@section('isi')
<div class="mx-auto" style="max-width: 800px;">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Tambah Item Manual</h5>
        </div>
        <div class="card-body">
            <p class="text-muted small">
                Berkas {{ $berkas->label }} — {{ Str::limit($berkas->uraian, 80) }}
            </p>

            <div class="alert alert-info small">
                Gunakan form ini untuk arsip yang belum tercatat di buku agenda, misalnya laporan,
                SK, kontrak, atau dokumen dari unit. Item ini hanya ada di dalam berkas.
            </div>

            <form action="{{ route('item-berkas.store', $berkas) }}" method="POST">
                @csrf
                @include('item_berkas._form', ['item' => null])

                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('berkas.show', $berkas) }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-primary flex-grow-1">Simpan Item</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection