<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Arsip') — Kementerian ESDM</title>

    {{-- Google Fonts: Playfair Display + Source Sans 3 --}}
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Source+Sans+3:wght@300;400;500;600&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --esdm-navy:    #0d2b4e;
            --esdm-gold:    #c8972a;
            --esdm-gold-lt: #f0d080;
            --esdm-cream:   #faf8f3;
            --esdm-border:  #d4c9a8;
            --esdm-text:    #1a1a2e;
            --esdm-muted:   #6b7280;
            --sidebar-w:    260px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Source Sans 3', sans-serif;
            background: var(--esdm-cream);
            color: var(--esdm-text);
            min-height: 100vh;
        }

        /* ── SIDEBAR ── */
        #sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--esdm-navy);
            display: flex;
            flex-direction: column;
            z-index: 100;
            border-right: 3px solid var(--esdm-gold);
        }

        .sidebar-brand {
            padding: 24px 20px 20px;
            border-bottom: 1px solid rgba(200,151,42,.3);
        }

        .sidebar-brand .brand-title {
            font-family: 'Playfair Display', serif;
            font-size: 15px;
            color: var(--esdm-gold);
            line-height: 1.3;
            margin: 0;
        }

        .sidebar-brand .brand-sub {
            font-size: 11px;
            color: rgba(255,255,255,.5);
            margin-top: 4px;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .sidebar-brand .brand-logo {
            width: 36px;
            height: 36px;
            background: var(--esdm-gold);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            font-size: 18px;
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px 0;
            overflow-y: auto;
        }

        .nav-section-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: rgba(255,255,255,.35);
            padding: 12px 20px 6px;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: rgba(255,255,255,.72);
            font-size: 14px;
            font-weight: 400;
            border-left: 3px solid transparent;
            transition: all .15s;
            text-decoration: none;
        }

        .sidebar-nav .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,.07);
            border-left-color: rgba(200,151,42,.5);
        }

        .sidebar-nav .nav-link.active {
            color: var(--esdm-gold);
            background: rgba(200,151,42,.12);
            border-left-color: var(--esdm-gold);
            font-weight: 600;
        }

        .sidebar-nav .nav-link i {
            font-size: 16px;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(200,151,42,.2);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--esdm-gold);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            color: var(--esdm-navy);
            flex-shrink: 0;
        }

        .user-info .user-name {
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            line-height: 1.2;
        }

        .user-info .user-role {
            font-size: 11px;
            color: rgba(255,255,255,.45);
        }

        .btn-logout {
            display: flex;
            align-items: center;
            gap: 6px;
            width: 100%;
            margin-top: 10px;
            padding: 8px 12px;
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 6px;
            color: rgba(255,255,255,.65);
            font-size: 13px;
            cursor: pointer;
            transition: all .15s;
            text-decoration: none;
            justify-content: center;
        }

        .btn-logout:hover {
            background: rgba(220,38,38,.2);
            border-color: rgba(220,38,38,.4);
            color: #fca5a5;
        }

        /* ── MAIN CONTENT ── */
        #main {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── TOPBAR ── */
        .topbar {
            background: #fff;
            border-bottom: 1px solid var(--esdm-border);
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar-title {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            color: var(--esdm-navy);
            margin: 0;
        }

        .topbar-meta {
            font-size: 12px;
            color: var(--esdm-muted);
            display: flex;
            align-items: center;
            gap: 16px;
        }

        /* ── PAGE CONTENT ── */
        .page-content {
            flex: 1;
            padding: 28px;
        }

        /* ── CARDS ── */
        .card {
            border: 1px solid var(--esdm-border);
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
            background: #fff;
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid var(--esdm-border);
            padding: 16px 20px;
            font-weight: 600;
            font-size: 14px;
            color: var(--esdm-navy);
        }

        /* ── BADGES KLASIFIKASI ── */
        .badge-biasa          { background: #d1fae5; color: #065f46; }
        .badge-terbatas       { background: #dbeafe; color: #1e3a8a; }
        .badge-rahasia        { background: #fef3c7; color: #92400e; }
        .badge-sangat-rahasia { background: #fee2e2; color: #7f1d1d; }

        /* ── ALERT ── */
        .alert { border-radius: 6px; font-size: 14px; }

        /* ── BUTTONS ── */
        .btn-primary {
            background: var(--esdm-navy);
            border-color: var(--esdm-navy);
        }
        .btn-primary:hover {
            background: #0a2240;
            border-color: #0a2240;
        }

        .btn-gold {
            background: var(--esdm-gold);
            border-color: var(--esdm-gold);
            color: #fff;
        }
        .btn-gold:hover {
            background: #b07d1f;
            border-color: #b07d1f;
            color: #fff;
        }

        /* ── TABLES ── */
        .table th {
            background: #f8f7f4;
            color: var(--esdm-navy);
            font-weight: 600;
            font-size: 13px;
            border-bottom: 2px solid var(--esdm-border);
        }

        .table td { font-size: 13px; vertical-align: middle; }

        /* ── DECORATIVE LINE ── */
        .gold-line {
            height: 3px;
            background: linear-gradient(90deg, var(--esdm-gold), var(--esdm-gold-lt), transparent);
            border-radius: 2px;
            margin-bottom: 24px;
        }

        /* ── BREADCRUMB ── */
        .breadcrumb-nav {
            padding: 8px 28px 0;
        }
        .breadcrumb {
            font-size: 12px;
            margin-bottom: 0;
            background: transparent;
            padding: 0;
        }
        .breadcrumb-item a {
            color: var(--esdm-navy);
            text-decoration: none;
        }
        .breadcrumb-item a:hover { text-decoration: underline; }
        .breadcrumb-item.active { color: var(--esdm-muted); }
        .breadcrumb-item + .breadcrumb-item::before { color: #d4c9a8; }

        /* ── MOBILE RESPONSIVE ── */
        #sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.45);
            z-index: 99;
        }

        #btn-sidebar-toggle {
            display: none;
            background: none;
            border: 1px solid var(--esdm-border);
            border-radius: 6px;
            padding: 5px 9px;
            color: var(--esdm-navy);
            cursor: pointer;
            line-height: 1;
        }

        @media (max-width: 768px) {
            #sidebar {
                transform: translateX(-100%);
                transition: transform .25s ease;
                z-index: 200;
            }
            #sidebar.open { transform: translateX(0); }
            #main { margin-left: 0; }
            #btn-sidebar-toggle { display: inline-flex; align-items: center; }
            .topbar { gap: 12px; }
            .topbar-title { font-size: 16px; }
            .topbar-meta { display: none; }
            .page-content { padding: 16px; }
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- ═══════════════════════ SIDEBAR ═══════════════════════ --}}
<nav id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-logo">🗄️</div>
        <h1 class="brand-title">Sistem Arsip<br>Dokumen Digital</h1>
        <p class="brand-sub">Kementerian ESDM</p>
    </div>

    <div class="sidebar-nav">
        @php
            $pendingDownloads = \App\Models\DownloadRequest::where('status', 'pending')->count();
            $pendingUploads   = \App\Models\Document::where('status', 'pending_approval')->count();
        @endphp

        <div class="nav-section-label">Menu Utama</div>

        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            Dashboard
        </a>

        <a href="{{ route('documents.index') }}"
           class="nav-link {{ request()->routeIs('documents.*') && !request()->routeIs('documents.create') ? 'active' : '' }}">
            <i class="bi bi-folder2-open"></i>
            Arsip Dokumen
            @if($pendingUploads > 0)
                <span class="badge bg-warning text-dark ms-auto" style="font-size:10px;">{{ $pendingUploads }}</span>
            @endif
        </a>

        <a href="{{ route('documents.create') }}"
           class="nav-link {{ request()->routeIs('documents.create') ? 'active' : '' }}">
            <i class="bi bi-cloud-upload"></i>
            Upload Dokumen
        </a>

        <div class="nav-section-label">Persetujuan</div>

        <a href="{{ route('download-requests.index') }}"
           class="nav-link {{ request()->routeIs('download-requests.*') ? 'active' : '' }}">
            <i class="bi bi-download"></i>
            Permintaan Download
            @if($pendingDownloads > 0)
                <span class="badge bg-danger ms-auto" style="font-size:10px;">{{ $pendingDownloads }}</span>
            @endif
        </a>

        <div class="nav-section-label">Sistem</div>

        <a href="{{ route('activity-logs.index') }}"
           class="nav-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i>
            Log Aktivitas
        </a>

        <a href="{{ route('divisions.index') }}"
           class="nav-link {{ request()->routeIs('divisions.*') ? 'active' : '' }}">
            <i class="bi bi-building"></i>
            Manajemen Divisi
        </a>

        <a href="{{ route('users.index') }}"
           class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i>
            Manajemen Pengguna
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">
                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="user-info">
                <div class="user-name">{{ Auth::user()->name ?? '-' }}</div>
                <div class="user-role">Arsiparis</div>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="bi bi-box-arrow-left"></i>
                Keluar dari Sistem
            </button>
        </form>
    </div>
</nav>

{{-- Overlay mobile sidebar --}}
<div id="sidebar-overlay" onclick="closeSidebar()"></div>

{{-- ═══════════════════════ MAIN ═══════════════════════ --}}
<div id="main">
    {{-- Topbar --}}
    <div class="topbar">
        <div class="d-flex align-items-center gap-2">
            <button id="btn-sidebar-toggle" onclick="toggleSidebar()" title="Menu">
                <i class="bi bi-list" style="font-size:18px;"></i>
            </button>
            <h2 class="topbar-title mb-0">@yield('page-title', 'Arsip Dokumen')</h2>
        </div>
        <div class="topbar-meta">
            <span><i class="bi bi-calendar3 me-1"></i>{{ now()->translatedFormat('d F Y') }}</span>
            <span><i class="bi bi-shield-lock me-1"></i>Intranet Lokal</span>
        </div>
    </div>

    {{-- Breadcrumb --}}
    @if (View::hasSection('breadcrumb'))
    <nav aria-label="breadcrumb" class="breadcrumb-nav">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">
                    <i class="bi bi-house-door me-1"></i>Home
                </a>
            </li>
            @yield('breadcrumb')
        </ol>
    </nav>
    @endif

    {{-- Flash messages --}}
    <div class="px-4 pt-3">
        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill"></i>
                {{ session('error') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-circle-fill"></i>
                {{ session('warning') }}
            </div>
        @endif
    </div>

    {{-- Page Content --}}
    <div class="page-content">
        @yield('content')
    </div>

    {{-- Footer --}}
    <footer class="text-center py-3 border-top" style="font-size:12px; color: var(--esdm-muted);">
        Sistem Arsip Dokumen Digital &mdash; Kementerian ESDM &copy; {{ date('Y') }}
    </footer>
</div>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
function toggleSidebar() {
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebar-overlay');
    const isOpen   = sidebar.classList.toggle('open');
    overlay.style.display = isOpen ? 'block' : 'none';
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebar-overlay').style.display = 'none';
}
// Close sidebar on resize to desktop
window.addEventListener('resize', () => {
    if (window.innerWidth > 768) closeSidebar();
});
</script>

@stack('scripts')
</body>
</html>
