@extends('layouts.app')
@section('judul', 'Master Unit Pengolah - siarsip')

@section('isi')
<div class="mx-auto" style="max-width: 950px;">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <h4 class="mb-0">Unit Pengolah</h4>
        <a href="{{ route('unit-pengolah.create') }}" class="btn btn-primary btn-sm">+ Tambah Unit</a>
    </div>
    <p class="text-muted small">Bidang dan UPT yang menciptakan serta mengelola arsip.</p>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 60px;" class="text-center">Urut</th>
                        <th style="width: 90px;">Kode</th>
                        <th>Nama Unit</th>
                        <th style="width: 90px;" class="text-center">Status</th>
                        <th style="width: 120px;" class="text-center">Dipakai</th>
                        <th style="width: 150px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($unit as $u)
                    @php $dipakai = $pemakaian[$u->kode] ?? 0; @endphp
                    <tr>
                        <td class="text-center text-muted">{{ $u->urutan }}</td>
                        <td class="fw-bold">{{ $u->kode }}</td>
                        <td>{{ $u->nama }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $u->aktif ? 'success' : 'secondary' }}">
                                {{ $u->aktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($dipakai > 0)
                                <span class="badge bg-info">{{ number_format($dipakai) }} data</span>
                            @else
                                <span class="text-muted small">Belum dipakai</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('unit-pengolah.edit', $u->kode) }}" class="btn btn-sm btn-warning">Ubah</a>
                                @if($dipakai === 0)
                                    <form action="{{ route('unit-pengolah.destroy', $u->kode) }}" method="POST"
                                          onsubmit="return confirm('Hapus unit {{ $u->kode }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada unit pengolah.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="alert alert-secondary small mt-3">
        Kode unit tidak dapat diubah setelah dibuat, karena dipakai sebagai penanda di arsip,
        berkas, boks, data pegawai, dan akun pengguna. Unit yang sudah tidak berlaku sebaiknya
        dinonaktifkan saja — bukan dihapus — agar data lamanya tetap terbaca.
    </div>
</div>
@endsection