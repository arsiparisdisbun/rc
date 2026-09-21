@extends('layouts.app')
@section('judul', 'Ubah Data Pegawai - siarsip')

@section('isi')
<div class="mx-auto" style="max-width: 700px;">
    <div class="card shadow-sm">
        <div class="card-header bg-warning">
            <h5 class="mb-0">Ubah Data Pegawai — {{ $pegawai->nama }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('pegawai.update', $pegawai) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">NIP</label>
                        <input type="text" name="nip" class="form-control" value="{{ old('nip', $pegawai->nip) }}">
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label fw-bold">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama', $pegawai->nama) }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Jabatan</label>
                    <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan', $pegawai->jabatan) }}">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Unit Pengolah</label>
                        <select name="unit_pengolah" class="form-select">
                            <option value="">— Belum ditentukan —</option>
                            @foreach($daftarUnit as $u)
                                <option value="{{ $u->kode }}" @selected(old('unit_pengolah', $pegawai->unit_pengolah) === $u->kode)>{{ $u->label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Status Kepegawaian <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            @foreach(\App\Models\Pegawai::STATUS as $kode => $label)
                                <option value="{{ $kode }}" @selected(old('status', $pegawai->status) === $kode)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Tanggal Perubahan Status</label>
                    <input type="date" name="tanggal_status" class="form-control"
                           value="{{ old('tanggal_status', $pegawai->tanggal_status?->format('Y-m-d')) }}">
                    <small class="text-muted">Isi bila statusnya bukan Aktif — misalnya tanggal pensiun atau tanggal pindah.</small>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $pegawai->keterangan) }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('pegawai.show', $pegawai) }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-warning flex-grow-1">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection