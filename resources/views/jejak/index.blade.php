@extends('layouts.app')
@section('judul', 'Riwayat Perubahan - siarsip')

@section('isi')
<h4 class="mb-3">Riwayat Perubahan</h4>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-2">
        <select name="model" class="form-select">
            <option value="">Semua objek</option>
            @foreach($daftarModel as $m)
                <option value="{{ $m }}" @selected(request('model') === $m)>{{ $m }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <select name="aksi" class="form-select">
            <option value="">Semua aksi</option>
            <option value="tambah" @selected(request('aksi') === 'tambah')>Ditambahkan</option>
            <option value="ubah" @selected(request('aksi') === 'ubah')>Diubah</option>
            <option value="hapus" @selected(request('aksi') === 'hapus')>Dihapus</option>
        </select>
    </div>
    <div class="col-md-2">
        <select name="user" class="form-select">
            <option value="">Semua pengguna</option>
            @foreach($daftarUser as $u)
                <option value="{{ $u->id }}" @selected((string) request('user') === (string) $u->id)>{{ $u->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <input type="date" name="dari" class="form-control" value="{{ request('dari') }}" title="Dari tanggal">
    </div>
    <div class="col-md-2">
        <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}" title="Sampai tanggal">
    </div>
    <div class="col-md-2 d-flex gap-2">
        <button class="btn btn-secondary flex-grow-1">Cari</button>
        <a href="{{ route('jejak.index') }}" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

<p class="text-muted small mb-2">Menampilkan {{ number_format($jejak->total()) }} catatan.</p>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
            <thead class="table-dark">
                <tr>
                    <th style="width: 130px;">Waktu</th>
                    <th style="width: 110px;">Objek</th>
                    <th style="width: 95px;">Aksi</th>
                    <th style="width: 160px;">Pengguna</th>
                    <th>Perubahan</th>
                </tr>
            </thead>
            <tbody>
            @forelse($jejak as $j)
                <tr>
                    <td><small>{{ $j->created_at?->format('d/m/Y H:i') }}</small></td>
                    <td><small>{{ $j->label_model }} #{{ $j->model_id }}</small></td>
                    <td>
                        @php
                            $w = match($j->aksi) {
                                'tambah' => 'success',
                                'ubah'   => 'warning',
                                'hapus'  => 'danger',
                            };
                        @endphp
                        <span class="badge bg-{{ $w }}">{{ $j->label_aksi }}</span>
                    </td>
                    <td>
                        <small>{{ $j->nama_user ?: 'Sistem' }}</small>
                        @if($j->unit_pengolah)
                            <br><small class="text-muted">{{ $j->unit_pengolah }}</small>
                        @endif
                    </td>
                    <td>
                        @if($j->aksi === 'ubah' && $j->sesudah)
                            @foreach(array_slice($j->sesudah, 0, 4, true) as $kolom => $baru)
                                @php $lama = $j->sebelum[$kolom] ?? null; @endphp
                                <div class="small">
                                    <strong>{{ \App\Models\JejakAudit::namaKolom($kolom) }}:</strong>
                                    <span class="text-danger">{{ Str::limit((string) ($lama ?: '(kosong)'), 30) }}</span>
                                    &rarr;
                                    <span class="text-success">{{ Str::limit((string) ($baru ?: '(kosong)'), 30) }}</span>
                                </div>
                            @endforeach
                            @if(count($j->sesudah) > 4)
                                <small class="text-muted">dan {{ count($j->sesudah) - 4 }} kolom lainnya</small>
                            @endif
                        @elseif($j->aksi === 'tambah')
                            <small class="text-muted">Data baru dibuat</small>
                        @else
                            <small class="text-muted">Data dihapus</small>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat tercatat.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $jejak->links() }}</div>
@endsection