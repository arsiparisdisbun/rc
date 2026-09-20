@extends('layouts.cetak')
@section('judul', 'Buku Agenda Surat Masuk')

@section('isi')
<div class="kepala">
    <h2>Buku Agenda Surat Masuk</h2>
    <p>Dinas Perkebunan Provinsi Jawa Timur{{ $periode ? ' — ' . $periode : '' }}</p>
</div>

<table style="font-size: 8.5pt;">
    <thead>
        <tr>
            <th style="width: 28px;">No<br>Urut</th>
            <th style="width: 35px;">No<br>TNDE</th>
            <th style="width: 50px;">Tgl<br>Terima</th>
            <th style="width: 50px;">Tgl<br>Surat</th>
            <th style="width: 105px;">Nomor Surat</th>
            <th style="width: 55px;">Sifat</th>
            <th style="width: 45px;">Lampiran</th>
            <th>Isi Ringkas</th>
            <th style="width: 100px;">Dari</th>
            <th style="width: 95px;">Kepada</th>
            <th style="width: 45px;">Tkt<br>Perkemb.</th>
            <th style="width: 60px;">Kode<br>Klasifikasi</th>
        </tr>
        <tr class="nomor-kolom">
            @for($i = 1; $i <= 12; $i++)<td>({{ $i }})</td>@endfor
        </tr>
    </thead>
    <tbody>
    @forelse($arsip as $a)
        <tr>
            <td class="tengah">{{ $a->no_urut }}</td>
            <td class="tengah">{{ $a->no_tnde ?: '-' }}</td>
            <td class="tengah">{{ $a->tanggal_penerimaan?->format('d/m/Y') }}</td>
            <td class="tengah">{{ $a->tanggal_surat?->format('d/m/Y') }}</td>
            <td>{{ $a->nomor_surat }}</td>
            <td class="kecil">{{ $a->sifat }}</td>
            <td class="tengah kecil">{{ $a->lampiran ?: '-' }}</td>
            <td>{{ $a->isi_ringkas }}</td>
            <td class="kecil">{{ $a->dari }}</td>
            <td class="kecil">{{ $a->kepada }}</td>
            <td class="tengah kecil">{{ $a->tingkat_perkembangan ?: '-' }}</td>
            <td class="tengah">{{ $a->kode_klasifikasi ?: '-' }}</td>
        </tr>
    @empty
        <tr><td colspan="12" class="tengah">Tidak ada data.</td></tr>
    @endforelse
    </tbody>
</table>

<p class="kecil" style="margin-top:4mm;">
    Jumlah: {{ number_format($arsip->count()) }} surat. Dicetak {{ now()->translatedFormat('d F Y, H:i') }}.
</p>
@endsection