@extends('layouts.cetak')
@section('judul', "Daftar Isi Berkas {$berkas->label}")

@section('isi')
<div class="kepala">
    <h2>Daftar Isi Berkas</h2>
    <p>Unit Kerja: {{ $berkas->unit?->label }}</p>
</div>

<table style="margin-bottom: 4mm;">
    <tr>
        <td style="width: 110px; background:#f5f5f5;"><strong>No Berkas</strong></td>
        <td style="width: 150px;">{{ $berkas->label }}</td>
        <td style="width: 110px; background:#f5f5f5;"><strong>Kode Klasifikasi</strong></td>
        <td>{{ $berkas->kode_klasifikasi ?: '-' }}</td>
    </tr>
    <tr>
        <td style="background:#f5f5f5;"><strong>Uraian Berkas</strong></td>
        <td colspan="3">{{ $berkas->uraian }}</td>
    </tr>
    <tr>
        <td style="background:#f5f5f5;"><strong>Kurun Waktu</strong></td>
        <td>{{ $berkas->kurun_waktu }}</td>
        <td style="background:#f5f5f5;"><strong>Lokasi Simpan</strong></td>
        <td>{{ $berkas->boks?->label }} {{ $berkas->lokasi_simpan ? '— ' . $berkas->lokasi_simpan : '' }}</td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th style="width: 30px;">No<br>Urut</th>
            <th style="width: 55px;">Kode Unit<br>Pengolah</th>
            <th style="width: 110px;">Nama Unit<br>Pengolah</th>
            <th style="width: 45px;">No<br>Berkas</th>
            <th style="width: 120px;">Nomor Surat/<br>Dokumen</th>
            <th style="width: 40px;">No Item<br>Arsip</th>
            <th style="width: 65px;">Kode<br>Klasifikasi</th>
            <th>Uraian Informasi Arsip</th>
            <th style="width: 65px;">Tanggal/<br>Tahun</th>
            <th style="width: 40px;">Jumlah</th>
            <th style="width: 45px;">Satuan</th>
            <th style="width: 65px;">Ket. SKKAD</th>
            <th style="width: 70px;">Keterangan</th>
        </tr>
        <tr class="nomor-kolom">
            @for($i = 1; $i <= 13; $i++)<td>({{ $i }})</td>@endfor
        </tr>
    </thead>
    <tbody>
    @forelse($berkas->item as $i => $it)
        <tr>
            <td class="tengah">{{ $i + 1 }}</td>
            <td class="tengah">{{ $berkas->unit_pengolah }}</td>
            <td class="kecil">{{ $berkas->unit?->nama }}</td>
            <td class="tengah">{{ $berkas->no_berkas }}/{{ $berkas->tahun }}</td>
            <td class="kecil">{{ $it->nomor ?: '-' }}</td>
            <td class="tengah">{{ $it->nomor_item }}</td>
            <td class="tengah">{{ $it->kode ?: '-' }}</td>
            <td>{{ $it->isi }}</td>
            <td class="tengah kecil">{{ $it->tanggal_tampil ?: '-' }}</td>
            <td class="tengah">{{ $it->jumlah ?: '-' }}</td>
            <td class="tengah">{{ $it->satuan }}</td>
            <td class="tengah kecil">{{ $it->skkad }}</td>
            <td class="kecil">{{ $it->keterangan }}</td>
        </tr>
    @empty
        <tr><td colspan="13" class="tengah">Berkas masih kosong.</td></tr>
    @endforelse
    </tbody>
</table>

<p class="kecil" style="margin-top:4mm;">
    Jumlah item: {{ $berkas->item->count() }}. Dicetak {{ now()->translatedFormat('d F Y, H:i') }}.
</p>
@endsection