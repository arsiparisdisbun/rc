@extends('layouts.app')
@section('judul', 'Daftar Berkas - siarsip')

@section('isi')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Daftar Berkas</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('berkas.cetak-daftar', request()->query()) }}" target="_blank"
               class="btn btn-outline-secondary btn-sm">Cetak Daftar Berkas</a>
            <a href="{{ route('berkas.ekspor-daftar-excel', request()->query()) }}"
               class="btn btn-outline-secondary btn-sm">Ekspor Excel</a>
            <a href="{{ route('berkas.create') }}" class="btn btn-primary btn-sm">+ Buat Berkas</a>
        </div>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="q" class="form-control" value="{{ request('q') }}"
                   placeholder="Cari uraian atau kode klasifikasi...">
        </div>
        @if(auth()->user()->lihatSemuaUnit())
            <div class="col-md-2">
                <select name="unit" class="form-select">
                    <option value="">Semua unit</option>
                    @foreach($daftarUnit as $u)
                        <option value="{{ $u->kode }}" @selected(request('unit') === $u->kode)>{{ $u->kode }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="col-md-2">
            <select name="tahun" class="form-select">
                <option value="">Semua tahun</option>
                @foreach($daftarTahun as $th)
                    <option value="{{ $th }}" @selected((string) request('tahun') === (string) $th)>{{ $th }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">Semua status</option>
                <option value="draf" @selected(request('status') === 'draf')>Draf</option>
                <option value="diajukan" @selected(request('status') === 'diajukan')>Menunggu Verifikasi</option>
                <option value="terverifikasi" @selected(request('status') === 'terverifikasi')>Terverifikasi</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-secondary flex-grow-1">Cari</button>
            <a href="{{ route('berkas.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <p class="text-muted small mb-2">
        Menampilkan {{ number_format($berkas->total()) }} berkas.
        @if(request('penyimpanan'))
            — status penyimpanan: <strong>{{ request('penyimpanan') }}</strong>
            <a href="{{ route('berkas.index') }}" class="ms-1">Reset filter</a>
        @endif
    </p>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 80px;">No<br>Berkas</th>
                        <th style="width: 70px;">Unit</th>
                        <th style="width: 160px;">Klasifikasi</th>
                        <th>Uraian Informasi Berkas</th>
                        <th style="width: 90px;">Kurun<br>Waktu</th>
                        <th style="width: 70px;" class="text-center">Item</th>
                        <th style="width: 110px;">Boks</th>
                        <th style="width: 110px;" class="text-center">Penyimpanan</th>
                        <th style="width: 130px;" class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($berkas as $b)
                    <tr>
                        <td class="fw-bold text-center">
                            <a href="{{ route('berkas.show', $b) }}" class="text-decoration-none">{{ $b->label }}</a>
                        </td>
                        <td class="text-center">{{ $b->unit_pengolah }}</td>
                        <td>
                            @if($b->kode_klasifikasi)
                                <span class="fw-bold">{{ $b->kode_klasifikasi }}</span><br>
                                <small class="text-muted">{{ Str::limit($b->klasifikasi?->uraian, 32) }}</small>
                            @else
                                <span class="badge bg-light text-dark border">Belum diklasifikasi</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('berkas.show', $b) }}" class="text-decoration-none text-dark">
                                {{ Str::limit($b->uraian, 80) }}
                            </a>
                            @if($b->sub_bagian)
                                <br><small class="text-muted">{{ $b->sub_bagian }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ $b->kurun_waktu }}</td>
                        <td class="text-center">{{ $b->item_count }}</td>
                        <td>
                            @if($b->boks)
                                <small>{{ $b->boks->label }}</small>
                            @else
                                <small class="text-muted">-</small>
                            @endif
                        </td>
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
                        <td class="text-center">
                            @php
                                $warnaStatus = match($b->status) {
                                    'draf'          => 'secondary',
                                    'diajukan'      => 'warning',
                                    'terverifikasi' => 'success',
                                };
                            @endphp
                            <span class="badge bg-{{ $warnaStatus }}">{{ $b->label_status }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center py-4 text-muted">Belum ada berkas.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $berkas->links() }}</div>
@endsection