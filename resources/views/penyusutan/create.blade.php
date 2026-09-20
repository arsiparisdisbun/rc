@extends('layouts.app')
@section('judul', 'Catat Penyusutan - siarsip')

@section('isi')
@php $musnah = $jenis === 'musnah'; @endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Catat {{ $musnah ? 'Pemusnahan Arsip' : 'Penyerahan Arsip Statis' }}</h4>
    <a href="{{ route('penyusutan.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>

<div class="alert alert-{{ $musnah ? 'danger' : 'info' }} small">
    @if($musnah)
        <strong>Perhatian.</strong> Yang ditampilkan hanya berkas yang nasib akhirnya <strong>Musnah</strong>
        menurut JRA dan masa retensinya sudah habis. Pemusnahan arsip bersifat permanen —
        pastikan sudah ada persetujuan sesuai ketentuan sebelum dicatat di sini.
    @else
        Yang ditampilkan hanya berkas yang nasib akhirnya <strong>Permanen</strong> menurut JRA
        dan masa retensinya sudah habis. Arsip ini diserahkan ke lembaga kearsipan daerah
        untuk disimpan selamanya.
    @endif
</div>

<form action="{{ route('penyusutan.store') }}" method="POST">
    @csrf
    <input type="hidden" name="jenis" value="{{ $jenis }}">

    <div class="card shadow-sm mb-3">
        <div class="card-header fw-bold">Berkas yang Disusutkan</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 45px;" class="text-center">
                            <input type="checkbox" class="form-check-input" id="pilih-semua">
                        </th>
                        <th style="width: 85px;">No Berkas</th>
                        <th style="width: 65px;">Unit</th>
                        <th style="width: 130px;">Klasifikasi</th>
                        <th>Uraian</th>
                        <th style="width: 80px;">Kurun<br>Waktu</th>
                        <th style="width: 90px;">Boks</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($kandidat as $b)
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" name="berkas_id[]" value="{{ $b->id }}" class="form-check-input pilihan">
                        </td>
                        <td class="fw-bold">{{ $b->label }}</td>
                        <td class="text-center">{{ $b->unit_pengolah }}</td>
                        <td><small>{{ $b->kode_klasifikasi }}</small></td>
                        <td>{{ Str::limit($b->uraian, 70) }}</td>
                        <td class="text-center">{{ $b->kurun_waktu }}</td>
                        <td><small>{{ $b->boks?->label ?: '-' }}</small></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">
                        Belum ada berkas yang habis masa retensinya untuk jalur ini.
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
                    <input type="text" name="nomor_ba" class="form-control" value="{{ old('nomor_ba') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_ba" class="form-control"
                           value="{{ old('tanggal_ba', date('Y-m-d')) }}" required>
                </div>
            </div>

            @if($musnah)
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tempat Pemusnahan <span class="text-danger">*</span></label>
                        <input type="text" name="tempat" class="form-control" value="{{ old('tempat') }}"
                               placeholder="Contoh: Halaman Kantor Dinas Perkebunan" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Cara Pemusnahan <span class="text-danger">*</span></label>
                        <select name="cara" class="form-select" required>
                            @foreach(\App\Models\Penyusutan::CARA as $c)
                                <option value="{{ $c }}" @selected(old('cara') === $c)>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif

            <h6 class="fw-bold mt-2">
                {{ $musnah ? 'Pelaksana Pemusnahan' : 'PIHAK I' }} — Unit Kearsipan
            </h6>
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
                    <input type="text" name="pihak1_pangkat" class="form-control" value="{{ old('pihak1_pangkat') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Jabatan <span class="text-danger">*</span></label>
                    <input type="text" name="pihak1_jabatan" class="form-control" value="{{ old('pihak1_jabatan') }}" required>
                </div>
            </div>

            @if($musnah)
                <h6 class="fw-bold mt-2">Saksi</h6>
                <div class="row">
                    <div class="col-md-5 mb-3">
                        <label class="form-label fw-bold">Saksi 1 — Nama <span class="text-danger">*</span></label>
                        <input type="text" name="saksi1_nama" class="form-control" value="{{ old('saksi1_nama') }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">NIP</label>
                        <input type="text" name="saksi1_nip" class="form-control" value="{{ old('saksi1_nip') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Jabatan <span class="text-danger">*</span></label>
                        <input type="text" name="saksi1_jabatan" class="form-control" value="{{ old('saksi1_jabatan') }}" required>
                    </div>

                    <div class="col-md-5 mb-3">
                        <label class="form-label fw-bold">Saksi 2 — Nama <span class="text-danger">*</span></label>
                        <input type="text" name="saksi2_nama" class="form-control" value="{{ old('saksi2_nama') }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">NIP</label>
                        <input type="text" name="saksi2_nip" class="form-control" value="{{ old('saksi2_nip') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Jabatan <span class="text-danger">*</span></label>
                        <input type="text" name="saksi2_jabatan" class="form-control" value="{{ old('saksi2_jabatan') }}" required>
                    </div>
                </div>
            @else
                <h6 class="fw-bold mt-2">PIHAK II — Penerima</h6>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Instansi Penerima <span class="text-danger">*</span></label>
                        <input type="text" name="pihak2_instansi" class="form-control"
                               value="{{ old('pihak2_instansi', 'Dinas Perpustakaan dan Kearsipan Provinsi Jawa Timur') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="pihak2_nama" class="form-control" value="{{ old('pihak2_nama') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">NIP</label>
                        <input type="text" name="pihak2_nip" class="form-control" value="{{ old('pihak2_nip') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Pangkat</label>
                        <input type="text" name="pihak2_pangkat" class="form-control" value="{{ old('pihak2_pangkat') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Jabatan <span class="text-danger">*</span></label>
                        <input type="text" name="pihak2_jabatan" class="form-control" value="{{ old('pihak2_jabatan') }}" required>
                    </div>
                </div>
            @endif

            <div class="mb-0">
                <label class="form-label fw-bold">Catatan</label>
                <textarea name="catatan" class="form-control" rows="2">{{ old('catatan') }}</textarea>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center">
        <span class="text-muted small">Dipilih: <strong id="jumlah-pilihan">0</strong> berkas</span>
        <button class="btn btn-{{ $musnah ? 'danger' : 'primary' }}" id="tombol-simpan" disabled
                onclick="return confirm('Catat {{ $musnah ? 'pemusnahan' : 'penyerahan' }} untuk berkas yang dipilih?')">
            Catat {{ $musnah ? 'Pemusnahan' : 'Penyerahan' }}
        </button>
    </div>
</form>
@endsection

@push('skrip')
<script>
    const semua = document.getElementById('pilih-semua');
    const pilihan = document.querySelectorAll('.pilihan');
    const tombol = document.getElementById('tombol-simpan');
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