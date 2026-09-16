<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('judul', 'Cetak')</title>
    <style>
        @page { size: A4 landscape; margin: 12mm; }

        body {
            font-family: "Times New Roman", serif;
            font-size: 11pt;
            color: #000;
            margin: 0;
        }

        .kepala { text-align: center; margin-bottom: 4mm; }
        .kepala h2 { margin: 0 0 2mm; font-size: 14pt; text-transform: uppercase; }
        .kepala p  { margin: 0; font-size: 11pt; }

        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 3px 5px; vertical-align: top; }
        thead th { background: #e9e9e9; text-align: center; font-weight: bold; }
        .nomor-kolom td { text-align: center; font-size: 9pt; font-style: italic; }
        .tengah { text-align: center; }
        .kecil { font-size: 9pt; }

        .alat-cetak {
            margin-bottom: 5mm; padding: 8px; background: #f0f0f0;
            border: 1px solid #ccc; font-family: sans-serif; font-size: 10pt;
        }
        .alat-cetak button, .alat-cetak a {
            padding: 5px 14px; margin-right: 6px; cursor: pointer;
            font-size: 10pt; text-decoration: none; color: #000;
            border: 1px solid #888; background: #fff; display: inline-block;
        }

        @media print {
            .alat-cetak { display: none; }
            thead { display: table-header-group; }
            tr { page-break-inside: avoid; }
        }
    </style>
    @stack('gaya')
</head>
<body>

<div class="alat-cetak">
    <button onclick="window.print()">Cetak / Simpan PDF</button>
    <a href="{{ url()->previous() }}">Kembali</a>
    <span style="margin-left:10px; color:#555;">
        Pada kotak cetak, pilih "Save as PDF" bila ingin menyimpan sebagai berkas.
    </span>
</div>

@yield('isi')

@stack('skrip')
</body>
</html>