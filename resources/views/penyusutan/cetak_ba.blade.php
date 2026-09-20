@extends('layouts.cetak')
@section('judul', 'Berita Acara ' . $penyusutan->label_jenis)

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
    .ttd td { text-align: center; vertical-align: top; padding-top: 2mm; }
    .ttd .ruang { height: 20mm; }
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
    $p = $penyusutan;
    $tgl = $p->tanggal_ba ?? now();
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
    <p>{{ $p->musnah() ? 'PEMUSNAHAN ARSIP' : 'PENYERAHAN ARSIP STATIS' }}</p>
    <p class="nomor">NOMOR: {{ $p->nomor_ba ?: '................................' }}</p>
</div>

<div class="isi">
    <p class="menjorok">
        Pada hari ini {{ $tgl->translatedFormat('l') }} tanggal {{ $tgl->translatedFormat('d') }}
        bulan {{ $tgl->translatedFormat('F') }} tahun {{ $tgl->translatedFormat('Y') }},
        yang bertandatangan dibawah ini,
    </p>

    <table class="pejabat">
        <tr><td>Nama</td><td>:</td><td>{{ $p->pihak1_nama }}</td></tr>
        <tr><td>NIP</td><td>:</td><td>{{ $p->pihak1_nip ?: '-' }}</td></tr>
        <tr><td>Pangkat</td><td>:</td><td>{{ $p->pihak1_pangkat ?: '-' }}</td></tr>
        <tr><td>Jabatan</td><td>:</td><td>{{ $p->pihak1_jabatan }}</td></tr>
    </table>

    @if($p->musnah())
        <p class="menjorok">
            Dalam hal ini bertindak dan atas nama Unit Kearsipan Dinas Perkebunan Provinsi Jawa Timur,
            telah melaksanakan pemusnahan arsip sejumlah <strong>{{ $p->jumlah_berkas }}</strong> nomor berkas
            @if($p->jumlah_boks) dalam <strong>{{ $p->jumlah_boks }}</strong> boks @endif
            sebagaimana tercantum pada daftar arsip usul musnah sebagaimana terlampir.
        </p>

        <p class="menjorok">
            Pemusnahan dilaksanakan di {{ $p->tempat }} dengan cara
            <strong>{{ strtolower($p->cara) }}</strong>, sehingga arsip tersebut tidak dapat dikenali
            baik isi maupun bentuknya.
        </p>

        <p class="menjorok">Pemusnahan arsip ini disaksikan oleh:</p>

        <table class="pejabat">
            <tr><td>1. Nama</td><td>:</td><td>{{ $p->saksi1_nama }}</td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp; NIP</td><td>:</td><td>{{ $p->saksi1_nip ?: '-' }}</td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp; Jabatan</td><td>:</td><td>{{ $p->saksi1_jabatan }}</td></tr>
            <tr><td style="padding-top:2mm;">2. Nama</td><td style="padding-top:2mm;">:</td><td style="padding-top:2mm;">{{ $p->saksi2_nama }}</td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp; NIP</td><td>:</td><td>{{ $p->saksi2_nip ?: '-' }}</td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp; Jabatan</td><td>:</td><td>{{ $p->saksi2_jabatan }}</td></tr>
        </table>
    @else
        <p class="menjorok">
            Dalam hal ini bertindak dan atas nama Unit Kearsipan Dinas Perkebunan Provinsi Jawa Timur,
            selanjutnya disebut sebagai PIHAK I;
        </p>

        <table class="pejabat">
            <tr><td>Nama</td><td>:</td><td>{{ $p->pihak2_nama }}</td></tr>
            <tr><td>NIP</td><td>:</td><td>{{ $p->pihak2_nip ?: '-' }}</td></tr>
            <tr><td>Pangkat</td><td>:</td><td>{{ $p->pihak2_pangkat ?: '-' }}</td></tr>
            <tr><td>Jabatan</td><td>:</td><td>{{ $p->pihak2_jabatan }}</td></tr>
        </table>

        <p class="menjorok">
            Dalam hal ini bertindak dan atas nama {{ $p->pihak2_instansi }},
            yang selanjutnya disebut sebagai PIHAK II;
        </p>

        <p class="menjorok">
            PIHAK I menyerahkan arsip statis sejumlah <strong>{{ $p->jumlah_berkas }}</strong> nomor berkas
            @if($p->jumlah_boks) dalam <strong>{{ $p->jumlah_boks }}</strong> boks @endif
            sebagaimana tercantum pada daftar arsip statis sebagaimana terlampir kepada PIHAK II.
        </p>

        <p class="menjorok">
            PIHAK II telah menerima penyerahan arsip statis tersebut dari PIHAK I untuk dikelola
            dan dilestarikan sesuai dengan peraturan perundangan yang berlaku.
        </p>
    @endif

    <p class="menjorok">
        Demikian Berita Acara ini dibuat dalam rangkap 2 (dua) untuk dipergunakan sebagaimana mestinya.
    </p>
</div>

@if($p->musnah())
    <table class="ttd">
        <tr>
            <td colspan="2" style="text-align:right;">Surabaya, {{ $tgl->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td style="width:50%;"><strong>Saksi 1</strong></td>
            <td style="width:50%;"><strong>Yang Melaksanakan</strong></td>
        </tr>
        <tr>
            <td class="ruang"></td>
            <td class="ruang"></td>
        </tr>
        <tr>
            <td>
                <span class="nama">{{ $p->saksi1_nama }}</span><br>
                NIP {{ $p->saksi1_nip ?: '-' }}
            </td>
            <td>
                <span class="nama">{{ $p->pihak1_nama }}</span><br>
                NIP {{ $p->pihak1_nip ?: '-' }}
            </td>
        </tr>
        <tr>
            <td style="padding-top:8mm;"><strong>Saksi 2</strong></td>
            <td></td>
        </tr>
        <tr>
            <td class="ruang"></td>
            <td></td>
        </tr>
        <tr>
            <td>
                <span class="nama">{{ $p->saksi2_nama }}</span><br>
                NIP {{ $p->saksi2_nip ?: '-' }}
            </td>
            <td></td>
        </tr>
    </table>
@else
    <table class="ttd">
        <tr>
            <td style="width:50%;"></td>
            <td style="width:50%;">Surabaya, {{ $tgl->translatedFormat('d F Y') }}</td>
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
                <span class="nama">{{ $p->pihak2_nama }}</span><br>
                NIP {{ $p->pihak2_nip ?: '-' }}
            </td>
            <td>
                <span class="nama">{{ $p->pihak1_nama }}</span><br>
                NIP {{ $p->pihak1_nip ?: '-' }}
            </td>
        </tr>
    </table>
@endif

{{-- Lampiran --}}
<div class="lampiran">
    <h4>
        {{ $p->musnah() ? 'DAFTAR ARSIP YANG DIMUSNAHKAN' : 'DAFTAR ARSIP STATIS YANG DISERAHKAN' }}<br>
        <span style="font-weight:normal; font-size:10pt;">
            Lampiran Berita Acara Nomor: {{ $p->nomor_ba ?: '-' }}
        </span>
    </h4>

    <table class="tabel-lampiran">
        <thead>
            <tr>
                <th style="width: 28px;">No</th>
                <th style="width: 50px;">Unit<br>Pengolah</th>
                <th style="width: 50px;">No<br>Berkas</th>
                <th style="width: 65px;">Kode<br>Klasifikasi</th>
                <th>Uraian Informasi Berkas</th>
                <th style="width: 55px;">Kurun<br>Waktu</th>
                <th style="width: 38px;">Jml</th>
                <th style="width: 45px;">Satuan</th>
                <th style="width: 55px;">Nasib<br>Akhir</th>
                <th style="width: 50px;">Boks</th>
            </tr>
        </thead>
        <tbody>
        @foreach($p->berkas as $i => $b)
            <tr>
                <td class="tengah">{{ $i + 1 }}</td>
                <td class="tengah">{{ $b->unit_pengolah }}</td>
                <td class="tengah">{{ $b->label }}</td>
                <td class="tengah">{{ $b->kode_klasifikasi ?: '-' }}</td>
                <td>{{ $b->uraian }}</td>
                <td class="tengah">{{ $b->kurun_waktu }}</td>
                <td class="tengah">{{ $b->jumlah_fisik ?: '-' }}</td>
                <td class="tengah">{{ $b->satuan }}</td>
                <td class="tengah">{{ $b->klasifikasi?->nasib_akhir ?? '-' }}</td>
                <td class="tengah">{{ $b->boks?->nomor ?? '-' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <p style="font-size:9pt; margin-top:4mm;">
        Jumlah: {{ $p->jumlah_berkas }} berkas
        @if($p->jumlah_boks) dalam {{ $p->jumlah_boks }} boks @endif.
    </p>
</div>
@endsection