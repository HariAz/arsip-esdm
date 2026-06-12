<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — Sistem Arsip Dokumen ESDM</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Source+Sans+3:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --navy:  #0d2b4e;
            --gold:  #c8972a;
            --cream: #faf8f3;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Source Sans 3', sans-serif;
            min-height: 100vh;
            display: flex;
            background: var(--cream);
        }

        /* ── PANEL KIRI ── */
        .panel-left {
            width: 45%;
            background: var(--navy);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px 52px;
            position: relative;
            overflow: hidden;
        }

        /* Ornamen garis diagonal */
        .panel-left::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 300px; height: 300px;
            border: 40px solid rgba(200,151,42,.12);
            border-radius: 50%;
        }

        .panel-left::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -60px;
            width: 220px; height: 220px;
            border: 30px solid rgba(200,151,42,.08);
            border-radius: 50%;
        }

        .panel-left-top { position: relative; z-index: 1; }

        .emblem {
            width: 56px; height: 56px;
            background: var(--gold);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 28px;
        }

        .panel-left h1 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: #fff;
            line-height: 1.25;
            margin-bottom: 16px;
        }

        .panel-left h1 span { color: var(--gold); }

        .panel-left p {
            font-size: 14px;
            color: rgba(255,255,255,.55);
            line-height: 1.7;
            max-width: 320px;
        }

        .panel-left-bottom {
            position: relative;
            z-index: 1;
        }

        .ref-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(200,151,42,.15);
            border: 1px solid rgba(200,151,42,.3);
            border-radius: 6px;
            padding: 8px 14px;
            font-size: 12px;
            color: rgba(255,255,255,.65);
        }

        .ref-badge i { color: var(--gold); }

        /* ── PANEL KANAN ── */
        .panel-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 40px;
        }

        .login-box {
            width: 100%;
            max-width: 400px;
        }

        .login-box .form-title {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            color: var(--navy);
            margin-bottom: 6px;
        }

        .login-box .form-sub {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 36px;
        }

        .gold-divider {
            height: 3px;
            width: 48px;
            background: var(--gold);
            border-radius: 2px;
            margin-bottom: 32px;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--navy);
            margin-bottom: 6px;
        }

        .form-control {
            border: 1px solid #d4c9a8;
            border-radius: 6px;
            padding: 11px 14px;
            font-size: 14px;
            background: #fff;
            color: var(--navy);
            transition: border-color .15s, box-shadow .15s;
        }

        .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(200,151,42,.15);
            outline: none;
        }

        .input-group .form-control { border-right: none; border-radius: 6px 0 0 6px; }

        .btn-toggle-pw {
            border: 1px solid #d4c9a8;
            border-left: none;
            border-radius: 0 6px 6px 0;
            background: #fff;
            color: #6b7280;
            padding: 0 14px;
            cursor: pointer;
            transition: color .15s;
        }

        .btn-toggle-pw:hover { color: var(--navy); }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: var(--navy);
            border: none;
            border-radius: 6px;
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Source Sans 3', sans-serif;
            cursor: pointer;
            transition: background .15s, transform .1s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 28px;
        }

        .btn-login:hover { background: #0a2240; }
        .btn-login:active { transform: scale(.99); }

        .gold-accent-line {
            height: 3px;
            background: linear-gradient(90deg, var(--gold), rgba(200,151,42,.2));
            border-radius: 0 0 6px 6px;
        }

        .form-check-label { font-size: 13px; color: #6b7280; }

        .alert-danger {
            background: #fff5f5;
            border: 1px solid #fecaca;
            color: #991b1b;
            border-radius: 6px;
            font-size: 13px;
            padding: 10px 14px;
        }

        .invalid-feedback { font-size: 12px; }

        /* Responsive */
        @media (max-width: 768px) {
            .panel-left { display: none; }
            body { justify-content: center; }
        }
    </style>
</head>
<body>

{{-- ── PANEL KIRI ── --}}
<div class="panel-left">
    <div class="panel-left-top">
        <div class="emblem">🗄️</div>
        <h1>Sistem Arsip<br><span>Dokumen Digital</span></h1>
        <p>
            Platform pengelolaan arsip dokumen terpusat untuk mendukung
            tata kelola pemerintahan yang tertib, efisien, dan akuntabel.
        </p>
    </div>
    <div class="panel-left-bottom">
        <div class="ref-badge">
            <i class="bi bi-shield-check"></i>
            Kepmen ESDM No. 167.K/04/MEM/2020
        </div>
    </div>
</div>

{{-- ── PANEL KANAN ── --}}
<div class="panel-right">
    <div class="login-box">

        <h2 class="form-title">Masuk ke Sistem</h2>
        <p class="form-sub">Gunakan akun arsiparis Anda</p>
        <div class="gold-divider"></div>

        {{-- Flash error --}}
        @if(session('error'))
            <div class="alert alert-danger d-flex gap-2 align-items-center mb-3">
                <i class="bi bi-exclamation-triangle-fill"></i>
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" novalidate>
            @csrf

            {{-- Email --}}
            <div class="mb-3">
                <label for="email" class="form-label">Alamat Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}"
                    placeholder="nama@esdm.go.id"
                    autofocus
                    autocomplete="email"
                >
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Password --}}
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="••••••••"
                        autocomplete="current-password"
                    >
                    <button type="button" class="btn-toggle-pw" onclick="togglePassword()" tabindex="-1">
                        <i class="bi bi-eye" id="pw-icon"></i>
                    </button>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Remember me --}}
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                <label class="form-check-label" for="remember">Ingat saya di perangkat ini</label>
            </div>

            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right"></i>
                Masuk ke Sistem
            </button>
        </form>

        <div class="gold-accent-line mt-4"></div>

        <p class="text-center mt-3" style="font-size:12px; color:#9ca3af;">
            Sistem ini hanya dapat diakses melalui jaringan intranet kantor.
        </p>

    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('pw-icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>

</body>
</html>
