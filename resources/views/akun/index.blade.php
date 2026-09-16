@extends('layouts.app')
@section('judul', 'Kelola Akun - siarsip')

@section('isi')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Kelola Akun</h4>
        <a href="{{ route('akun.create') }}" class="btn btn-primary btn-sm">+ Tambah Akun</a>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Nama Pengguna</th>
                        <th>Nama Lengkap</th>
                        <th>Peran</th>
                        <th>Unit</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width: 160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($akun as $a)
                    <tr>
                        <td class="fw-bold">
                            {{ $a->username }}
                            @if($a->id === auth()->id())
                                <span class="badge bg-info ms-1">Anda</span>
                            @endif
                        </td>
                        <td>{{ $a->name }}</td>
                        <td>
                            @php
                                $warnaPeran = match($a->peran) {
                                    'superadmin' => 'danger',
                                    'kearsipan'  => 'primary',
                                    default      => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $warnaPeran }}">{{ ucfirst($a->peran) }}</span>
                        </td>
                        <td><small>{{ $a->unit?->label ?: '-' }}</small></td>
                        <td class="text-center">
                            <span class="badge bg-{{ $a->aktif ? 'success' : 'secondary' }}">
                                {{ $a->aktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('akun.edit', $a) }}" class="btn btn-sm btn-warning">Ubah</a>
                            @if($a->id !== auth()->id())
                                <form action="{{ route('akun.destroy', $a) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus akun {{ $a->username }}? Tindakan ini tidak dapat dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection