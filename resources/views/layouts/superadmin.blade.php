<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Ruang Baca Superadmin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --gdeep:   #0d3d2e;
            --gmid:    #166534;
            --gbright: #22c55e;
            --glight:  #bbf7d0;
            --cream:   #faf7f2;
            --warm:    #fffef9;
            --ink:     #0f1a14;
            --muted:   #6b7c6e;
            --border:  rgba(22,101,52,0.10);
            --border2: rgba(22,101,52,0.20);
            --surface: #f4f9f5;
        }

        * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; margin: 0; padding: 0; }
        body { background: var(--cream); color: var(--ink); }

        /* ── NAVBAR ── */
        #sa-navbar {
            height: 60px;
            background: var(--gdeep);
            display: flex;
            align-items: center;
            padding: 0 28px;
            gap: 20px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 0 rgba(255,255,255,0.05), 0 4px 20px rgba(13,61,46,0.3);
        }

        /* Logo (kiri) */
        .navbar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            flex-shrink: 0;
        }
        .navbar-logo-icon {
            width: 34px;
            height: 34px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gbright);
            font-size: 15px;
        }
        .navbar-logo-text {
            line-height: 1.1;
        }
        .navbar-logo-r {
            font-family: 'Playfair Display', serif;
            font-size: 12px;
            font-weight: 700;
            color: rgba(255,255,255,0.5);
            letter-spacing: 2.5px;
            display: block;
            text-transform: uppercase;
        }
        .navbar-logo-b {
            font-family: 'Playfair Display', serif;
            font-size: 12px;
            font-weight: 700;
            color: var(--gbright);
            letter-spacing: 2.5px;
            display: block;
            text-transform: uppercase;
        }

        /* Divider */
        .navbar-divider {
            width: 1px;
            height: 28px;
            background: rgba(255,255,255,0.1);
            flex-shrink: 0;
        }

        /* Panel badge */
        .navbar-panel-tag {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.8px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.35);
        }
        .navbar-panel-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--gbright);
            box-shadow: 0 0 6px var(--gbright);
        }

        /* Nav links (tengah) */
        .navbar-nav {
            display: flex;
            align-items: center;
            gap: 2px;
            margin-left: 8px;
        }
        .navbar-link {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 500;
            color: rgba(255,255,255,0.5);
            text-decoration: none;
            transition: all 0.15s;
        }
        .navbar-link:hover {
            background: rgba(255,255,255,0.06);
            color: rgba(255,255,255,0.85);
        }
        .navbar-link.active {
            background: rgba(34,197,94,0.12);
            color: var(--gbright);
            font-weight: 600;
        }
        .navbar-link i {
            font-size: 12px;
            width: 14px;
            text-align: center;
        }

        /* Spacer */
        .navbar-spacer { flex: 1; }

        /* Right side actions */
        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Notif button */
        .navbar-btn {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: rgba(255,255,255,0.5);
            font-size: 13px;
            transition: all 0.15s;
            position: relative;
        }
        .navbar-btn:hover {
            background: rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.85);
        }
        .navbar-notif-dot {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #ef4444;
            border: 1.5px solid var(--gdeep);
        }

        /* Profile button (pojok kanan) */
        .navbar-profile {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 5px 12px 5px 6px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 100px;
            cursor: pointer;
            transition: all 0.15s;
            margin-left: 2px;
        }
        .navbar-profile:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.18);
        }
        .navbar-profile-avatar {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: rgba(34,197,94,0.2);
            border: 1px solid rgba(34,197,94,0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
            color: var(--gbright);
            flex-shrink: 0;
            overflow: hidden;
        }
        .navbar-profile-avatar img {
            width: 100%; height: 100%; object-fit: cover;
        }
        .navbar-profile-info { line-height: 1.2; }
        .navbar-profile-name {
            font-size: 12px;
            font-weight: 600;
            color: rgba(255,255,255,0.85);
        }
        .navbar-profile-role {
            font-size: 9px;
            color: rgba(255,255,255,0.35);
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        .navbar-profile-chevron {
            font-size: 9px;
            color: rgba(255,255,255,0.3);
            margin-left: 2px;
        }

        /* ── DROPDOWNS ── */
        .sa-dropdown {
            position: fixed;
            top: 68px;
            background: white;
            border-radius: 14px;
            box-shadow: 0 8px 32px rgba(13,61,46,0.14), 0 2px 8px rgba(0,0,0,0.08);
            border: 1px solid var(--border);
            z-index: 200;
            overflow: hidden;
        }
        .sa-dropdown.hidden { display: none; }

        .dropdown-header {
            padding: 13px 16px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .dropdown-header-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--ink);
        }
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            font-size: 13px;
            color: var(--ink);
            text-decoration: none;
            transition: background 0.12s;
            cursor: pointer;
            background: none;
            border: none;
            width: 100%;
            font-family: 'DM Sans', sans-serif;
        }
        .dropdown-item:hover { background: var(--surface); }
        .dropdown-item i { width: 14px; color: var(--muted); font-size: 12px; }
        .dropdown-item.danger { color: #991b1b; }
        .dropdown-item.danger:hover { background: rgba(220,38,38,0.05); }
        .dropdown-item.danger i { color: #ef4444; }
        .dropdown-divider { height: 1px; background: var(--border); }

        /* ── MAIN CONTENT ── */
        .sa-main {
            min-height: calc(100vh - 60px);
            background: var(--cream);
            padding: 28px;
        }

        /* ── ALERT BANNERS ── */
        .alert-success {
            display: flex; align-items: center; gap: 10px;
            background: rgba(34,197,94,0.08);
            border: 1px solid rgba(34,197,94,0.2);
            color: #166534; border-radius: 12px;
            padding: 11px 16px; font-size: 13px; font-weight: 500;
            margin-bottom: 18px; animation: fadeUp 0.3s ease both;
        }
        .alert-error {
            display: flex; align-items: center; gap: 10px;
            background: rgba(220,38,38,0.07);
            border: 1px solid rgba(220,38,38,0.18);
            color: #991b1b; border-radius: 12px;
            padding: 11px 16px; font-size: 13px; font-weight: 500;
            margin-bottom: 18px; animation: fadeUp 0.3s ease both;
        }

        @keyframes fadeUp {
            from { opacity:0; transform:translateY(8px); }
            to   { opacity:1; transform:translateY(0); }
        }

        /* ── SCROLLBAR ── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 4px; }
    </style>
    @stack('styles')
</head>
<body>

    {{-- ===== NAVBAR ===== --}}
    <nav id="sa-navbar">

        {{-- Logo (pojok kiri) --}}
        <a href="{{ route('superadmin.dashboard') }}" class="navbar-logo">
            <div class="navbar-logo-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <div class="navbar-logo-text">
                <span class="navbar-logo-r">Ruang</span>
                <span class="navbar-logo-b">Baca</span>
            </div>
        </a>

        <div class="navbar-divider"></div>

        <div class="navbar-panel-tag">
            <span class="navbar-panel-dot"></span>
            Superadmin
        </div>

        {{-- Nav links --}}
        <div class="navbar-nav">
            <a href="{{ route('superadmin.dashboard') }}"
               class="navbar-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>

            {{-- Uncomment saat route tersedia:
            <a href="{{ route('superadmin.schools.index') }}"
               class="navbar-link {{ request()->routeIs('superadmin.schools.*') ? 'active' : '' }}">
                <i class="fas fa-school"></i>
                <span>Sekolah</span>
            </a>

            <a href="{{ route('superadmin.users.index') }}"
               class="navbar-link {{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Pengguna</span>
                @php $pendingTotal = \App\Models\User::where('status','pending')->count(); @endphp
                @if($pendingTotal > 0)
                    <span style="background:#ef4444;color:white;font-size:9px;font-weight:700;padding:1px 6px;border-radius:10px;">{{ $pendingTotal }}</span>
                @endif
            </a>

            <a href="{{ route('superadmin.analytics') }}"
               class="navbar-link {{ request()->routeIs('superadmin.analytics') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i>
                <span>Analitik</span>
            </a>

            <a href="{{ route('superadmin.settings') }}"
               class="navbar-link {{ request()->routeIs('superadmin.settings') ? 'active' : '' }}">
                <i class="fas fa-cog"></i>
                <span>Pengaturan</span>
            </a>
            --}}
        </div>

        {{-- Topbar nav (breadcrumb dari child page) --}}
        @hasSection('topbar-nav')
            <div style="display:flex;align-items:center;gap:0;border-left:1px solid rgba(255,255,255,0.1);margin-left:4px;padding-left:14px;">
                @yield('topbar-nav')
            </div>
        @endif

        <div class="navbar-spacer"></div>

        {{-- Right actions (pojok kanan) --}}
        <div class="navbar-actions">

            {{-- Notifikasi --}}
            @php
                $saNotifCount = \App\Models\User::where('status','pending')->count();
            @endphp
            <div class="navbar-btn" onclick="toggleDropdown('sa-notif-dropdown')">
                <i class="fas fa-bell"></i>
                @if($saNotifCount > 0)
                    <span class="navbar-notif-dot"></span>
                @endif
            </div>

            {{-- Help --}}
            <div class="navbar-btn">
                <i class="fas fa-question-circle"></i>
            </div>

            {{-- Profile + Logout (pojok kanan) --}}
            <div class="navbar-profile" onclick="toggleDropdown('sa-profile-dropdown')">
                <div class="navbar-profile-avatar">
                    @if(auth()->user()->photo)
                        <img src="{{ asset('storage/'.auth()->user()->photo) }}" alt="">
                    @else
                        {{ strtoupper(substr(auth()->user()->full_name ?? 'SA', 0, 2)) }}
                    @endif
                </div>
                <div class="navbar-profile-info">
                    <div class="navbar-profile-name">{{ auth()->user()->full_name ?? 'Superadmin' }}</div>
                    <div class="navbar-profile-role">Global Admin</div>
                </div>
                <i class="fas fa-chevron-down navbar-profile-chevron"></i>
            </div>
        </div>

        {{-- Notif Dropdown --}}
        <div id="sa-notif-dropdown" class="sa-dropdown hidden" style="right:80px;width:300px;">
            <div class="dropdown-header">
                <span class="dropdown-header-title">Notifikasi</span>
                @if($saNotifCount > 0)
                    <span style="background:rgba(220,38,38,0.08);color:#991b1b;font-size:10px;font-weight:700;padding:2px 8px;border-radius:8px;">{{ $saNotifCount }} pending</span>
                @endif
            </div>
            @if($saNotifCount > 0)
                {{-- Uncomment saat route tersedia:
                <a href="{{ route('superadmin.users.index') }}" class="dropdown-item">
                    <div style="width:32px;height:32px;background:rgba(234,153,34,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-user-clock" style="color:#b45309;font-size:11px;"></i>
                    </div>
                    <div>
                        <div style="font-size:12px;font-weight:600;color:var(--ink);">{{ $saNotifCount }} pengguna menunggu persetujuan</div>
                        <div style="font-size:11px;color:var(--muted);margin-top:1px;">Klik untuk kelola pendaftaran</div>
                    </div>
                </a>
                --}}
            @else
                <div style="padding:28px;text-align:center;color:var(--muted);">
                    <i class="fas fa-check-circle" style="font-size:22px;color:var(--glight);display:block;margin-bottom:8px;opacity:0.6;"></i>
                    <span style="font-size:12px;">Semua sudah beres!</span>
                </div>
            @endif
        </div>

        {{-- Profile Dropdown --}}
        <div id="sa-profile-dropdown" class="sa-dropdown hidden" style="right:24px;width:220px;">
            <div class="dropdown-header" style="flex-direction:column;align-items:flex-start;gap:2px;">
                <div style="font-size:13px;font-weight:700;color:var(--ink);">{{ auth()->user()->full_name }}</div>
                <div style="font-size:11px;color:var(--muted);">Global Superadmin</div>
            </div>
            {{-- Uncomment saat route tersedia:
            <a href="{{ route('superadmin.settings') }}" class="dropdown-item">
                <i class="fas fa-cog"></i> Pengaturan
            </a>
            --}}
            <a href="{{ route('superadmin.dashboard') }}" class="dropdown-item">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
            <div class="dropdown-divider"></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item danger">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </button>
            </form>
        </div>

    </nav>

    {{-- ===== PAGE CONTENT ===== --}}
    <main class="sa-main">
        @if(session('success'))
            <div class="alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
                <button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;color:inherit;opacity:0.6;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
                <button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;color:inherit;opacity:0.6;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @yield('content')
    </main>

@stack('scripts')
<script>
    function toggleDropdown(id) {
        document.querySelectorAll('.sa-dropdown').forEach(el => {
            if (el.id !== id) el.classList.add('hidden');
        });
        document.getElementById(id).classList.toggle('hidden');
    }

    document.addEventListener('click', function(e) {
        const isDropdownTrigger = e.target.closest('[onclick*="toggleDropdown"]');
        const isInsideDropdown  = e.target.closest('.sa-dropdown');
        if (!isDropdownTrigger && !isInsideDropdown) {
            document.querySelectorAll('.sa-dropdown').forEach(el => el.classList.add('hidden'));
        }
    });
</script>
</body>
</html>
