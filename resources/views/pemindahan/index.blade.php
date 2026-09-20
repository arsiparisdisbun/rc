@extends('layouts.app')
@section('judul', 'Pemindahan Arsip Inaktif - siarsip')

@section('isi')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Pemindahan Arsip Inaktif</h4>
    @unless(auth()->user()->kearsipan())
        <a href="{{ route('pemindahan.create') }}" class="btn btn-primary btn-sm">+ Ajukan Penyerahan</a>
    @endunless
</div>

<form method="GET" class="row g-2 mb-3">
    @if(auth()->user()->lihatSemuaUnit())
        <div class="col-md-3">
            <select name="unit" class="form-select">
                <option value="">Semua unit</option>
                @foreach($daftarUnit as $u)
                    <option value="{{ $u->kode }}" @selected(request('unit') === $u->kode)>{{ $u->label }}</option>
                @endforeach
            </select>
        </div>
    @endif
    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">Semua status</option>
            <option value="diajukan" @selected(request('status') === 'diajukan')>Menunggu Penerimaan</option>
            <option value="diterima" @selected(request('status') === 'diterima')>Diterima</option>
            <option value="ditolak" @selected(request('status') === 'ditolak')>Ditolak</option>
        </select>
    </div>
    <div class="col-md-3 d-flex gap-2">
        <button class="btn btn-secondary flex-grow-1">Cari</button>
        <a href="{{ route('pemindahan.index') }}" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
            <thead class="table-dark">
                <tr>
                    <th style="width: 170px;">Nomor Berita Acara</th>
                    <th style="width: 200px;">Unit Pengolah</th>
                    <th style="width: 100px;" class="text-center">Jumlah<br>Berkas</th>
                    <th style="width: 110px;">Tanggal<br>Pengajuan</th>
                    <th>Penanda Tangan Unit</th>
                    <th style="width: 150px;" class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
            @forelse($pemindahan as $p)
                <tr>
                    <td class="fw-bold">
                        <a href="{{ route('pemindahan.show', $p) }}" class="text-decoration-none">
                            {{ $p->nomor_ba ?: 'Belum bernomor' }}
                        </a>
                    </td>
                    <td><small>{{ $p->unit?->label }}</small></td>
                    <td class="text-center">{{ $p->berkas_count }}</td>
                    <td class="text-center">{{ $p->diajukan_pada?->format('d/m/Y') }}</td>
                    <td><small>{{ $p->pihak1_nama }}<br><span class="text-muted">{{ $p->pihak1_jabatan }}</span></small></td>
                    <td class="text-center">
                        @php
                            $w = match($p->status) {
                                'diajukan' => 'warning',
                                'diterima' => 'success',
                                'ditolak'  => 'danger',
                            };
                        @endphp
                        <span class="badge bg-{{ $w }}">{{ $p->label_status }}</span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada pengajuan penyerahan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $pemindahan->links() }}</div>
@endsection