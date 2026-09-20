@extends('layouts.app')
@section('judul', 'Detail Penyerahan - siarsip')

@section('isi')
@php
    $kearsipan = auth()->user()->lihatSemuaUnit();
    $w = match($pemindahan->status) {
        'diajukan' => 'warning',
        'diterima' => 'success',
        'ditolak'  => 'danger',
    };
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-1">{{ $pemindahan->nomor_ba ?: 'Penyerahan Arsip Inaktif' }}</h4>
        <span class="badge bg-{{ $w }}">{{ $pemindahan->label_status }}</span>
        <span class="text-muted small ms-2">{{ $pemindahan->unit?->label }}</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('pemindahan.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
        <a href="{{ route('pemindahan.cetak-ba', $pemindahan) }}" target="_blank"
           class="btn btn-outline-secondary btn-sm">Cetak Berita Acara</a>
    </div>
</div>

@if($pemindahan->catatan)
    <div class="alert alert-warning"><strong>Catatan:</strong> {{ $pemindahan->catatan }}</div>
@endif

<form action="{{ route('pemindahan.terima', $pemindahan) }}" method="POST">
    @csrf

    <div class="card shadow-sm mb-3">
        <div class="card-header fw-bold">
            Daftar Berkas ({{ $pemindahan->berkas->count() }} berkas)
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 85px;">No Berkas</th>
                        <th style="width: 140px;">Klasifikasi</th>
                        <th>Uraian</th>
                        <th style="width: 85px;">Kurun<br>Waktu</th>
                        <th style="width: 220px;">Boks Inaktif</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($pemindahan->berkas as $b)
                    <tr>
                        <td class="fw-bold">
                            <a href="{{ route('berkas.show', $b) }}" class="text-decoration-none">{{ $b->label }}</a>
                        </td>
                        <td><small>{{ $b->kode_klasifikasi ?: '-' }}</small></td>
                        <td>{{ Str::limit($b->uraian, 70) }}</td>
                        <td class="text-center">{{ $b->kurun_waktu }}</td>
                        <td>
                            @if($pemindahan->diajukan() && $kearsipan)
                                <select name="boks[{{ $b->id }}]" class="form-select form-select-sm">
                                    <option value="">— Pilih boks —</option>
                                    @foreach($boksInaktif as $bk)
                                        <option value="{{ $bk->id }}" @selected(old("boks.{$b->id}") == $bk->id)>
                                            {{ $bk->label }} {{ $bk->lokasi ? '— ' . $bk->lokasi : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <small>{{ $b->boks?->label ?: 'Belum ditentukan' }}</small>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header fw-bold">PIHAK I — Unit Pengolah</div>
                <div class="card-body">
                    <p class="mb-1"><strong>{{ $pemindahan->pihak1_nama }}</strong></p>
                    <p class="mb-1 small">NIP {{ $pemindahan->pihak1_nip ?: '-' }}</p>
                    <p class="mb-1 small">{{ $pemindahan->pihak1_pangkat ?: '-' }}</p>
                    <p class="mb-0 small text-muted">{{ $pemindahan->pihak1_jabatan }}</p>
                    <hr>
                    <p class="small text-muted mb-0">
                        Diajukan {{ $pemindahan->diajukan_pada?->translatedFormat('d F Y, H:i') }}
                        oleh {{ $pemindahan->pengaju?->name }}
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header fw-bold">PIHAK II — Unit Kearsipan</div>
                <div class="card-body">
                    @if($pemindahan->diajukan() && $kearsipan)
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small fw-bold">Nama <span class="text-danger">*</span></label>
                                <input type="text" name="pihak2_nama" class="form-control form-control-sm"
                                       value="{{ old('pihak2_nama') }}" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small fw-bold">NIP</label>
                                <input type="text" name="pihak2_nip" class="form-control form-control-sm"
                                       value="{{ old('pihak2_nip') }}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small fw-bold">Pangkat</label>
                                <input type="text" name="pihak2_pangkat" class="form-control form-control-sm"
                                       value="{{ old('pihak2_pangkat') }}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small fw-bold">Jabatan <span class="text-danger">*</span></label>
                                <input type="text" name="pihak2_jabatan" class="form-control form-control-sm"
                                       value="{{ old('pihak2_jabatan') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Tanggal Penerimaan <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_terima" class="form-control form-control-sm"
                                       value="{{ old('tanggal_terima', date('Y-m-d')) }}" required>
                            </div>
                        </div>
                        <button class="btn btn-success w-100">Terima Penyerahan</button>
                    @elseif($pemindahan->diterima())
                        <p class="mb-1"><strong>{{ $pemindahan->pihak2_nama }}</strong></p>
                        <p class="mb-1 small">NIP {{ $pemindahan->pihak2_nip ?: '-' }}</p>
                        <p class="mb-1 small">{{ $pemindahan->pihak2_pangkat ?: '-' }}</p>
                        <p class="mb-0 small text-muted">{{ $pemindahan->pihak2_jabatan }}</p>
                        <hr>
                        <p class="small text-muted mb-0">
                            Diterima {{ $pemindahan->diproses_pada?->translatedFormat('d F Y, H:i') }}
                            oleh {{ $pemindahan->pemroses?->name }}
                        </p>
                    @else
                        <p class="text-muted small mb-0">Menunggu penerimaan oleh Unit Kearsipan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</form>

@if($pemindahan->diajukan() && $kearsipan)
    <div class="card shadow-sm mt-3">
        <div class="card-body">
            <form action="{{ route('pemindahan.tolak', $pemindahan) }}" method="POST"
                  onsubmit="return confirm('Tolak penyerahan ini? Berkas akan dikembalikan ke unit.')">
                @csrf
                <label class="form-label fw-bold small">Tolak Penyerahan</label>
                <div class="d-flex gap-2">
                    <input type="text" name="catatan" class="form-control form-control-sm"
                           placeholder="Alasan penolakan" required>
                    <button class="btn btn-outline-danger btn-sm">Tolak</button>
                </div>
            </form>
        </div>
    </div>
@endif
@endsection