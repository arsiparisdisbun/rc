@extends('layouts.cetak')
@section('judul', 'Daftar Berkas')

@section('isi')
<div class="kepala">
    <h2>Daftar Berkas</h2>
    <p>Unit Kerja: {{ $namaUnit }}</p>
</div>

<table>
    <thead>
        <tr>
            <th style="width: 30px;">No<br>Urut</th>
            <th style="width: 55px;">Kode Unit<br>Pengolah</th>
            <th style="width: 110px;">Nama Unit<br>Pengolah</th>
            <th style="width: 45px;">No<br>Berkas</th>
            <th style="width: 65px;">Kode<br>Klasifikasi</th>
            <th>Uraian Informasi Berkas</th>
            <th style="width: 55px;">Kurun<br>Waktu</th>
            <th style="width: 40px;">Jumlah</th>
            <th style="width: 45px;">Satuan</th>
            <th style="width: 65px;">Ket. SKKAD</th>
            <th style="width: 85px;">Lokasi Simpan</th>
            <th style="width: 40px;">Retensi<br>Aktif</th>
            <th style="width: 40px;">Retensi<br>Inaktif</th>
            <th style="width: 55px;">Status<br>Akhir</th>
            <th style="width: 70px;">Keterangan</th>
        </tr>
        <tr class="nomor-kolom">
            @for($i = 1; $i <= 15; $i++)<td>({{ $i }})</td>@endfor
        </tr>
    </thead>
    <tbody>
    @forelse($berkas as $i => $b)
        <tr>
            <td class="tengah">{{ $i + 1 }}</td>
            <td class="tengah">{{ $b->unit_pengolah }}</td>
            <td class="kecil">{{ $b->unit?->nama }}</td>
            <td class="tengah">{{ $b->no_berkas }}/{{ $b->tahun }}</td>
            <td class="tengah">{{ $b->kode_klasifikasi ?: '-' }}</td>
            <td>
                {{ $b->uraian }}
                @if($b->sub_bagian)<br><span class="kecil">({{ $b->sub_bagian }})</span>@endif
            </td>
            <td class="tengah">{{ $b->kurun_waktu }}</td>
            <td class="tengah">{{ $b->jumlah_fisik ?: $b->item_count }}</td>
            <td class="tengah">{{ $b->satuan }}</td>
            <td class="tengah kecil">{{ $b->skkad }}</td>
            <td class="kecil">
                {{ $b->boks?->label }}
                @if($b->lokasi_simpan)<br>{{ $b->lokasi_simpan }}@endif
            </td>
            <td class="tengah">{{ $b->klasifikasi?->retensi_aktif ?? '-' }}</td>
            <td class="tengah">{{ $b->klasifikasi?->retensi_inaktif ?? '-' }}</td>
            <td class="tengah">{{ $b->klasifikasi?->nasib_akhir ?? '-' }}</td>
            <td class="kecil">
                {{ $b->status_penyimpanan }}
                @if($b->keterangan)<br>{{ $b->keterangan }}@endif
            </td>
        </tr>
    @empty
        <tr><td colspan="15" class="tengah">Tidak ada berkas.</td></tr>
    @endforelse
    </tbody>
</table>

<p class="kecil" style="margin-top:4mm;">
    Jumlah berkas: {{ $berkas->count() }}. Dicetak {{ now()->translatedFormat('d F Y, H:i') }}.
</p>
@endsection