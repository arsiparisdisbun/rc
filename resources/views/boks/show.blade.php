@extends('layouts.app')
@section('judul', "{$boks->label} - siarsip")

@section('isi')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-1">{{ $boks->label }}</h4>
        <p class="text-muted mb-0 small">{{ $boks->unit?->label }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('boks.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
        <a href="{{ route('boks.cetak-label', ['id' => [$boks->id]]) }}" target="_blank"
           class="btn btn-outline-secondary btn-sm">Cetak Label</a>
        <a href="{{ route('boks.edit', $boks) }}" class="btn btn-warning btn-sm">Ubah</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header fw-bold">Data Boks</div>
            <div class="card-body">
                <p class="mb-2"><strong>Jenis:</strong>
                    <span class="badge bg-{{ $boks->jenis === 'aktif' ? 'success' : 'warning' }}">
                        {{ ucfirst($boks->jenis) }}
                    </span>
                </p>
                <p class="mb-2"><strong>Lokasi:</strong> {{ $boks->lokasi ?: '-' }}</p>
                <p class="mb-2"><strong>Status:</strong> {{ $boks->terpakai ? 'Dipakai' : 'Kosong' }}</p>
                <p class="mb-0"><strong>Jumlah berkas:</strong> {{ $boks->berkas->count() }}</p>
                @if($boks->keterangan)
                    <hr>
                    <p class="mb-0 small text-muted">{{ $boks->keterangan }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header fw-bold">Berkas di Dalam Boks</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size: 0.9rem;">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 80px;">No Berkas</th>
                            <th style="width: 120px;">Klasifikasi</th>
                            <th>Uraian</th>
                            <th style="width: 80px;">Kurun<br>Waktu</th>
                            <th style="width: 100px;" class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($boks->berkas as $b)
                        <tr>
                            <td class="fw-bold">
                                <a href="{{ route('berkas.show', $b) }}" class="text-decoration-none">{{ $b->label }}</a>
                            </td>
                            <td><small>{{ $b->kode_klasifikasi ?: '-' }}</small></td>
                            <td>{{ Str::limit($b->uraian, 60) }}</td>
                            <td class="text-center">{{ $b->kurun_waktu }}</td>
                            <td class="text-center">
                                @php
                                    $sp = $b->status_penyimpanan;
                                    $w = match(true) {
                                        $sp === 'Kerja'   => 'info',
                                        $sp === 'Aktif'   => 'success',
                                        $sp === 'Inaktif' => 'warning',
                                        $sp === 'Tidak diketahui' => 'secondary',
                                        default => 'danger',
                                    };
                                @endphp
                                <span class="badge bg-{{ $w }}">{{ $sp }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-4 text-muted">Boks masih kosong.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection