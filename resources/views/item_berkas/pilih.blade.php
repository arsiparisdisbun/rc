@extends('layouts.app')
@section('judul', "Ambil Arsip - Berkas {$berkas->label}")

@section('isi')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-1">Ambil Arsip dari Buku Agenda</h4>
        <p class="text-muted mb-0 small">Berkas {{ $berkas->label }} — {{ Str::limit($berkas->uraian, 70) }}</p>
    </div>
    <a href="{{ route('berkas.show', $berkas) }}" class="btn btn-outline-secondary btn-sm">Kembali ke Berkas</a>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-5">
        <input type="text" name="q" class="form-control" value="{{ request('q') }}"
               placeholder="Cari isi ringkas, nomor surat, atau kode klasifikasi...">
    </div>
    <div class="col-md-2">
        <select name="jenis" class="form-select">
            <option value="">Semua jenis</option>
            <option value="masuk" @selected(request('jenis') === 'masuk')>Surat Masuk</option>
            <option value="keluar" @selected(request('jenis') === 'keluar')>Surat Keluar</option>
        </select>
    </div>
    <div class="col-md-2">
        <input type="text" name="kode" class="form-control" value="{{ request('kode') }}"
               placeholder="Kode klasifikasi">
    </div>
    <div class="col-md-3 d-flex gap-2">
        <button class="btn btn-secondary flex-grow-1">Cari</button>
        <a href="{{ route('item-berkas.pilih', $berkas) }}" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

<div class="alert alert-info small">
    Hanya arsip yang belum masuk berkas mana pun yang ditampilkan.
    Menyaring berdasarkan kode klasifikasi mempercepat pengumpulan arsip satu kegiatan.
</div>

<form action="{{ route('item-berkas.dari-arsip', $berkas) }}" method="POST">
    @csrf

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 45px;" class="text-center">
                            <input type="checkbox" class="form-check-input" id="pilih-semua">
                        </th>
                        <th style="width: 95px;">Jenis</th>
                        <th style="width: 170px;">Nomor Surat</th>
                        <th>Isi Ringkas</th>
                        <th style="width: 95px;">Tgl Surat</th>
                        <th style="width: 150px;">Klasifikasi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($arsip as $a)
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" name="arsip_id[]" value="{{ $a->id }}" class="form-check-input pilihan">
                        </td>
                        <td>
                            <span class="badge bg-{{ $a->jenis === 'masuk' ? 'primary' : 'success' }}">
                                {{ $a->jenis === 'masuk' ? 'Masuk' : 'Keluar' }}
                            </span>
                        </td>
                        <td>{{ $a->nomor_surat }}</td>
                        <td>{{ Str::limit($a->isi_ringkas, 90) }}</td>
                        <td class="text-center">{{ $a->tanggal_surat?->format('d/m/Y') }}</td>
                        <td>
                            @if($a->kode_klasifikasi)
                                <span class="fw-bold">{{ $a->kode_klasifikasi }}</span><br>
                                <small class="text-muted">{{ Str::limit($a->klasifikasi?->uraian, 28) }}</small>
                            @else
                                <small class="text-muted">-</small>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">
                        Tidak ada arsip yang cocok, atau semuanya sudah masuk berkas lain.
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3">
        <div>{{ $arsip->links() }}</div>
        <button class="btn btn-primary" id="tombol-tambah" disabled>
            Tambahkan ke Berkas (<span id="jumlah-pilihan">0</span>)
        </button>
    </div>
</form>
@endsection

@push('skrip')
<script>
    const semua = document.getElementById('pilih-semua');
    const pilihan = document.querySelectorAll('.pilihan');
    const tombol = document.getElementById('tombol-tambah');
    const jumlah = document.getElementById('jumlah-pilihan');

    function hitung() {
        const n = document.querySelectorAll('.pilihan:checked').length;
        jumlah.textContent = n;
        tombol.disabled = n === 0;
    }

    semua?.addEventListener('change', () => {
        pilihan.forEach(p => p.checked = semua.checked);
        hitung();
    });

    pilihan.forEach(p => p.addEventListener('change', hitung));
    hitung();
</script>
@endpush