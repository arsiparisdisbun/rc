<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengodean Arsip - siarsip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <style>
        .tabel-kode th, .tabel-kode td { border: 1px solid #dee2e6; vertical-align: middle; }
        .tabel-kode thead th { text-align: center; }
        .baris-selesai { background-color: #d1e7dd !important; }
        .pagination svg { width: 1rem; height: 1rem; }
    </style>
</head>
<body class="bg-light">
<div class="container-fluid px-4 my-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Pengodean Klasifikasi Arsip</h4>
        <a href="{{ route('surat-masuk.index') }}" class="btn btn-outline-secondary">Kembali ke Buku Agenda</a>
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
                            <select class="form-select form-select-sm pilih-kode" data-id="{{ $a->id }}"></select>
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
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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

    $('.pilih-kode').on('select2:select', function (e) {
        const id   = $(this).data('id');
        const kode = e.params.data.id;
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
    });
});
</script>
</body>
</html>