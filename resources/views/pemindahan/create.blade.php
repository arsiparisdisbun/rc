@extends('layouts.app')
@section('judul', 'Ajukan Penyerahan - siarsip')

@section('isi')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Ajukan Penyerahan Arsip Inaktif</h4>
    <a href="{{ route('pemindahan.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>

<div class="alert alert-info small">
    Yang ditampilkan hanya berkas yang <strong>sudah terverifikasi</strong>,
    <strong>sudah jatuh tempo inaktif</strong>, dan belum pernah diserahkan.
    Boks inaktif tujuan ditentukan oleh Unit Kearsipan saat penyerahan diterima.
</div>

<form action="{{ route('pemindahan.store') }}" method="POST">
    @csrf
    <input type="hidden" name="unit_pengolah" value="{{ $unit }}">

    <div class="card shadow-sm mb-3">
        <div class="card-header fw-bold">Berkas yang Diserahkan</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 45px;" class="text-center">
                            <input type="checkbox" class="form-check-input" id="pilih-semua">
                        </th>
                        <th style="width: 85px;">No Berkas</th>
                        <th style="width: 140px;">Klasifikasi</th>
                        <th>Uraian</th>
                        <th style="width: 85px;">Kurun<br>Waktu</th>
                        <th style="width: 110px;">Boks Aktif</th>
                        <th style="width: 110px;" class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($kandidat as $b)
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" name="berkas_id[]" value="{{ $b->id }}" class="form-check-input pilihan">
                        </td>
                        <td class="fw-bold">{{ $b->label }}</td>
                        <td><small>{{ $b->kode_klasifikasi ?: '-' }}</small></td>
                        <td>{{ Str::limit($b->uraian, 70) }}</td>
                        <td class="text-center">{{ $b->kurun_waktu }}</td>
                        <td><small>{{ $b->boks?->label ?: '-' }}</small></td>
                        <td class="text-center">
                            <span class="badge bg-warning">{{ $b->status_penyimpanan }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">
                        Belum ada berkas yang siap diserahkan.
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-header fw-bold">Berita Acara</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label fw-bold">Nomor Berita Acara</label>
                    <input type="text" name="nomor_ba" class="form-control" value="{{ old('nomor_ba') }}"
                           placeholder="Contoh: 000.5.6.1/123/{{ $unit }}/{{ now()->year }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Tanggal Berita Acara</label>
                    <input type="date" name="tanggal_ba" class="form-control"
                           value="{{ old('tanggal_ba', date('Y-m-d')) }}">
                </div>
            </div>

            <h6 class="fw-bold mt-2">Penanda Tangan Unit Pengolah (PIHAK I)</h6>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Nama <span class="text-danger">*</span></label>
                    <input type="text" name="pihak1_nama" class="form-control" value="{{ old('pihak1_nama') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">NIP</label>
                    <input type="text" name="pihak1_nip" class="form-control" value="{{ old('pihak1_nip') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Pangkat</label>
                    <input type="text" name="pihak1_pangkat" class="form-control" value="{{ old('pihak1_pangkat') }}"
                           placeholder="Contoh: Pembina Tk. I (IV/b)">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Jabatan <span class="text-danger">*</span></label>
                    <input type="text" name="pihak1_jabatan" class="form-control" value="{{ old('pihak1_jabatan') }}" required>
                </div>
            </div>

            <div class="mb-0">
                <label class="form-label fw-bold">Catatan</label>
                <textarea name="catatan" class="form-control" rows="2">{{ old('catatan') }}</textarea>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center">
        <span class="text-muted small">Dipilih: <strong id="jumlah-pilihan">0</strong> berkas</span>
        <button class="btn btn-primary" id="tombol-ajukan" disabled>Ajukan Penyerahan</button>
    </div>
</form>
@endsection

@push('skrip')
<script>
    const semua = document.getElementById('pilih-semua');
    const pilihan = document.querySelectorAll('.pilihan');
    const tombol = document.getElementById('tombol-ajukan');
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