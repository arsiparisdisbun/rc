@extends('layouts.app')
@section('judul', $info['judul'] . ' - siarsip')

@section('isi')
<div class="mx-auto" style="max-width: 800px;">
    <h4 class="mb-1">{{ $info['judul'] }}</h4>
    <p class="text-muted small">{{ $info['keterangan'] }}</p>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form action="{{ route('pengaturan.store', $kelompok) }}" method="POST" class="row g-2">
                @csrf
                <div class="col-md-9">
                    <input type="text" name="nilai" class="form-control" value="{{ old('nilai') }}"
                           placeholder="Nilai baru, misalnya: {{ $info['contoh'] }}" required>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100">+ Tambah</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-dark">
                    <tr>
                        <th>Nilai</th>
                        <th style="width: 140px;" class="text-center">Dipakai</th>
                        <th style="width: 110px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($daftar as $p)
                    @php $dipakai = $pemakaian[$p->nilai] ?? 0; @endphp
                    <tr>
                        <td class="fw-bold">{{ $p->nilai }}</td>
                        <td class="text-center">
                            @if($dipakai > 0)
                                <span class="badge bg-info">{{ number_format($dipakai) }} data</span>
                            @else
                                <span class="text-muted small">Belum dipakai</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($dipakai === 0)
                                <form action="{{ route('pengaturan.destroy', [$kelompok, $p]) }}" method="POST"
                                      onsubmit="return confirm('Hapus {{ $p->nilai }} dari daftar?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center py-4 text-muted">Daftar masih kosong.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="alert alert-secondary small mt-3">
        Nilai yang sudah dipakai data tidak dapat dihapus. Mengganti nama juga tidak disediakan —
        bila perlu berganti istilah, tambahkan nilai baru lalu sesuaikan data lama satu per satu
        sebelum nilai lamanya dihapus.
    </div>
</div>
@endsection