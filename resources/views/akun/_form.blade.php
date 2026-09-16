<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Nama Pengguna <span class="text-danger">*</span></label>
        <input type="text" name="username" class="form-control"
               value="{{ old('username', $akun->username ?? '') }}" required>
        <small class="text-muted">Dipakai untuk masuk. Tanpa spasi.</small>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control"
               value="{{ old('name', $akun->name ?? '') }}" required>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Peran <span class="text-danger">*</span></label>
        <select name="peran" id="peran" class="form-select" required
                @disabled(isset($akun) && $akun->id === auth()->id())>
            @foreach(['operator' => 'Operator Unit', 'kearsipan' => 'Unit Kearsipan', 'superadmin' => 'Superadmin'] as $nilai => $label)
                <option value="{{ $nilai }}" @selected(old('peran', $akun->peran ?? 'operator') === $nilai)>{{ $label }}</option>
            @endforeach
        </select>
        @if(isset($akun) && $akun->id === auth()->id())
            <input type="hidden" name="peran" value="{{ $akun->peran }}">
        @endif
    </div>
    <div class="col-md-6 mb-3" id="kotak-unit">
        <label class="form-label fw-bold">Unit Pengolah</label>
        <select name="unit_pengolah" class="form-select">
            <option value="">— Pilih unit —</option>
            @foreach($daftarUnit as $u)
                <option value="{{ $u->kode }}" @selected(old('unit_pengolah', $akun->unit_pengolah ?? '') === $u->kode)>
                    {{ $u->label }}
                </option>
            @endforeach
        </select>
        <small class="text-muted">Wajib untuk peran Operator.</small>
    </div>
</div>

<div class="mb-3">
    <label class="form-label fw-bold">
        Kata Sandi {!! isset($akun) ? '' : '<span class="text-danger">*</span>' !!}
    </label>
    <input type="password" name="password" class="form-control" minlength="8"
           @required(! isset($akun))>
    <small class="text-muted">
        {{ isset($akun) ? 'Kosongkan bila tidak ingin mengganti kata sandi.' : 'Minimal 8 karakter.' }}
    </small>
</div>

<div class="form-check">
    <input type="checkbox" name="aktif" value="1" class="form-check-input" id="aktif"
           @checked(old('aktif', $akun->aktif ?? true))
           @disabled(isset($akun) && $akun->id === auth()->id())>
    <label class="form-check-label" for="aktif">Akun aktif dan dapat masuk</label>
</div>

@push('skrip')
<script>
    // Kotak unit hanya relevan untuk peran operator
    const peran = document.getElementById('peran');
    const kotakUnit = document.getElementById('kotak-unit');

    function aturKotakUnit() {
        kotakUnit.style.display = peran.value === 'operator' ? '' : 'none';
    }

    peran.addEventListener('change', aturKotakUnit);
    aturKotakUnit();
</script>
@endpush