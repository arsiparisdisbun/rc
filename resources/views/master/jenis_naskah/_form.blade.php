<div class="mb-3">
    <label class="form-label fw-bold">Nama Jenis Naskah <span class="text-danger">*</span></label>
    <input type="text" name="nama" class="form-control"
           value="{{ old('nama', $jenis->nama ?? '') }}" required autofocus>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Kelompok</label>
        <input type="text" name="kelompok" class="form-control" list="daftar-kelompok"
               value="{{ old('kelompok', $jenis->kelompok ?? '') }}" placeholder="Contoh: Korespondensi">
        <datalist id="daftar-kelompok">
            @foreach($daftarKelompok as $k)
                <option value="{{ $k }}">
            @endforeach
        </datalist>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Sub Kelompok</label>
        <input type="text" name="sub_kelompok" class="form-control" list="daftar-sub"
               value="{{ old('sub_kelompok', $jenis->sub_kelompok ?? '') }}" placeholder="Contoh: Internal">
        <datalist id="daftar-sub">
            @foreach($daftarSub as $s)
                <option value="{{ $s }}">
            @endforeach
        </datalist>
    </div>
</div>

<div class="mb-3">
    <label class="form-label fw-bold">Urutan Tampil</label>
    <input type="number" name="urutan" class="form-control" min="1"
           value="{{ old('urutan', $jenis->urutan ?? '') }}" placeholder="Kosongkan untuk ditaruh di urutan terakhir">
    <small class="text-muted">Menentukan posisi dalam daftar pilihan di form surat keluar.</small>
</div>

<div class="form-check mb-4">
    <input type="checkbox" name="aktif" value="1" class="form-check-input" id="aktif"
           @checked(old('aktif', $jenis->aktif ?? true))>
    <label class="form-check-label" for="aktif">Aktif</label>
    <br><small class="text-muted">Jenis nonaktif tidak muncul sebagai pilihan saat mengagendakan surat baru.</small>
</div>