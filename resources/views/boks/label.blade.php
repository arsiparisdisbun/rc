@extends('layouts.cetak')
@section('judul', 'Label Boks')

@push('gaya')
<style>
    @page { size: A4 portrait; margin: 8mm; }

    .lembar { display: flex; flex-wrap: wrap; gap: 4mm; }

    .label {
        width: 96mm; height: 68mm;
        border: 2px solid #000;
        padding: 5mm;
        box-sizing: border-box;
        display: flex;
        page-break-inside: avoid;
    }

    .label .isi { flex: 1; display: flex; flex-direction: column; justify-content: space-between; }
    .label .unit { font-size: 10pt; margin: 0 0 2mm; }
    .label .nomor { font-size: 30pt; font-weight: bold; line-height: 1; margin: 0; }
    .label .jenis { font-size: 12pt; text-transform: uppercase; letter-spacing: 1px; margin: 1mm 0 0; }
    .label .lokasi { font-size: 10pt; margin: 0; }
    .label .qr { width: 26mm; text-align: center; font-size: 7pt; }
    .label .qr canvas, .label .qr img { width: 26mm !important; height: 26mm !important; }
</style>
@endpush

@section('isi')
<div class="lembar">
    @forelse($boks as $b)
        <div class="label">
            <div class="isi">
                <div>
                    <p class="unit">{{ $b->unit?->nama }}<br><strong>{{ $b->unit_pengolah }}</strong></p>
                    <p class="nomor">BOKS {{ $b->nomor }}</p>
                    <p class="jenis">Arsip {{ $b->jenis }}</p>
                </div>
                <p class="lokasi">{{ $b->lokasi ?: '' }}</p>
            </div>
            <div class="qr">
                <div class="kode-qr" data-isi="{{ route('boks.show', $b) }}"></div>
                <div style="margin-top:1mm;">Pindai untuk<br>melihat isi boks</div>
            </div>
        </div>
    @empty
        <p>Tidak ada boks untuk dicetak.</p>
    @endforelse
</div>
@endsection

@push('skrip')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    document.querySelectorAll('.kode-qr').forEach(function (el) {
        new QRCode(el, {
            text: el.dataset.isi,
            width: 100,
            height: 100,
            correctLevel: QRCode.CorrectLevel.M
        });
    });
</script>
@endpush