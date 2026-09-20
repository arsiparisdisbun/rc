@extends('layouts.app')
@section('judul', "Berkas {$berkas->label} - siarsip")

@push('gaya')
<style>
    .tabel-detail th { width: 230px; background: #f8f9fa; font-weight: 600; }
    .tabel-detail th, .tabel-detail td { border: 1px solid #dee2e6; padding: .5rem .75rem; }
    .tabel-item th, .tabel-item td { border: 1px solid #dee2e6; vertical-align: middle; }
    .tabel-item thead th { text-align: center; }
</style>
@endpush

@section('isi')
@php
    $boleh = $berkas->bolehDiubahOleh(auth()->user());
    $kearsipan = auth()->user()->lihatSemuaUnit();

    $warnaStatus = match($berkas->status) {
        'draf'          => 'secondary',
        'diajukan'      => 'warning',
        'terverifikasi' => 'success',
    };

    $sp = $berkas->status_penyimpanan;
    $warnaSp = match(true) {
        $sp === 'Kerja'   => 'info',
        $sp === 'Aktif'   => 'success',
        $sp === 'Inaktif' => 'warning',
        $sp === 'Tidak diketahui' => 'secondary',
        default => 'danger',
    };
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-1">Berkas {{ $berkas->label }}</h4>
        <span class="badge bg-{{ $warnaStatus }}">{{ $berkas->label_status }}</span>
        <span class="badge bg-{{ $warnaSp }}">{{ $sp }}</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('berkas.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
        @if($boleh)
            <a href="{{ route('berkas.edit', $berkas) }}" class="btn btn-warning btn-sm">Ubah Berkas</a>
        @endif
        <a href="{{ route('berkas.cetak-isi', $berkas) }}" target="_blank"
           class="btn btn-outline-secondary btn-sm">Cetak Daftar Isi</a>
        <a href="{{ route('berkas.ekspor-isi-excel', $berkas) }}"
           class="btn btn-outline-secondary btn-sm">Ekspor Excel</a>
    </div>
</div>

@if($berkas->catatan_verifikasi)
    <div class="alert alert-warning">
        <strong>Catatan dari Unit Kearsipan:</strong> {{ $berkas->catatan_verifikasi }}
    </div>
@endif

@if($berkas->terverifikasi() && ! $kearsipan)
    <div class="alert alert-info small">
        Berkas sudah terverifikasi dan terkunci. Hubungi Unit Kearsipan bila perlu mengubahnya.
    </div>
@endif

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header fw-bold">Data Berkas</div>
            <div class="card-body p-0">
                <table class="table tabel-detail mb-0">
                    <tr><th>Unit Pengolah</th><td>{{ $berkas->unit?->label }}</td></tr>
                    @if($berkas->sub_bagian)
                        <tr><th>Sub Bagian</th><td>{{ $berkas->sub_bagian }}</td></tr>
                    @endif
                    <tr><th>Uraian Informasi Berkas</th><td>{{ $berkas->uraian }}</td></tr>
                    <tr><th>Kurun Waktu</th><td>{{ $berkas->kurun_waktu }}</td></tr>
                    <tr>
                        <th>Jumlah</th>
                        <td>
                            {{ $berkas->item->count() }} item
                            @if($berkas->jumlah_fisik)
                                <span class="text-muted">· fisik: {{ $berkas->jumlah_fisik }} {{ $berkas->satuan }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr><th>SKKAD</th><td>{{ $berkas->skkad }}</td></tr>
                    <tr>
                        <th>Penyimpanan</th>
                        <td>
                            {{ $berkas->boks?->label ?? 'Boks belum ditentukan' }}
                            @if($berkas->lokasi_simpan)
                                <br><small class="text-muted">{{ $berkas->lokasi_simpan }}</small>
                            @endif
                        </td>
                    </tr>
                    @if($berkas->keterangan)
                        <tr><th>Keterangan</th><td>{{ $berkas->keterangan }}</td></tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card shadow-sm mb-3">
            <div class="card-header fw-bold">Klasifikasi &amp; Retensi</div>
            <div class="card-body p-0">
                @if($berkas->klasifikasi)
                    <table class="table tabel-detail mb-0">
                        <tr><th>Kode</th><td class="fw-bold">{{ $berkas->kode_klasifikasi }}</td></tr>
                        <tr><th>Uraian JRA</th><td>{{ $berkas->klasifikasi->uraian }}</td></tr>
                        <tr><th>Retensi Aktif</th><td>{{ $berkas->klasifikasi->retensi_aktif }} tahun</td></tr>
                        <tr><th>Retensi Inaktif</th><td>{{ $berkas->klasifikasi->retensi_inaktif }} tahun</td></tr>
                        <tr><th>Status Akhir</th><td class="fw-bold">{{ $berkas->klasifikasi->nasib_akhir }}</td></tr>
                        <tr class="table-light">
                            <th>Perhitungan mulai</th>
                            <td>{{ $berkas->awal_retensi ?? '-' }}</td>
                        </tr>
                        <tr><th>Pindah inaktif</th><td>{{ $berkas->jatuh_tempo_inaktif ?? '-' }}</td></tr>
                        <tr><th>{{ $berkas->klasifikasi->nasib_akhir }} pada</th><td>{{ $berkas->jatuh_tempo_akhir ?? '-' }}</td></tr>
                    </table>
                @else
                    <div class="p-4 text-center text-muted">
                        Berkas belum diklasifikasi, retensinya belum dapat dihitung.
                    </div>
                @endif
            </div>
        </div>

        {{-- Alur verifikasi --}}
        <div class="card shadow-sm">
            <div class="card-header fw-bold">Verifikasi</div>
            <div class="card-body">
                @if($berkas->status === 'draf')
                    @if($boleh)
                        <p class="small text-muted">Setelah item lengkap, ajukan berkas ini untuk diverifikasi Unit Kearsipan.</p>
                        <form action="{{ route('berkas.ajukan', $berkas) }}" method="POST"
                              onsubmit="return confirm('Ajukan berkas ini untuk diverifikasi?')">
                            @csrf
                            <button class="btn btn-primary w-100" @disabled($berkas->item->isEmpty())>
                                Ajukan Verifikasi
                            </button>
                        </form>
                        @if($berkas->item->isEmpty())
                            <small class="text-danger">Berkas kosong tidak dapat diajukan.</small>
                        @endif
                    @else
                        <p class="small text-muted mb-0">Berkas masih berstatus draf di unit.</p>
                    @endif

                @elseif($berkas->status === 'diajukan')
                    <p class="small text-muted">
                        Diajukan {{ $berkas->diajukan_pada?->translatedFormat('d F Y, H:i') }}.
                    </p>
                    @if($kearsipan)
                        <form action="{{ route('berkas.verifikasi', $berkas) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <textarea name="catatan" class="form-control form-control-sm" rows="2"
                                          placeholder="Catatan (wajib bila dikembalikan)"></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button name="keputusan" value="terima" class="btn btn-success flex-grow-1">Terima</button>
                                <button name="keputusan" value="kembalikan" class="btn btn-outline-danger flex-grow-1">Kembalikan</button>
                            </div>
                        </form>
                    @else
                        <p class="small mb-0">Menunggu keputusan Unit Kearsipan.</p>
                    @endif

                @else
                    <p class="small mb-1">
                        Diverifikasi {{ $berkas->diverifikasi_pada?->translatedFormat('d F Y, H:i') }}
                        oleh {{ $berkas->verifikator?->name ?? '-' }}.
                    </p>
                    @if($kearsipan)
                        <hr>
                        <form action="{{ route('berkas.buka-kunci', $berkas) }}" method="POST"
                              onsubmit="return confirm('Buka kunci berkas ini agar unit dapat mengubahnya?')">
                            @csrf
                            <div class="mb-2">
                                <textarea name="catatan" class="form-control form-control-sm" rows="2"
                                          placeholder="Alasan pembukaan kunci" required></textarea>
                            </div>
                            <button class="btn btn-outline-warning btn-sm w-100">Buka Kunci</button>
                        </form>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Daftar isi berkas --}}
<div class="card shadow-sm mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-bold">Daftar Isi Berkas ({{ $berkas->item->count() }} item)</span>
        @if($boleh)
            <div class="d-flex gap-2">
                <a href="{{ route('item-berkas.pilih', $berkas) }}" class="btn btn-sm btn-outline-primary">
                    Ambil dari Buku Agenda
                </a>
                <a href="{{ route('item-berkas.create', $berkas) }}" class="btn btn-sm btn-primary">
                    + Item Manual
                </a>
            </div>
        @endif
    </div>
    <div class="table-responsive">
        <table class="table table-hover tabel-item mb-0" style="font-size: 0.88rem;">
            <thead class="table-dark">
                <tr>
                    <th style="width: 55px;">No<br>Item</th>
                    <th style="width: 180px;">Nomor Surat/Dokumen</th>
                    <th>Uraian Informasi Arsip</th>
                    <th style="width: 100px;">Tanggal</th>
                    <th style="width: 130px;">Klasifikasi</th>
                    <th style="width: 90px;" class="text-center">Jumlah</th>
                    <th style="width: 110px;">SKKAD</th>
                    <th style="width: 100px;">Sumber</th>
                    @if($boleh)<th style="width: 110px;" class="text-center">Aksi</th>@endif
                </tr>
            </thead>
            <tbody>
            @forelse($berkas->item as $it)
                <tr>
                    <td class="text-center fw-bold">{{ $it->nomor_item }}</td>
                    <td>{{ $it->nomor ?: '-' }}</td>
                    <td>{{ Str::limit($it->isi, 90) }}</td>
                    <td class="text-center">{{ $it->tanggal_tampil ?: '-' }}</td>
                    <td><small>{{ $it->kode ?: '-' }}</small></td>
                    <td class="text-center">{{ $it->jumlah ? $it->jumlah . ' ' . $it->satuan : '-' }}</td>
                    <td><small>{{ $it->skkad }}</small></td>
                    <td>
                        <span class="badge bg-{{ $it->dariArsip() ? 'info' : 'secondary' }}">{{ $it->sumber }}</span>
                    </td>
                    @if($boleh)
                        <td class="text-center">
                            @if(! $it->dariArsip())
                                <a href="{{ route('item-berkas.edit', [$berkas, $it]) }}" class="btn btn-sm btn-warning">Ubah</a>
                            @endif
                            <form action="{{ route('item-berkas.destroy', [$berkas, $it]) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Keluarkan item {{ $it->nomor_item }} dari berkas ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Keluarkan</button>
                            </form>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $boleh ? 9 : 8 }}" class="text-center py-4 text-muted">
                        Berkas masih kosong. Tambahkan item dari buku agenda atau ketik manual.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@if(auth()->user()->lihatSemuaUnit())
    @include('jejak._riwayat', ['objek' => $berkas, 'batas' => 15])
@endif
@endsection