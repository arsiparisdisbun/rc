@extends('layouts.app')
@section('judul', 'Data Kepegawaian - siarsip')

@section('isi')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Data Kepegawaian</h4>
        <a href="{{ route('pegawai.create') }}" class="btn btn-primary btn-sm">+ Tambah Pegawai</a>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-5">
            <input type="text" name="q" class="form-control" value="{{ request('q') }}"
                   placeholder="Cari nama, NIP, atau jabatan...">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Semua status</option>
                @foreach(\App\Models\Pegawai::STATUS as $kode => $label)
                    <option value="{{ $kode }}" @selected(request('status') === $kode)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button class="btn btn-secondary flex-grow-1">Cari</button>
            <a href="{{ route('pegawai.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 140px;">NIP</th>
                        <th>Nama</th>
                        <th style="width: 220px;">Jabatan</th>
                        <th style="width: 90px;">Unit</th>
                        <th style="width: 110px;" class="text-center">Status</th>
                        <th style="width: 90px;" class="text-center">Berkas</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($pegawai as $p)
                    <tr>
                        <td>{{ $p->nip ?: '-' }}</td>
                        <td>
                            <a href="{{ route('pegawai.show', $p) }}" class="text-decoration-none fw-bold">
                                {{ $p->nama }}
                            </a>
                        </td>
                        <td>{{ $p->jabatan ?: '-' }}</td>
                        <td class="text-center">{{ $p->unit_pengolah ?: '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $p->status === 'aktif' ? 'success' : 'secondary' }}">
                                {{ $p->label_status }}
                            </span>
                        </td>
                        <td class="text-center">{{ $p->berkas_count }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data pegawai.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $pegawai->links() }}</div>
@endsection