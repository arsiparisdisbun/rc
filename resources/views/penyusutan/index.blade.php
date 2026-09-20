@extends('layouts.app')
@section('judul', 'Penyusutan Akhir - siarsip')

@section('isi')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Penyusutan Akhir</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('penyusutan.create', ['jenis' => 'serah']) }}" class="btn btn-outline-primary btn-sm">
            + Catat Penyerahan
        </a>
        <a href="{{ route('penyusutan.create', ['jenis' => 'musnah']) }}" class="btn btn-danger btn-sm">
            + Catat Pemusnahan
        </a>
    </div>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-3">
        <select name="jenis" class="form-select">
            <option value="">Semua jenis</option>
            <option value="musnah" @selected(request('jenis') === 'musnah')>Pemusnahan</option>
            <option value="serah" @selected(request('jenis') === 'serah')>Penyerahan Arsip Statis</option>
        </select>
    </div>
    <div class="col-md-3 d-flex gap-2">
        <button class="btn btn-secondary flex-grow-1">Cari</button>
        <a href="{{ route('penyusutan.index') }}" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
            <thead class="table-dark">
                <tr>
                    <th style="width: 180px;">Nomor Berita Acara</th>
                    <th style="width: 160px;">Jenis</th>
                    <th style="width: 110px;">Tanggal</th>
                    <th style="width: 100px;" class="text-center">Jumlah<br>Berkas</th>
                    <th>Pelaksana</th>
                    <th style="width: 140px;">Dicatat Oleh</th>
                </tr>
            </thead>
            <tbody>
            @forelse($penyusutan as $p)
                <tr>
                    <td class="fw-bold">
                        <a href="{{ route('penyusutan.show', $p) }}" class="text-decoration-none">
                            {{ $p->nomor_ba ?: 'Belum bernomor' }}
                        </a>
                    </td>
                    <td>
                        <span class="badge bg-{{ $p->musnah() ? 'danger' : 'primary' }}">{{ $p->label_jenis }}</span>
                    </td>
                    <td class="text-center">{{ $p->tanggal_ba?->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $p->berkas_count }}</td>
                    <td><small>{{ $p->pihak1_nama }}<br><span class="text-muted">{{ $p->pihak1_jabatan }}</span></small></td>
                    <td><small>{{ $p->pencatat?->name }}</small></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada catatan penyusutan akhir.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $penyusutan->links() }}</div>
@endsection