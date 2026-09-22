@extends('layouts.app')
@section('judul', 'Master Jenis Naskah - siarsip')

@section('isi')
<div class="mx-auto" style="max-width: 950px;">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <h4 class="mb-0">Jenis Naskah Dinas</h4>
        <a href="{{ route('jenis-naskah.create') }}" class="btn btn-primary btn-sm">+ Tambah Jenis</a>
    </div>
    <p class="text-muted small">Pilihan jenis naskah saat mengagendakan surat keluar, mengikuti pembagian dalam tata naskah dinas.</p>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 60px;" class="text-center">Urut</th>
                        <th>Nama Jenis Naskah</th>
                        <th style="width: 130px;">Kelompok</th>
                        <th style="width: 120px;">Sub Kelompok</th>
                        <th style="width: 85px;" class="text-center">Status</th>
                        <th style="width: 110px;" class="text-center">Dipakai</th>
                        <th style="width: 140px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($jenis as $j)
                    @php $dipakai = $pemakaian[$j->id] ?? 0; @endphp
                    <tr>
                        <td class="text-center text-muted">{{ $j->urutan }}</td>
                        <td class="fw-bold">{{ $j->nama }}</td>
                        <td><small>{{ $j->kelompok ?: '-' }}</small></td>
                        <td><small>{{ $j->sub_kelompok ?: '-' }}</small></td>
                        <td class="text-center">
                            <span class="badge bg-{{ $j->aktif ? 'success' : 'secondary' }}">
                                {{ $j->aktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($dipakai > 0)
                                <span class="badge bg-info">{{ number_format($dipakai) }}</span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('jenis-naskah.edit', $j) }}" class="btn btn-sm btn-warning">Ubah</a>
                                @if($dipakai === 0)
                                    <form action="{{ route('jenis-naskah.destroy', $j) }}" method="POST"
                                          onsubmit="return confirm('Hapus jenis naskah {{ $j->nama }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada jenis naskah.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="alert alert-secondary small mt-3">
        Mengubah nama berlaku untuk semua surat yang memakainya, termasuk surat lama —
        karena yang tersimpan di surat adalah penunjuk ke jenis ini, bukan teks namanya.
        Jenis yang sudah tidak dipakai lagi sebaiknya dinonaktifkan, bukan dihapus.
    </div>
</div>
@endsection