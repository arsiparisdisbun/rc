<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - siarsip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
<div class="container" style="max-width: 420px;">

    <div class="text-center mb-4">
        <h3 class="fw-bold">siarsip</h3>
        <p class="text-muted mb-0">Sistem Informasi Kearsipan<br>Dinas Perkebunan Provinsi Jawa Timur</p>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">

            @if($errors->any())
                <div class="alert alert-danger py-2">
                    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                </div>
            @endif

            <form action="{{ route('login.masuk') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Pengguna</label>
                    <input type="text" name="username" class="form-control"
                           value="{{ old('username') }}" autofocus required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Kata Sandi</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="form-check mb-4">
                    <input type="checkbox" name="ingat" value="1" class="form-check-input" id="ingat">
                    <label class="form-check-label" for="ingat">Ingat saya di perangkat ini</label>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2">Masuk</button>
            </form>
        </div>
    </div>

    <p class="text-center text-muted small mt-3">
        Lupa kata sandi? Hubungi pengelola aplikasi untuk mengatur ulang.
    </p>
</div>
</body>
</html>