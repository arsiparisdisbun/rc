@extends('layouts.app')
@section('judul', 'Daftar Boks - siarsip')

@section('isi')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Daftar Boks Arsip</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('boks.cetak-label', request()->query()) }}" target="_blank"
               class="btn btn-outline-secondary btn-sm">Cetak Label</a>
            <a href="{{ route('boks.create') }}" class="btn btn-primary btn-sm">+ Tambah Boks</a>
        </div>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="q" class="form-control" value="{{ request('q') }}"
                   placeholder="Cari lokasi atau keterangan...">
        </div>
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
        <div class="col-md-2">
            <select name="jenis" class="form-select">
                <option value="">Semua jenis</option>
                <option value="aktif" @selected(request('jenis') === 'aktif')>Aktif</option>
                <option value="inaktif" @selected(request('jenis') === 'inaktif')>Inaktif</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-secondary flex-grow-1">Cari</button>
            <a href="{{ route('boks.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <p class="text-muted small mb-2">Menampilkan {{ number_format($boks->total()) }} boks.</p>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 150px;">Boks</th>
                        <th style="width: 220px;">Unit</th>
                        <th>Lokasi</th>
                        <th>Keterangan</th>
                        <th class="text-center" style="width: 100px;">Status</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($boks as $b)
                    <tr>
                        <td class="fw-bold">
                            <a href="{{ route('boks.show', $b) }}" class="text-decoration-none">Boks {{ $b->nomor }}</a>
                            <span class="badge bg-{{ $b->jenis === 'aktif' ? 'success' : 'warning' }} ms-1">
                                {{ ucfirst($b->jenis) }}
                            </span>
                        </td>
                        <td><small>{{ $b->unit?->label }}</small></td>
                        <td>{{ $b->lokasi ?: '-' }}</td>
                        <td><small>{{ Str::limit($b->keterangan, 60) ?: '-' }}</small></td>
                        <td class="text-center">
                            <span class="badge bg-{{ $b->terpakai ? 'primary' : 'secondary' }}">
                                {{ $b->terpakai ? 'Dipakai' : 'Kosong' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('boks.edit', $b) }}" class="btn btn-sm btn-warning">Ubah</a>
                            <form action="{{ route('boks.destroy', $b) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus {{ $b->label }} unit {{ $b->unit_pengolah }}?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada boks. Klik "Tambah Boks" untuk membuat.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $boks->links() }}</div>
@endsection