@extends('layouts.app')
@section('judul', 'Pengodean Arsip - siarsip')

@push('gaya')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
<style>
    .tabel-kode th, .tabel-kode td { border: 1px solid #dee2e6; vertical-align: middle; }
    .tabel-kode thead th { text-align: center; }
    .baris-selesai { background-color: #d1e7dd !important; }
</style>
@endpush

@section('isi')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Pengodean Klasifikasi Arsip</h4>
        <a href="{{ route('surat-masuk.index') }}" class="btn btn-outline-secondary btn-sm">Kembali ke Buku Agenda</a>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between mb-2">
                <span><strong>{{ number_format($berkode) }}</strong> dari {{ number_format($total) }} arsip sudah diklasifikasi</span>
                <span class="text-muted">Sisa {{ number_format($belum) }} arsip</span>
            </div>
            <div class="progress" style="height: 20px;">
                <div class="progress-bar bg-success" style="width: {{ $persen }}%;">{{ $persen }}%</div>
            </div>
        </div>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-9">
            <input type="text" name="q" class="form-control" value="{{ request('q') }}"
                   placeholder="Cari isi ringkas, pengirim, atau nomor surat — berguna untuk mengodekan surat sejenis sekaligus">
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-secondary flex-grow-1">Cari</button>
            <a href="{{ route('surat-masuk.pengodean') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table tabel-kode mb-0" style="font-size: 0.9rem;">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 60px;">No<br>Urut</th>
                        <th style="width: 100px;">Tgl Surat</th>
                        <th style="width: 170px;">Nomor Surat</th>
                        <th>Isi Ringkas</th>
                        <th style="width: 170px;">Dari</th>
                        <th style="width: 330px;">Kode Klasifikasi</th>
                        <th style="width: 150px;">Hasil</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($arsip as $a)
                    <tr id="baris-{{ $a->id }}">
                        <td class="text-center fw-bold">{{ $a->no_urut }}<br><small class="text-muted fw-normal">{{ $a->tahun }}</small></td>
                        <td class="text-center">{{ $a->tanggal_surat?->format('d/m/Y') }}</td>
                        <td>{{ $a->nomor_surat }}</td>
                        <td>{{ $a->isi_ringkas }}</td>
                        <td>{{ $a->dari }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <select class="form-select form-select-sm pilih-kode" data-id="{{ $a->id }}"></select>
                                @if($a->dokumen_path)
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-saran-baris"
                                            data-arsip-id="{{ $a->id }}"
                                            data-url="{{ route('arsip.saran-klasifikasi', $a) }}"
                                            title="Minta saran dari isi PDF">💡</button>
                                @endif
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="hasil text-muted small">—</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-5 text-success fw-bold">
                        Semua arsip sudah diklasifikasi.
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $arsip->links() }}</div>
@endsection

@push('skrip')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function () {
    $('.pilih-kode').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Ketik kode atau uraian...',
        minimumInputLength: 2,
        ajax: {
            url: '{{ route('klasifikasi.cari') }}',
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({ results: data }),
            cache: true
        }
    });

    function simpanKode(id, kode) {
        const $baris = $('#baris-' + id);
        const $hasil = $baris.find('.hasil');

        $hasil.removeClass('text-danger text-success').addClass('text-muted').text('Menyimpan...');

        $.ajax({
            url: '{{ url('surat-masuk/pengodean') }}/' + id,
            method: 'POST',
            data: { _method: 'PATCH', _token: '{{ csrf_token() }}', kode_klasifikasi: kode },
            success: function (r) {
                $baris.addClass('baris-selesai');
                $hasil.removeClass('text-muted').addClass('text-success')
                      .html('<strong>' + r.status + '</strong><br><small>Inaktif ' + (r.inaktif || '-') +
                            '<br>' + (r.nasib || '') + ' ' + (r.akhir || '-') + '</small>');
            },
            error: function (x) {
                const pesan = x.responseJSON?.message || 'Gagal menyimpan';
                $hasil.removeClass('text-muted').addClass('text-danger').text(pesan);
            }
        });
    }

    $('.pilih-kode').on('select2:select', function (e) {
        simpanKode($(this).data('id'), e.params.data.id);
    });

    $(document).on('click', '.btn-saran-baris', function () {
        const $tombol = $(this);
        const id = $tombol.data('arsip-id');
        const $select = $(`.pilih-kode[data-id="${id}"]`);

        $tombol.prop('disabled', true).html('...');

        $.get($tombol.data('url'))
            .done(function (r) {
                let kode = null, uraian = null;

                if (r.kode_pasti) {
                    kode = r.kode_pasti.kode; uraian = r.kode_pasti.uraian;
                } else if (r.kandidat && r.kandidat.length) {
                    kode = r.kandidat[0].kode; uraian = r.kandidat[0].uraian;
                } else if (r.pilihan_acuan && r.pilihan_acuan.length) {
                    kode = r.pilihan_acuan[0].kode; uraian = r.pilihan_acuan[0].uraian;
                }

                if (kode) {
                    const opsi = new Option(`${kode} — ${uraian}`, kode, true, true);
                    $select.empty().append(opsi).trigger('change');
                    simpanKode(id, kode);
                } else {
                    alert('Tidak ditemukan saran yang cocok untuk surat ini.');
                }
            })
            .fail(function (x) {
                alert(x.responseJSON?.error || 'Gagal mengambil saran.');
            })
            .always(function () {
                $tombol.prop('disabled', false).html('💡');
            });
    });
});
</script>
@endpush