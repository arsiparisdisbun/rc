@php
    $daftar = $objek->jejak()->with('user')->limit($batas ?? 20)->get();
@endphp

<div class="card shadow-sm mt-3">
    <div class="card-header fw-bold">Riwayat Perubahan</div>

    @if($daftar->isEmpty())
        <div class="card-body text-muted small">Belum ada riwayat tercatat.</div>
    @else
        <div class="list-group list-group-flush">
            @foreach($daftar as $j)
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            @php
                                $w = match($j->aksi) {
                                    'tambah' => 'success',
                                    'ubah'   => 'warning',
                                    'hapus'  => 'danger',
                                };
                            @endphp
                            <span class="badge bg-{{ $w }}">{{ $j->label_aksi }}</span>
                            <span class="small ms-1">oleh <strong>{{ $j->nama_user ?: 'Sistem' }}</strong></span>
                            @if($j->unit_pengolah)
                                <span class="text-muted small">({{ $j->unit_pengolah }})</span>
                            @endif
                        </div>
                        <small class="text-muted">{{ $j->created_at?->translatedFormat('d F Y, H:i') }}</small>
                    </div>

                    @if($j->aksi === 'ubah' && $j->sesudah)
                        <div class="mt-2 small">
                            @foreach($j->sesudah as $kolom => $baru)
                                @php $lama = $j->sebelum[$kolom] ?? null; @endphp
                                <div class="mb-1">
                                    <strong>{{ \App\Models\JejakAudit::namaKolom($kolom) }}:</strong>
                                    <span class="text-danger text-decoration-line-through">
                                        {{ Str::limit((string) ($lama ?: '(kosong)'), 60) }}
                                    </span>
                                    <span class="mx-1">&rarr;</span>
                                    <span class="text-success">
                                        {{ Str::limit((string) ($baru ?: '(kosong)'), 60) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>