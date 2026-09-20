@extends('layouts.cetak')
@section('judul', 'Buku Agenda Surat Keluar')

@section('isi')
<div class="kepala">
    <h2>Buku Agenda Surat Keluar</h2>
    <p>{{ $namaUnit }}{{ $periode ? ' — ' . $periode : '' }}</p>
</div>

<table style="font-size: 8.5pt;">
    <thead>
        <tr>
            <th style="width: 45px;">No<br>Agenda</th>
            <th style="width: 60px;">Jenis<br>Naskah</th>
            <th style="width: 50px;">Sifat</th>
            <th style="width: 48px;">Tgl<br>Verifikasi</th>
            <th style="width: 105px;">Nomor Surat</th>
            <th style="width: 55px;">Kode<br>Klasifikasi</th>
            <th style="width: 48px;">Tgl<br>Surat</th>
            <th style="width: 32px;">Jml<br>Lbr</th>
            <th style="width: 38px;">Unit<br>Kerja</th>
            <th>Isi Ringkas</th>
            <th style="width: 95px;">Kepada</th>
            <th style="width: 42px;">Tkt<br>Perkemb.</th>
            <th style="width: 75px;">Pembuat</th>
        </tr>
        <tr class="nomor-kolom">
            @for($i = 1; $i <= 13; $i++)<td>({{ $i }})</td>@endfor
        </tr>
    </thead>
    <tbody>
    @forelse($arsip as $a)
        <tr>
            <td class="tengah">{{ $a->no_urut }}</td>
            <td class="kecil">{{ $a->jenisNaskah?->nama ?: '-' }}</td>
            <td class="kecil">{{ $a->sifat }}</td>
            <td class="tengah kecil">{{ $a->tanggal_verifikasi?->format('d/m/Y') ?: '-' }}</td>
            <td>{{ $a->nomor_surat }}</td>
            <td class="tengah">{{ $a->kode_klasifikasi ?: '-' }}</td>
            <td class="tengah kecil">{{ $a->tanggal_surat?->format('d/m/Y') }}</td>
            <td class="tengah">{{ $a->jumlah_lembar ?: '-' }}</td>
            <td class="tengah kecil">{{ $a->unit_pengolah }}</td>
            <td>{{ $a->isi_ringkas }}</td>
            <td class="kecil">{{ $a->kepada }}</td>
            <td class="tengah kecil">{{ $a->tingkat_perkembangan ?: '-' }}</td>
            <td class="kecil">{{ $a->pembuat ?: '-' }}</td>
        </tr>
    @empty
        <tr><td colspan="13" class="tengah">Tidak ada data.</td></tr>
    @endforelse
    </tbody>
</table>

<p class="kecil" style="margin-top:4mm;">
    Jumlah: {{ number_format($arsip->count()) }} surat. Dicetak {{ now()->translatedFormat('d F Y, H:i') }}.
</p>
@endsection