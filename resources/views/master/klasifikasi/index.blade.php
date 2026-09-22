@extends('layouts.app')
@section('judul', 'Master JRA - siarsip')

@section('isi')
    <div class="d-flex justify-content-between align-items-center mb-1">
        <h4 class="mb-0">Jadwal Retensi Arsip (JRA)</h4>
        <a href="{{ route('klasifikasi.create') }}" class="btn btn-primary btn-sm">+ Tambah Kode</a>
    </div>
    <p class="text-muted small">
        Dasar perhitungan retensi seluruh arsip dan berkas. Total {{ number_format($total) }} kode terdaftar.
    </p>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-6">
            <input type="text" name="q" class="form-control" value="{{ request('q') }}"
                   placeholder="Cari kode atau uraian masalah...">
        </div>
        <div class="col-md-3">
            <select name="nasib" class="form-select">
                <option value="">Semua nasib akhir</option>
                @foreach($daftarNasib as $n)
                    <option value="{{ $n }}" @selected(request('nasib') === $n)>{{ $n }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-secondary flex-grow-1">Cari</button>
            <a href="{{ route('klasifikasi.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <p class="text-muted small mb-2">Menampilkan {{ number_format($klasifikasi->total()) }} kode.</p>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 120px;">Kode</th>
                        <th>Uraian Masalah</th>
                        <th style="width: 75px;" class="text-center">Aktif<br>(th)</th>
                        <th style="width: 75px;" class="text-center">Inaktif<br>(th)</th>
                        <th style="width: 100px;">Nasib Akhir</th>
                        <th style="width: 80px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($klasifikasi as $k)
                    <tr>
                        <td class="fw-bold">{{ $k->kode_klasifikasi }}</td>
                        <td>{{ Str::limit($k->uraian, 110) }}</td>
                        <td class="text-center">{{ $k->retensi_aktif }}</td>
                        <td class="text-center">{{ $k->retensi_inaktif }}</td>
                        <td>
                            <span class="badge bg-{{ $k->nasib_akhir === 'Musnah' ? 'danger' : 'primary' }}">
                                {{ $k->nasib_akhir }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('klasifikasi.edit', $k->kode_klasifikasi) }}" class="btn btn-sm btn-warning">Ubah</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">Tidak ada kode yang cocok.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $klasifikasi->links() }}</div>

    <div class="alert alert-warning small mt-2">
        <strong>Perlu kehati-hatian.</strong> Mengubah retensi sebuah kode langsung menggeser
        status penyimpanan seluruh arsip dan berkas yang memakainya — ada yang bisa berubah dari
        Aktif menjadi Siap Musnah, atau sebaliknya. Ubah hanya bila ada peraturan JRA baru.
        Kode tidak dapat dihapus lewat aplikasi.
    </div>
@endsection