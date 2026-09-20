<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - siarsip</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('gaya')
    <link href="{{ asset('css/siarsip.css') }}" rel="stylesheet">

    <style>
        /* Palet Warna: Professional Navy & Royal Blue (Elegan & Berwibawa) */
        :root {
            --kertas-kanan: #ffffff;
            --kertas-kiri: #f8fafc; 
            --biru-utama: #2563eb;  /* Biru Royal yang terang dan modern */
            --biru-gelap: #1d4ed8;  /* Biru saat tombol di-hover */
            --aksen-gelap: #0f172a; /* Biru sangat gelap untuk teks utama */
            --bg-input: #f1f5f9;
            --tinta: #0f172a;
            --tinta-lembut: #475569;
            --bayang-halus: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
            --bayang-kartu: 0 25px 50px -12px rgba(37, 99, 235, 0.15);
        }

        /* --- BACKGROUND HALAMAN --- */
        html, body { height: 100%; font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden; }
        body { 
            margin: 0; 
            background: linear-gradient(135deg, #dbeafe 0%, #f0fdf4 50%, #f8fafc 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        /* --- KARTU MELAYANG (FLOATING CARD) --- */
        .login-wrapper {
            display: flex;
            width: 100%;
            max-width: 1150px; 
            background-color: var(--kertas-kanan);
            border-radius: 1.5rem; 
            box-shadow: var(--bayang-kartu);
            overflow: hidden; 
        }

        /* ---------- Animasi Masuk ---------- */
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .anim-jeda-1 { animation: fadeSlideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; animation-delay: 0.1s; opacity: 0; }
        .anim-jeda-2 { animation: fadeSlideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; animation-delay: 0.2s; opacity: 0; }
        .anim-jeda-3 { animation: fadeSlideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; animation-delay: 0.3s; opacity: 0; }
        .anim-jeda-4 { animation: fadeSlideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; animation-delay: 0.4s; opacity: 0; }
        .anim-jeda-5 { animation: fadeSlideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; animation-delay: 0.5s; opacity: 0; }

        /* ---------- Panel Kiri: Identitas ---------- */
        .masuk-kiri {
            --s-kerja:   #cbd5e1;
            --s-aktif:   #3b82f6;
            --s-inaktif: #f59e0b;
            --s-akhir:   #ef4444;

            flex: 1 1 52%; 
            background-color: var(--kertas-kiri);
            color: var(--tinta);
            padding: 4rem 4.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            border-right: 1px solid #e2e8f0;
        }

        .masuk-kiri-isi { width: 100%; max-width: 520px; position: relative; z-index: 1; }

        .masuk-lambang {
            width: 92px; height: 92px;
            border-radius: 24px;
            background: #ffffff;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.8rem;
            box-shadow: var(--bayang-halus);
            border: 1px solid #e0f2fe;
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .masuk-lambang:hover { transform: scale(1.05) rotate(-3deg); }
        .masuk-lambang img { width: 66px; height: auto; }

        .masuk-wordmark { 
            font-size: 3.4rem; 
            font-weight: 800; 
            letter-spacing: -0.03em; 
            margin: 0 0 0.3rem; 
            color: var(--tinta); 
            line-height: 1;
        }
        
        .masuk-tagline { 
            font-size: 1.35rem; 
            font-weight: 700;
            color: var(--tinta);
            line-height: 1.4;
            margin-bottom: 2.5rem; 
        }

        /* Interaksi Siklus Retensi */
        .masuk-siklus { display: flex; position: relative; margin-bottom: 2rem; }
        .masuk-siklus::before {
            content: ""; position: absolute; left: 10%; right: 10%; top: 9px; height: 2px;
            background: #cbd5e1; border-radius: 2px;
        }
        .siklus-tahap {
            flex: 1; display: flex; flex-direction: column; align-items: center;
            text-align: center; position: relative; z-index: 1; cursor: pointer;
            transition: transform 0.3s ease;
        }
        .siklus-titik {
            width: 20px; height: 20px; border-radius: 50%; background: var(--warna);
            box-shadow: 0 0 0 6px var(--kertas-kiri); margin-bottom: 0.75rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .siklus-tahap:hover { transform: translateY(-4px); }
        .siklus-tahap:hover .siklus-titik { 
            transform: scale(1.4); 
            box-shadow: 0 0 0 3px var(--kertas-kiri), 0 0 15px var(--warna); 
        }
        .siklus-tahap:hover .siklus-nama { color: var(--biru-utama); }

        .siklus-nama { font-size: 0.95rem; font-weight: 700; color: var(--tinta); letter-spacing: 0.02em; text-transform: uppercase; transition: color 0.3s; }
        
        .masuk-keterangan { 
            font-size: 1.05rem; 
            line-height: 1.65; 
            color: var(--tinta-lembut); 
            max-width: 45ch; 
            margin: 0;
        }

        /* ---------- Panel Kanan: Formulir ---------- */
        .masuk-kanan { 
            flex: 1 1 48%; 
            display: flex; 
            flex-direction: column; 
            position: relative; 
            background-color: var(--kertas-kanan);
        }
        
        .masuk-kanan-tengah { 
            flex: 1; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 3rem; 
        }
        
        .masuk-form-bungkus { width: 100%; max-width: 400px; }
        .masuk-heading { font-size: 1.85rem; font-weight: 800; margin-bottom: 0.4rem; color: var(--tinta); letter-spacing: -0.02em; }
        .masuk-subheading { font-size: 0.95rem; color: var(--tinta-lembut); margin-bottom: 2rem; }

        .form-control {
            padding: 0.85rem 1.2rem; border-radius: 0.6rem; border: 2px solid transparent;
            font-size: 1rem; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: var(--bg-input);
            color: var(--tinta);
        }
        .form-control:focus {
            background-color: #ffffff;
            border-color: var(--biru-utama);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
            transform: translateY(-2px);
        }
        .form-label { color: var(--tinta); font-size: 0.9rem; font-weight: 700; transition: color 0.3s; }
        .form-control:focus + .form-label, .form-control:focus-within ~ .form-label { color: var(--biru-utama); }

        .masuk-sandi-bungkus { position: relative; }
        .masuk-sandi-bungkus .form-control { padding-right: 3rem; }
        .masuk-mata {
            position: absolute; right: .5rem; top: 50%; transform: translateY(-50%);
            background: none; border: none; padding: .6rem; display: flex;
            color: var(--tinta-lembut); border-radius: 0.4rem; transition: all 0.2s;
        }
        .masuk-mata:hover { color: var(--biru-utama); background: rgba(37, 99, 235, 0.1); transform: translateY(-50%) scale(1.1); }
        .masuk-mata:active { transform: translateY(-50%) scale(0.95); }

        /* Custom Checkbox */
        .form-check-input:checked { background-color: var(--biru-utama); border-color: var(--biru-utama); }
        .lupa-sandi { color: var(--biru-utama); text-decoration: none; font-weight: 600; font-size: 0.85rem; }
        .lupa-sandi:hover { text-decoration: underline; }

        /* Tombol Masuk Biru Korporat Senada */
        .btn-masuk {
            background: linear-gradient(135deg, var(--biru-utama) 0%, #1d4ed8 100%); 
            border: none; border-radius: 0.6rem;
            font-weight: 600; font-size: 1.05rem; letter-spacing: 0.02em; transition: all 0.3s ease;
            position: relative; overflow: hidden;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
            color: white;
        }
        .btn-masuk:hover {
            background: linear-gradient(135deg, var(--biru-gelap) 0%, #1e40af 100%); 
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.35);
            color: white;
        }
        .btn-masuk:active { transform: translateY(0); }

        .masuk-footer { padding: 1.5rem; font-size: 0.8rem; color: #94a3b8; text-align: center; }

        @media (max-width: 991px) {
            .login-wrapper { flex-direction: column; max-width: 600px; }
            .masuk-kiri { padding: 3rem 2rem; border-right: none; border-bottom: 1px solid #e2e8f0; }
            .masuk-kiri-isi { max-width: 100%; text-align: center; display: flex; flex-direction: column; align-items: center; }
            .masuk-keterangan { text-align: center; }
            .masuk-kanan-tengah { padding: 3rem 1.5rem; }
        }
    </style>
</head>
<body>

<div class="login-wrapper">

    <!-- Panel Kiri (Identitas) -->
    <section class="masuk-kiri">
        <div class="masuk-kiri-isi anim-jeda-1">
            <div class="masuk-lambang">
                <img src="{{ asset('img/logo-jatim.png') }}" alt="Lambang Provinsi Jawa Timur">
            </div>

            <h1 class="masuk-wordmark">
            <span style="color: var(--biru-utama);">Si</span><span style="color: var(--aksen-gelap);">arsip</span>
            </h1>
            <p class="masuk-tagline">
                Dinas Perkebunan Provinsi Jawa Timur
            </p>

            <div class="masuk-siklus" aria-hidden="true" title="Siklus Hidup Arsip">
                <div class="siklus-tahap">
                    <span class="siklus-titik" style="--warna: var(--s-kerja)"></span>
                    <span class="siklus-nama">Kerja</span>
                </div>
                <div class="siklus-tahap">
                    <span class="siklus-titik" style="--warna: var(--s-aktif)"></span>
                    <span class="siklus-nama">Aktif</span>
                </div>
                <div class="siklus-tahap">
                    <span class="siklus-titik" style="--warna: var(--s-inaktif)"></span>
                    <span class="siklus-nama">Inaktif</span>
                </div>
                <div class="siklus-tahap">
                    <span class="siklus-titik" style="--warna: var(--s-akhir)"></span>
                    <span class="siklus-nama">Musnah</span>
                </div>
            </div>

            <p class="masuk-keterangan">
                Mengawal perjalanan dokumen secara digital: dari penciptaan, pemeliharaan, hingga penyusutan akhir yang tertib dan akuntabel.
            </p>
        </div>
    </section>

    <!-- Panel Kanan (Form Login) -->
    <section class="masuk-kanan">
        
        <div class="masuk-kanan-tengah">
            <div class="masuk-form-bungkus">
                <h2 class="masuk-heading anim-jeda-1">Selamat Datang</h2>
                <p class="masuk-subheading anim-jeda-1">Silakan login untuk mengakses aplikasi siarsip</p>

                @if($errors->any())
                    <div class="alert alert-danger border-0 rounded-3 shadow-sm py-2 px-3 small mb-4 anim-jeda-2">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('login.masuk') }}" method="POST" id="formLogin">
                    @csrf

                    <div class="mb-4 anim-jeda-2">
                        <label class="form-label" for="username">Nama Pengguna</label>
                        <input type="text" name="username" id="username" class="form-control"
                               placeholder="Masukkan nama pengguna"
                               value="{{ old('username') }}" autofocus required>
                    </div>

                    <div class="mb-3 anim-jeda-3">
                        <label class="form-label" for="password">Password</label>
                        <div class="masuk-sandi-bungkus">
                            <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                            <button type="button" class="masuk-mata" id="togglePassword"
                                    aria-label="Tampilkan kata sandi" aria-pressed="false">
                                <svg id="ikonMataBuka" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg id="ikonMataTutup" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                     style="display:none">
                                    <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a21.6 21.6 0 0 1 5.06-5.94M9.9 4.24A10.4 10.4 0 0 1 12 4c7 0 11 7 11 7a21.6 21.6 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                    <line x1="1" y1="1" x2="23" y2="23"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4 mt-2 anim-jeda-4">
                        <div class="form-check">
                            <input type="checkbox" name="ingat" value="1" class="form-check-input" id="ingat">
                            <label class="form-check-label text-muted" style="font-size: 0.85rem;" for="ingat">Remember this Device</label>
                        </div>
                        <a href="#" class="lupa-sandi">Forgot Password?</a>
                    </div>

                    <button type="submit" class="btn btn-masuk w-100 py-2 anim-jeda-5" id="btnSubmit">
                        <span id="btnText">Login</span>
                        <span id="btnSpinner" class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
                    </button>
                </form>

            </div>
        </div>

        <div class="masuk-footer anim-jeda-5">
            &copy; 2025-{{ date('Y') }} siarsip - Dinas Perkebunan Provinsi Jawa Timur
        </div>

    </section>

</div>

<script>
    const tombolMata = document.getElementById('togglePassword');
    const inputSandi = document.getElementById('password');
    const mataBuka   = document.getElementById('ikonMataBuka');
    const mataTutup  = document.getElementById('ikonMataTutup');

    tombolMata.addEventListener('click', function () {
        const tampil = inputSandi.type === 'password';
        inputSandi.type = tampil ? 'text' : 'password';
        mataBuka.style.display  = tampil ? 'none' : '';
        mataTutup.style.display = tampil ? '' : 'none';
        tombolMata.setAttribute('aria-label', tampil ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        tombolMata.setAttribute('aria-pressed', tampil ? 'true' : 'false');
    });

    const formLogin = document.getElementById('formLogin');
    const btnSubmit = document.getElementById('btnSubmit');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');

    if(formLogin) {
        formLogin.addEventListener('submit', function() {
            btnSubmit.classList.add('opacity-75');
            btnSubmit.style.pointerEvents = 'none';
            btnText.textContent = 'Memproses...';
            btnSpinner.classList.remove('d-none');
        });
    }
</script>

</body>
</html>