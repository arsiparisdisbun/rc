@extends('layouts.app')
@section('judul', 'Detail Penyusutan - siarsip')

@section('isi')
@php $p = $penyusutan; @endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-1">{{ $p->nomor_ba ?: $p->label_jenis }}</h4>
        <span class="badge bg-{{ $p->musnah() ? 'danger' : 'primary' }}">{{ $p->label_jenis }}</span>
        <span class="text-muted small ms-2">{{ $p->tanggal_ba?->translatedFormat('d F Y') }}</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('penyusutan.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
        <a href="{{ route('penyusutan.cetak-ba', $p) }}" target="_blank"
           class="btn btn-outline-secondary btn-sm">Cetak Berita Acara</a>
        <form action="{{ route('penyusutan.destroy', $p) }}" method="POST" class="d-inline"
              onsubmit="return confirm('Batalkan pencatatan ini? Berkas akan dikembalikan ke keadaan semula.')">
            @csrf
            @method('DELETE')
            <button class="btn btn-outline-danger btn-sm">Batalkan Pencatatan</button>
        </form>
    </div>
</div>

@if($p->catatan)
    <div class="alert alert-secondary"><strong>Catatan:</strong> {{ $p->catatan }}</div>
@endif

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-bold">{{ $p->musnah() ? 'Pelaksana' : 'PIHAK I' }} — Unit Kearsipan</div>
            <div class="card-body">
                <p class="mb-1"><strong>{{ $p->pihak1_nama }}</strong></p>
                <p class="mb-1 small">NIP {{ $p->pihak1_nip ?: '-' }}</p>
                <p class="mb-1 small">{{ $p->pihak1_pangkat ?: '-' }}</p>
                <p class="mb-0 small text-muted">{{ $p->pihak1_jabatan }}</p>

                @if($p->musnah())
                    <hr>
                    <p class="mb-1 small"><strong>Tempat:</strong> {{ $p->tempat }}</p>
                    <p class="mb-0 small"><strong>Cara:</strong> {{ $p->cara }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-bold">{{ $p->musnah() ? 'Saksi' : 'PIHAK II — Penerima' }}</div>
            <div class="card-body">
                @if($p->musnah())
                    <p class="mb-1"><strong>{{ $p->saksi1_nama }}</strong></p>
                    <p class="mb-3 small text-muted">{{ $p->saksi1_jabatan }} · NIP {{ $p->saksi1_nip ?: '-' }}</p>
                    <p class="mb-1"><strong>{{ $p->saksi2_nama }}</strong></p>
                    <p class="mb-0 small text-muted">{{ $p->saksi2_jabatan }} · NIP {{ $p->saksi2_nip ?: '-' }}</p>
                @else
                    <p class="mb-2 small">{{ $p->pihak2_instansi }}</p>
                    <p class="mb-1"><strong>{{ $p->pihak2_nama }}</strong></p>
                    <p class="mb-1 small">NIP {{ $p->pihak2_nip ?: '-' }}</p>
                    <p class="mb-1 small">{{ $p->pihak2_pangkat ?: '-' }}</p>
                    <p class="mb-0 small text-muted">{{ $p->pihak2_jabatan }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header fw-bold">Daftar Berkas ({{ $p->berkas->count() }} berkas)</div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
            <thead class="table-dark">
                <tr>
                    <th style="width: 85px;">No Berkas</th>
                    <th style="width: 65px;">Unit</th>
                    <th style="width: 130px;">Klasifikasi</th>
                    <th>Uraian</th>
                    <th style="width: 80px;">Kurun<br>Waktu</th>
                    <th style="width: 90px;">Boks</th>
                </tr>
            </thead>
            <tbody>
            @foreach($p->berkas as $b)
                <tr>
                    <td class="fw-bold">
                        <a href="{{ route('berkas.show', $b) }}" class="text-decoration-none">{{ $b->label }}</a>
                    </td>
                    <td class="text-center">{{ $b->unit_pengolah }}</td>
                    <td><small>{{ $b->kode_klasifikasi }}</small></td>
                    <td>{{ Str::limit($b->uraian, 70) }}</td>
                    <td class="text-center">{{ $b->kurun_waktu }}</td>
                    <td><small>{{ $b->boks?->label ?: '-' }}</small></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<p class="text-muted small mt-2">
    Dicatat oleh {{ $p->pencatat?->name }} pada {{ $p->created_at?->translatedFormat('d F Y, H:i') }}.
</p>
@endsection