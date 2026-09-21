@extends('layouts.app')
@section('judul', 'Data Keuangan - siarsip')

@section('isi')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Data Keuangan</h4>
        <a href="{{ route('berkas.create') }}" class="btn btn-primary btn-sm">+ Buat Berkas</a>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-3">
        @foreach(\App\Models\Berkas::KATEGORI_KEUANGAN as $kat)
            <a href="{{ route('keuangan.index', array_merge(request()->except('page'), ['kategori' => $kat])) }}"
               class="btn btn-sm {{ request('kategori') === $kat ? 'btn-primary' : 'btn-outline-secondary' }}">
                {{ $kat }} <span class="badge bg-light text-dark ms-1">{{ $ringkasan[$kat] ?? 0 }}</span>
            </a>
        @endforeach
    </div>

    <form method="GET" class="row g-2 mb-3">
        @if(request('kategori'))
            <input type="hidden" name="kategori" value="{{ request('kategori') }}">
        @endif
        <div class="col-md-{{ $daftarUnit ? 4 : 5 }}">
            <input type="text" name="q" class="form-control" value="{{ request('q') }}"
                   placeholder="Cari uraian atau kode klasifikasi...">
        </div>
        @if($daftarUnit)
            <div class="col-md-2">
                <select name="unit" class="form-select">
                    <option value="">Semua unit</option>
                    @foreach($daftarUnit as $u)
                        <option value="{{ $u->kode }}" @selected(request('unit') === $u->kode)>{{ $u->kode }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="col-md-3">
            <select name="tahun" class="form-select">
                <option value="">Semua tahun</option>
                @foreach($daftarTahun as $th)
                    <option value="{{ $th }}" @selected((string) request('tahun') === (string) $th)>{{ $th }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-{{ $daftarUnit ? 3 : 4 }} d-flex gap-2">
            <button class="btn btn-secondary flex-grow-1">Cari</button>
            <a href="{{ route('keuangan.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <p class="text-muted small mb-2">
        Menampilkan {{ number_format($berkas->total()) }} berkas
        @if(request('kategori')) — kategori: <strong>{{ request('kategori') }}</strong> @endif
    </p>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 80px;">No Berkas</th>
                        @if($daftarUnit)<th style="width: 65px;">Unit</th>@endif
                        <th style="width: 140px;">Kategori</th>
                        <th style="width: 130px;">Klasifikasi</th>
                        <th>Uraian</th>
                        <th style="width: 90px;">Kurun Waktu</th>
                        <th style="width: 110px;" class="text-center">Penyimpanan</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($berkas as $b)
                    <tr>
                        <td class="fw-bold">
                            <a href="{{ route('berkas.show', $b) }}" class="text-decoration-none">{{ $b->label }}</a>
                        </td>
                        @if($daftarUnit)<td class="text-center">{{ $b->unit_pengolah }}</td>@endif
                        <td>
                            @if($b->kategori_keuangan)
                                <span class="badge bg-primary">{{ $b->kategori_keuangan }}</span>
                            @else
                                <span class="text-muted small">Belum ditentukan</span>
                            @endif
                        </td>
                        <td><small>{{ $b->kode_klasifikasi ?: '-' }}</small></td>
                        <td>{{ Str::limit($b->uraian, 70) }}</td>
                        <td class="text-center">{{ $b->kurun_waktu }}</td>
                        <td class="text-center">
                            @php
                                $sp = $b->status_penyimpanan;
                                $warnaSp = match(true) {
                                    $sp === 'Kerja'   => 'info',
                                    $sp === 'Aktif'   => 'success',
                                    $sp === 'Inaktif' => 'warning',
                                    $sp === 'Tidak diketahui' => 'secondary',
                                    default => 'danger',
                                };
                            @endphp
                            <span class="badge bg-{{ $warnaSp }}">{{ $sp }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ $daftarUnit ? 7 : 6 }}" class="text-center py-4 text-muted">Belum ada berkas keuangan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $berkas->links() }}</div>
@endsection