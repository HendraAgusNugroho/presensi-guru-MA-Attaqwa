<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#2d6a4f">
    <link rel="icon" href="{{ asset('images/logo-sekolah.png') }}" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-sekolah.png') }}">
    <meta name="description" content="Halaman login Sistem Presensi Guru MA Attaqwa — khusus staf dan guru terdaftar.">
    <link rel="canonical" href="{{ url('/login') }}">
    <title>Login — Sistem Presensi Guru MA Attaqwa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ is_file(public_path('css/app.css')) ? substr(md5_file(public_path('css/app.css')), 0, 12) : '1' }}">
    <style>
        body.login-page {
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, hsl(145,60%,10%) 0%, hsl(145,60%,18%) 50%, hsl(145,55%,26%) 100%);
            position: relative;
            overflow-x: hidden;
            padding: 16px;
        }
        body.login-page::before,
        body.login-page::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,.04);
            pointer-events: none;
        }
        body.login-page::before { width: 400px; height: 400px; top: -120px; right: -100px; }
        body.login-page::after { width: 300px; height: 300px; bottom: -80px; left: -80px; background: rgba(255,255,255,.03); }

        .login-wrap { width: 100%; max-width: 440px; position: relative; z-index: 1; }
        .login-card { background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 24px 64px rgba(0,0,0,.3); }

        .login-header {
            background: linear-gradient(135deg, hsl(145,60%,18%), hsl(145,60%,28%));
            padding: 28px 32px 40px;
            text-align: center;
            position: relative;
        }
        .login-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 28px;
            background: #fff;
            border-radius: 28px 28px 0 0;
            z-index: 1;
        }
        .login-header-inner {
            position: relative;
            z-index: 2;
            padding-bottom: 8px;
        }
        .school-logo-wrap {
            width: 84px; height: 84px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,.35);
            overflow: hidden;
            background: #fff;
        }
        .school-logo-wrap img { width: 100%; height: 100%; object-fit: contain; }
        .school-info {
            padding: 0 12px 12px;
            max-width: 320px;
            margin: 0 auto;
        }
        .school-name {
            color: rgba(255,255,255,.98);
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            line-height: 1.45;
            word-wrap: break-word;
        }
        .school-sub {
            color: rgba(255,255,255,.82);
            font-size: .72rem;
            margin-top: 6px;
            line-height: 1.55;
            word-wrap: break-word;
        }

        .login-body { padding: 32px 32px 32px; }
        .login-title { font-size: 1.2rem; font-weight: 800; color: #1a2e1a; margin-bottom: 4px; display: flex; align-items: center; justify-content: center; flex-wrap: wrap; gap: 8px; text-align: center; }
        .login-sub { font-size: .88rem; color: #5a7a5a; margin-bottom: 24px; }

        .login-body label { display: block; font-size: .88rem; font-weight: 700; margin-bottom: 6px; color: #1a2e1a; }
        .login-body .form-group { margin-bottom: 16px; }
        .login-body .input-icon { position: relative; }
        .login-body .input-icon .field-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0b8a0;
            font-size: .9rem;
            pointer-events: none;
            z-index: 2;
        }
        .login-body .input-icon .form-control {
            padding-left: 42px;
            background: #f8fdf8;
        }
        .login-body .password-field .form-control { padding-left: 42px; background: #f8fdf8; }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, hsl(145,60%,28%), hsl(145,60%,20%));
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all .2s;
            margin-top: 8px;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 48px;
        }
        .btn-login:hover { opacity: .9; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(0,80,0,.3); }

        .alert-err {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 16px;
            font-size: .88rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .remember { display: flex; align-items: center; gap: 8px; font-size: .88rem; color: #5a7a5a; cursor: pointer; margin-bottom: 4px; }
        .remember input { width: 16px; height: 16px; cursor: pointer; accent-color: hsl(145,60%,28%); }
        .footer-info { text-align: center; margin-top: 18px; font-size: .78rem; color: rgba(255,255,255,.5); }

        @media (max-width: 768px) {
            body.login-page { padding: 12px; align-items: flex-start; padding-top: max(16px, env(safe-area-inset-top)); }
            .login-wrap { max-width: 100%; margin-top: 0; }
            .login-header { padding: 22px 18px 44px; }
            .login-header::after { height: 32px; border-radius: 32px 32px 0 0; }
            .school-logo-wrap { width: 76px; height: 76px; margin-bottom: 14px; }
            .school-info { padding: 0 8px 16px; max-width: 100%; }
            .school-name { font-size: .74rem; line-height: 1.5; }
            .school-sub { font-size: .7rem; line-height: 1.6; margin-top: 8px; }
            .login-body { padding: 24px 20px 28px; }
            .login-title { font-size: 1.05rem; }
            .login-sub { font-size: .84rem; margin-bottom: 20px; }
            .footer-info { font-size: .72rem; margin-top: 14px; padding-bottom: env(safe-area-inset-bottom); }
        }

        @media (max-width: 480px) {
            .login-header { padding: 20px 16px 48px; }
            .school-logo-wrap { width: 72px; height: 72px; }
            .login-body { padding: 22px 16px 24px; }
        }

        /* Tablet / iPad */
        @media (min-width: 481px) and (max-width: 1024px) {
            body.login-page { padding: 24px; }
            .login-wrap { max-width: 420px; }
            .login-header { padding: 26px 28px 42px; }
            .school-info { padding-bottom: 14px; }
            .login-body { padding: 28px 28px 32px; }
        }
    </style>
</head>
<body class="login-page">
<div class="login-wrap">
    <div class="login-card">
        <div class="login-header">
            <div class="login-header-inner">
                <div class="school-logo-wrap">
                    <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo MA Attaqwa"
                         width="84" height="84" fetchpriority="high">
                </div>
                <div class="school-info">
                    <div class="school-name">YPIA Daarul Mu'min</div>
                    <div class="school-sub">Madrasah Aliyah Attaqwa Benda Tangerang</div>
                </div>
            </div>
        </div>
        <div class="login-body">
            <h1 class="login-title">
                <i class="fas fa-fingerprint" style="color:hsl(145,60%,28%)" aria-hidden="true"></i>
                Sistem Presensi Guru
            </h1>
            <p class="login-sub">Masuk menggunakan ID dan password Anda</p>

            @if($errors->any())
                <div class="alert-err" role="alert">
                    <i class="fas fa-times-circle" aria-hidden="true"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label for="id_pengguna">ID</label>
                    <div class="input-icon">
                        <i class="fas fa-id-card field-icon" aria-hidden="true"></i>
                        <input type="text" name="id_pengguna" id="id_pengguna"
                            class="form-control" value="{{ old('id_pengguna') }}"
                            placeholder="Masukkan ID Anda" autocomplete="username" required autofocus>
                    </div>
                </div>
                <div class="form-group">
                    <label for="passInput">Password</label>
                    <div class="password-field input-icon">
                        <i class="fas fa-lock field-icon" aria-hidden="true"></i>
                        <input type="password" name="password" id="passInput" class="form-control"
                            placeholder="Masukkan password" autocomplete="current-password" required>
                        <button type="button" class="password-toggle" data-toggle-password="passInput"
                            aria-label="Tampilkan password" aria-pressed="false">
                            <svg class="icon-eye" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg class="icon-eye-off" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <label class="remember" for="remember_me">
                    <input type="checkbox" name="remember" id="remember_me" value="1"> Ingat saya
                </label>
                <button type="submit" class="btn-login">
                    <i class="fas fa-right-to-bracket" aria-hidden="true"></i> Masuk ke Sistem
                </button>
            </form>
        </div>
    </div>
    <p class="footer-info">
        &copy; {{ date('Y') }} MA Attaqwa — YPIA Daarul Mu'min | Sistem Presensi Guru
    </p>
</div>
<script src="{{ asset('js/app.js') }}?v={{ is_file(public_path('js/app.js')) ? substr(md5_file(public_path('js/app.js')), 0, 12) : '1' }}"></script>
</body>
</html>
