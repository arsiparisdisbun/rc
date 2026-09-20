@extends('layouts.cetak')
@section('judul', 'Berita Acara Pemindahan Arsip Inaktif')

@push('gaya')
<style>
    @page { size: A4 portrait; margin: 20mm 20mm 15mm; }

    body { font-size: 12pt; line-height: 1.5; }

    .kop {
        display: flex; align-items: center; gap: 5mm;
        border-bottom: 3px solid #000; padding-bottom: 3mm; margin-bottom: 8mm;
    }
    .kop img { width: 22mm; }
    .kop .teks { flex: 1; text-align: center; }
    .kop .teks p { margin: 0; line-height: 1.3; }
    .kop .instansi { font-size: 14pt; font-weight: bold; }
    .kop .alamat { font-size: 9pt; }

    .judul-ba { text-align: center; margin-bottom: 6mm; }
    .judul-ba p { margin: 0; font-weight: bold; }
    .judul-ba .nomor { font-weight: normal; margin-top: 2mm; }

    .isi p { margin: 0 0 3mm; text-align: justify; }
    .isi .menjorok { text-indent: 10mm; }

    .pejabat { margin: 0 0 3mm 10mm; }
    .pejabat td { padding: 0.5mm 0; vertical-align: top; }
    .pejabat td:first-child { width: 28mm; }
    .pejabat td:nth-child(2) { width: 5mm; }

    .ttd { width: 100%; margin-top: 8mm; }
    .ttd td { text-align: center; vertical-align: top; width: 50%; padding-top: 2mm; }
    .ttd .ruang { height: 22mm; }
    .ttd .nama { font-weight: bold; text-decoration: underline; }

    .lampiran { page-break-before: always; }
    .lampiran h4 { text-align: center; margin: 0 0 5mm; }
    .tabel-lampiran { font-size: 9pt; }
    .tabel-lampiran th, .tabel-lampiran td { border: 1px solid #000; padding: 2px 4px; }
    .tabel-lampiran thead th { background: #e9e9e9; text-align: center; }
</style>
@endpush

@section('isi')
@php
    $p = $pemindahan;
    $tgl = $p->tanggal_ba ?? $p->diproses_pada ?? $p->diajukan_pada ?? now();

    $hari = $tgl->translatedFormat('l');
    $tanggalTeks = $tgl->translatedFormat('d');
    $bulanTeks = $tgl->translatedFormat('F');
    $tahunTeks = $tgl->translatedFormat('Y');
@endphp

<div class="kop">
    <img src="{{ asset('img/logo-jatim.png') }}" alt="Logo">
    <div class="teks">
        <p>PEMERINTAH PROVINSI JAWA TIMUR</p>
        <p class="instansi">DINAS PERKEBUNAN</p>
        <p class="alamat">Jalan Gayung Kebonsari Nomor 173 Surabaya 60235</p>
        <p class="alamat">Telepon (031) 8290595, Laman disbun.jatimprov.go.id</p>
    </div>
</div>

<div class="judul-ba">
    <p>BERITA ACARA</p>
    <p>PEMINDAHAN ARSIP INAKTIF</p>
    <p class="nomor">NOMOR: {{ $p->nomor_ba ?: '................................' }}</p>
</div>

<div class="isi">
    <p class="menjorok">
        Pada hari ini {{ $hari }} tanggal {{ $tanggalTeks }} bulan {{ $bulanTeks }}
        tahun {{ $tahunTeks }} yang bertandatangan dibawah ini,
    </p>

    <table class="pejabat">
        <tr><td>Nama</td><td>:</td><td>{{ $p->pihak1_nama }}</td></tr>
        <tr><td>NIP</td><td>:</td><td>{{ $p->pihak1_nip ?: '-' }}</td></tr>
        <tr><td>Pangkat</td><td>:</td><td>{{ $p->pihak1_pangkat ?: '-' }}</td></tr>
        <tr><td>Jabatan</td><td>:</td><td>{{ $p->pihak1_jabatan }}</td></tr>
    </table>

    <p class="menjorok">
        Dalam hal ini bertindak dan atas nama Unit Pengolah {{ $p->unit?->nama }}
        selanjutnya disebut sebagai PIHAK I;
    </p>

    <table class="pejabat">
        <tr><td>Nama</td><td>:</td><td>{{ $p->pihak2_nama ?: '................................' }}</td></tr>
        <tr><td>NIP</td><td>:</td><td>{{ $p->pihak2_nip ?: '................................' }}</td></tr>
        <tr><td>Pangkat</td><td>:</td><td>{{ $p->pihak2_pangkat ?: '................................' }}</td></tr>
        <tr><td>Jabatan</td><td>:</td><td>{{ $p->pihak2_jabatan ?: '................................' }}</td></tr>
    </table>

    <p class="menjorok">
        Dalam hal ini bertindak dan atas nama Unit Kearsipan Dinas Perkebunan Provinsi Jawa Timur
        yang selanjutnya disebut sebagai PIHAK II;
    </p>

    <p class="menjorok">
        PIHAK I memindahkan arsip dinamis sejumlah <strong>{{ $p->jumlah_boks }}</strong> boks,
        <strong>{{ $p->jumlah_berkas }}</strong> nomor berkas sebagaimana tercantum pada daftar
        arsip dinamis sebagaimana terlampir kepada PIHAK II.
    </p>

    <p class="menjorok">
        PIHAK II telah menerima pemindahan arsip dinamis sesuai daftar arsip dinamis tersebut
        dari PIHAK I untuk dikelola dan didayagunakan sesuai dengan peraturan perundangan yang berlaku.
    </p>

    <p class="menjorok">
        Demikian Berita Acara ini dibuat dalam rangkap 2 (dua), untuk para pihak dan
        dipergunakan sebagaimana mestinya.
    </p>
</div>

<table class="ttd">
    <tr>
        <td></td>
        <td>Surabaya, {{ $tgl->translatedFormat('d F Y') }}</td>
    </tr>
    <tr>
        <td><strong>PIHAK II</strong></td>
        <td><strong>PIHAK I</strong></td>
    </tr>
    <tr>
        <td class="ruang"></td>
        <td class="ruang"></td>
    </tr>
    <tr>
        <td>
            <span class="nama">{{ $p->pihak2_nama ?: '................................' }}</span><br>
            NIP {{ $p->pihak2_nip ?: '................................' }}
        </td>
        <td>
            <span class="nama">{{ $p->pihak1_nama }}</span><br>
            NIP {{ $p->pihak1_nip ?: '-' }}
        </td>
    </tr>
</table>

{{-- Lampiran daftar arsip --}}
<div class="lampiran">
    <h4>DAFTAR ARSIP DINAMIS YANG DIPINDAHKAN<br>
        <span style="font-weight:normal; font-size:10pt;">
            Lampiran Berita Acara Nomor: {{ $p->nomor_ba ?: '-' }}
        </span>
    </h4>

    <table class="tabel-lampiran">
        <thead>
            <tr>
                <th style="width: 28px;">No</th>
                <th style="width: 55px;">No<br>Berkas</th>
                <th style="width: 70px;">Kode<br>Klasifikasi</th>
                <th>Uraian Informasi Berkas</th>
                <th style="width: 55px;">Kurun<br>Waktu</th>
                <th style="width: 40px;">Jumlah</th>
                <th style="width: 45px;">Satuan</th>
                <th style="width: 60px;">Retensi<br>Inaktif</th>
                <th style="width: 55px;">Nasib<br>Akhir</th>
                <th style="width: 65px;">Boks</th>
            </tr>
        </thead>
        <tbody>
        @foreach($p->berkas as $i => $b)
            <tr>
                <td class="tengah">{{ $i + 1 }}</td>
                <td class="tengah">{{ $b->label }}</td>
                <td class="tengah">{{ $b->kode_klasifikasi ?: '-' }}</td>
                <td>{{ $b->uraian }}</td>
                <td class="tengah">{{ $b->kurun_waktu }}</td>
                <td class="tengah">{{ $b->jumlah_fisik ?: '-' }}</td>
                <td class="tengah">{{ $b->satuan }}</td>
                <td class="tengah">{{ $b->klasifikasi?->retensi_inaktif ?? '-' }} th</td>
                <td class="tengah">{{ $b->klasifikasi?->nasib_akhir ?? '-' }}</td>
                <td class="tengah">{{ $b->boks?->nomor ?? '-' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <p style="font-size:9pt; margin-top:4mm;">
        Jumlah: {{ $p->jumlah_berkas }} berkas dalam {{ $p->jumlah_boks }} boks.
    </p>
</div>
@endsection