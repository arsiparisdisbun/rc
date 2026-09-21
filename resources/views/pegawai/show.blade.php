@extends('layouts.app')
@section('judul', "Pegawai {$pegawai->nama} - siarsip")

@section('isi')
@php
    $warna = $pegawai->status === 'aktif' ? 'success' : 'secondary';
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-1">{{ $pegawai->nama }}</h4>
        <span class="badge bg-{{ $warna }}">{{ $pegawai->label_status }}</span>
        @if($pegawai->nip)
            <span class="text-muted small ms-2">NIP {{ $pegawai->nip }}</span>
        @endif
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('pegawai.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
        <a href="{{ route('pegawai.edit', $pegawai) }}" class="btn btn-warning btn-sm">Ubah Data</a>
    </div>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-header fw-bold">Data Pegawai</div>
    <div class="card-body p-0">
        <table class="table mb-0" style="font-size: 0.9rem;">
            <tr><th style="width: 180px;" class="bg-light">Jabatan</th><td>{{ $pegawai->jabatan ?: '-' }}</td></tr>
            <tr><th class="bg-light">Unit Pengolah</th><td>{{ $pegawai->unit?->label ?: '-' }}</td></tr>
            @if($pegawai->tanggal_status)
                <tr><th class="bg-light">Tanggal {{ $pegawai->label_status }}</th><td>{{ $pegawai->tanggal_status->translatedFormat('d F Y') }}</td></tr>
            @endif
            @if($pegawai->keterangan)
                <tr><th class="bg-light">Keterangan</th><td>{{ $pegawai->keterangan }}</td></tr>
            @endif
        </table>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header fw-bold">
        Berkas Kepegawaian ({{ $berkas->count() }})
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
            <thead class="table-dark">
                <tr>
                    <th style="width: 80px;">No Berkas</th>
                    <th style="width: 130px;">Klasifikasi</th>
                    <th>Uraian</th>
                    <th style="width: 90px;">Kurun Waktu</th>
                    <th style="width: 90px;" class="text-center">Item</th>
                    <th style="width: 110px;" class="text-center">Penyimpanan</th>
                </tr>
            </thead>
            <tbody>
            @forelse($berkas as $b)
                <tr>
                    <td class="fw-bold">
                        <a href="{{ route('berkas.show', $b) }}" class="text-decoration-none">{{ $b->label }}</a>
                    </td>
                    <td><small>{{ $b->kode_klasifikasi ?: '-' }}</small></td>
                    <td>{{ Str::limit($b->uraian, 60) }}</td>
                    <td class="text-center">{{ $b->kurun_waktu }}</td>
                    <td class="text-center">{{ $b->item_count }}</td>
                    <td class="text-center">
                        @php
                            $sp = $b->status_penyimpanan;
                            $warnaSp = match(true) {
                                $sp === 'Kerja'   => 'info',
                                $sp === 'Aktif'   => 'success',
                                $sp === 'Inaktif' => 'warning',
                                $sp === 'Tidak diketahui' => 'secondary',
                                default => 'danger',
                            };
                        @endphp
                        <span class="badge bg-{{ $warnaSp }}">{{ $sp }}</span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada berkas untuk pegawai ini.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection