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
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
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

        * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
        body { background: var(--cream); color: var(--ink); overflow: hidden; }

        /* ── SIDEBAR ── */
        #sa-sidebar {
            width: 220px;
            background: var(--gdeep);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            height: 100vh;
            position: relative;
            z-index: 40;
            transition: transform 0.3s ease;
        }

        .sidebar-logo {
            padding: 22px 20px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }
        .sidebar-brand {
            display: flex; align-items: center; gap: 10px;
        }
        .sidebar-brand-icon {
            width: 36px; height: 36px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px;
        }
        .sidebar-brand-text { line-height: 1.1; }
        .sidebar-brand-r {
            font-family: 'Playfair Display', serif;
            font-size: 13px; font-weight: 700;
            color: rgba(255,255,255,0.6);
            letter-spacing: 2px;
            display: block;
        }
        .sidebar-brand-b {
            font-family: 'Playfair Display', serif;
            font-size: 13px; font-weight: 700;
            color: var(--gbright);
            letter-spacing: 2px;
            display: block;
        }
        .sidebar-subbrand {
            font-size: 9px; font-weight: 600;
            letter-spacing: 2px; text-transform: uppercase;
            color: rgba(255,255,255,0.25);
            margin-top: 6px;
        }

        .sidebar-nav {
            flex: 1; padding: 16px 12px;
            display: flex; flex-direction: column; gap: 2px;
            overflow-y: auto;
        }
        .sidebar-section-label {
            font-size: 9px; font-weight: 700; letter-spacing: 2px;
            text-transform: uppercase; color: rgba(255,255,255,0.25);
            padding: 8px 8px 4px; margin-top: 8px;
        }
        .sidebar-link {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; border-radius: 10px;
            font-size: 13px; font-weight: 500;
            color: rgba(255,255,255,0.55);
            text-decoration: none;
            transition: all 0.15s;
            position: relative;
        }
        .sidebar-link:hover {
            background: rgba(255,255,255,0.06);
            color: rgba(255,255,255,0.85);
        }
        .sidebar-link.active {
            background: rgba(34,197,94,0.12);
            color: var(--gbright);
            font-weight: 600;
        }
        .sidebar-link.active::before {
            content: '';
            position: absolute; left: 0; top: 50%;
            transform: translateY(-50%);
            width: 3px; height: 20px;
            background: var(--gbright);
            border-radius: 0 3px 3px 0;
        }
        .sidebar-link i { width: 16px; text-align: center; font-size: 13px; }
        .sidebar-badge {
            margin-left: auto;
            background: rgba(248,81,73,0.9);
            color: white; font-size: 9px; font-weight: 700;
            padding: 1px 6px; border-radius: 10px;
        }

        .sidebar-footer {
            padding: 14px 16px;
            border-top: 1px solid rgba(255,255,255,0.07);
        }
        .sidebar-user {
            display: flex; align-items: center; gap: 10px;
        }
        .sidebar-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: rgba(34,197,94,0.15);
            border: 1px solid rgba(34,197,94,0.3);
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700; color: var(--gbright);
            flex-shrink: 0;
            overflow: hidden;
        }
        .sidebar-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .sidebar-user-name { font-size: 12px; font-weight: 600; color: rgba(255,255,255,0.8); }
        .sidebar-user-role { font-size: 10px; color: rgba(255,255,255,0.35); }

        /* ── TOPBAR ── */
        #sa-topbar {
            height: 58px;
            background: white;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            padding: 0 24px;
            gap: 16px;
            flex-shrink: 0;
        }
        .topbar-panel-tag {
            display: flex; align-items: center; gap: 8px;
            font-size: 11px; font-weight: 700; letter-spacing: 1.5px;
            text-transform: uppercase; color: var(--gmid);
            padding: 4px 12px; border-radius: 100px;
            background: rgba(22,101,52,0.07);
            border: 1px solid var(--border2);
        }
        .topbar-search {
            flex: 1; max-width: 280px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            display: flex; align-items: center; gap: 8px;
            padding: 0 12px; height: 36px;
        }
        .topbar-search input {
            background: none; border: none; outline: none;
            font-size: 12px; color: var(--ink); width: 100%;
            font-family: 'DM Sans', sans-serif;
        }
        .topbar-search input::placeholder { color: var(--muted); }
        .topbar-search i { color: var(--muted); font-size: 12px; }

        .topbar-actions { margin-left: auto; display: flex; align-items: center; gap: 8px; }
        .topbar-btn {
            width: 36px; height: 36px; border-radius: 10px;
            background: var(--surface); border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: var(--muted); font-size: 13px;
            transition: all 0.15s; position: relative;
        }
        .topbar-btn:hover { border-color: var(--border2); color: var(--gmid); }
        .topbar-notif-dot {
            position: absolute; top: 6px; right: 6px;
            width: 7px; height: 7px; border-radius: 50%;
            background: #ef4444; border: 1.5px solid white;
        }
        .topbar-profile {
            display: flex; align-items: center; gap: 8px;
            padding: 5px 12px 5px 6px;
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 100px; cursor: pointer; transition: all 0.15s;
        }
        .topbar-profile:hover { border-color: var(--border2); }
        .topbar-profile-avatar {
            width: 26px; height: 26px; border-radius: 50%;
            background: rgba(22,101,52,0.12);
            display: flex; align-items: center; justify-content: center;
            font-size: 10px; font-weight: 700; color: var(--gmid);
        }
        .topbar-profile-name { font-size: 12px; font-weight: 600; color: var(--ink); }

        /* ── MAIN CONTENT ── */
        .sa-main {
            flex: 1; overflow-y: auto;
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
<div style="display:flex; height:100vh; overflow:hidden;">

    {{-- ===== SIDEBAR ===== --}}
    <aside id="sa-sidebar">
        {{-- Logo --}}
        <div class="sidebar-logo">
            <a href="{{ route('superadmin.dashboard') }}" class="sidebar-brand" style="text-decoration:none;">
                <div class="sidebar-brand-icon">📚</div>
                <div class="sidebar-brand-text">
                    <span class="sidebar-brand-r">RUANG</span>
                    <span class="sidebar-brand-b">BACA</span>
                </div>
            </a>
            <div class="sidebar-subbrand">Superadmin Panel</div>
        </div>

        {{-- Nav --}}
        <nav class="sidebar-nav">
            <div class="sidebar-section-label">Menu Utama</div>

            <a href="{{ route('superadmin.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>

            {{-- <a href="{{ route('superadmin.schools.index') }}"
               class="sidebar-link {{ request()->routeIs('superadmin.schools.*') ? 'active' : '' }}">
                <i class="fas fa-school"></i>
                <span>Kelola Sekolah</span>
            </a> --}}

            {{-- <a href="{{ route('superadmin.users.index') }}"
               class="sidebar-link {{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Kelola Pengguna</span>
                @php $pendingTotal = \App\Models\User::where('status','pending')->count(); @endphp
                @if($pendingTotal > 0)
                    <span class="sidebar-badge">{{ $pendingTotal }}</span>
                @endif
            </a> --}}

            <div class="sidebar-section-label">Laporan</div>

            {{-- <a href="{{ route('superadmin.analytics') }}"
               class="sidebar-link {{ request()->routeIs('superadmin.analytics') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i>
                <span>Analitik</span>
            </a> --}}

            <div class="sidebar-section-label">Sistem</div>

            {{-- <a href="{{ route('superadmin.settings') }}"
               class="sidebar-link {{ request()->routeIs('superadmin.settings') ? 'active' : '' }}">
                <i class="fas fa-cog"></i>
                <span>Pengaturan</span>
            </a> --}}
        </nav>

        {{-- Footer --}}
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar">
                    @if(auth()->user()->photo)
                        <img src="{{ asset('storage/'.auth()->user()->photo) }}" alt="">
                    @else
                        {{ strtoupper(substr(auth()->user()->full_name ?? 'SA', 0, 2)) }}
                    @endif
                </div>
                <div>
                    <div class="sidebar-user-name">{{ auth()->user()->full_name ?? 'Superadmin' }}</div>
                    <div class="sidebar-user-role">Global Admin Access</div>
                </div>
            </div>
        </div>
    </aside>

    {{-- ===== MAIN WRAPPER ===== --}}
    <div style="flex:1; display:flex; flex-direction:column; overflow:hidden; min-width:0;">

        {{-- ===== TOPBAR ===== --}}
        <header id="sa-topbar">
            {{-- Mobile hamburger --}}
            <button onclick="toggleSidebar()" class="topbar-btn lg:hidden" style="flex-shrink:0;">
                <i class="fas fa-bars"></i>
            </button>

            {{-- Panel tag --}}
            <div class="topbar-panel-tag">
                <span style="width:7px;height:7px;background:var(--gbright);border-radius:50%;display:inline-block;"></span>
                Superadmin Panel
            </div>

            {{-- Page breadcrumb nav links --}}
            @hasSection('topbar-nav')
                <div style="display:flex;align-items:center;gap:0;border-left:1px solid var(--border);margin-left:8px;padding-left:16px;">
                    @yield('topbar-nav')
                </div>
            @endif

            {{-- Search --}}
            <div class="topbar-search" style="margin-left:16px;">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Cari sekolah, pengguna...">
            </div>

            {{-- Right actions --}}
            <div class="topbar-actions">
                {{-- Notif --}}
                <div class="topbar-btn" onclick="toggleDropdown('sa-notif-dropdown')">
                    <i class="fas fa-bell"></i>
                    @php
                        $saNotifCount = \App\Models\User::where('status','pending')->count();
                    @endphp
                    @if($saNotifCount > 0)
                        <span class="topbar-notif-dot"></span>
                    @endif
                </div>

                {{-- Help --}}
                <div class="topbar-btn">
                    <i class="fas fa-question-circle"></i>
                </div>

                {{-- Settings shortcut --}}
                {{-- <a href="{{ route('superadmin.settings') }}" class="topbar-btn">
                    <i class="fas fa-cog"></i>
                </a> --}}

                {{-- Profile --}}
                <div class="topbar-profile" onclick="toggleDropdown('sa-profile-dropdown')">
                    <div class="topbar-profile-avatar">
                        {{ strtoupper(substr(auth()->user()->full_name ?? 'SA', 0, 2)) }}
                    </div>
                    <span class="topbar-profile-name">Ruang Baca</span>
                    <i class="fas fa-chevron-down" style="font-size:10px;color:var(--muted);margin-left:2px;"></i>
                </div>
            </div>

            {{-- Notif Dropdown --}}
            <div id="sa-notif-dropdown" class="hidden" style="position:absolute;top:58px;right:160px;width:300px;background:white;border-radius:16px;box-shadow:0 8px 32px rgba(13,61,46,0.12);border:1px solid var(--border);z-index:100;overflow:hidden;">
                <div style="padding:14px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-size:13px;font-weight:700;color:var(--ink);">Notifikasi</span>
                    @if($saNotifCount > 0)
                        <span style="background:rgba(220,38,38,0.08);color:#991b1b;font-size:10px;font-weight:700;padding:2px 8px;border-radius:8px;">{{ $saNotifCount }} pending</span>
                    @endif
                </div>
                @if($saNotifCount > 0)
                    {{-- <a href="{{ route('superadmin.users.index') }}" style="display:flex;align-items:center;gap:12px;padding:12px 16px;text-decoration:none;transition:background 0.15s;" onmouseover="this.style.background='var(--surface)'" onmouseout="this.style.background='transparent'">
                        <div style="width:34px;height:34px;background:rgba(234,153,34,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas fa-user-clock" style="color:#b45309;font-size:12px;"></i>
                        </div>
                        <div>
                            <div style="font-size:12px;font-weight:600;color:var(--ink);">{{ $saNotifCount }} pengguna menunggu persetujuan</div>
                            <div style="font-size:11px;color:var(--muted);margin-top:2px;">Klik untuk kelola pendaftaran</div>
                        </div>
                    </a> --}}
                @else
                    <div style="padding:28px;text-align:center;color:var(--muted);font-size:13px;">
                        <i class="fas fa-check-circle" style="font-size:24px;color:var(--glight);display:block;margin-bottom:8px;"></i>
                        Semua sudah beres!
                    </div>
                @endif
            </div>

            {{-- Profile Dropdown --}}
            <div id="sa-profile-dropdown" class="hidden" style="position:absolute;top:58px;right:24px;width:220px;background:white;border-radius:14px;box-shadow:0 8px 32px rgba(13,61,46,0.12);border:1px solid var(--border);z-index:100;overflow:hidden;">
                <div style="padding:14px 16px;border-bottom:1px solid var(--border);">
                    <div style="font-size:13px;font-weight:700;color:var(--ink);">{{ auth()->user()->full_name }}</div>
                    <div style="font-size:11px;color:var(--muted);">Global Superadmin</div>
                </div>
                {{-- <a href="{{ route('superadmin.settings') }}" style="display:flex;align-items:center;gap:10px;padding:10px 16px;font-size:13px;color:var(--ink);text-decoration:none;transition:background 0.15s;" onmouseover="this.style.background='var(--surface)'" onmouseout="this.style.background='transparent'">
                    <i class="fas fa-cog" style="width:14px;color:var(--muted);"></i> Pengaturan
                </a> --}}
                <div style="border-top:1px solid var(--border);">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" style="width:100%;display:flex;align-items:center;gap:10px;padding:10px 16px;font-size:13px;color:#991b1b;background:none;border:none;cursor:pointer;font-family:'DM Sans',sans-serif;transition:background 0.15s;" onmouseover="this.style.background='rgba(220,38,38,0.05)'" onmouseout="this.style.background='transparent'">
                            <i class="fas fa-sign-out-alt" style="width:14px;"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </header>

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

        {{-- ===== FOOTER ===== --}}
        {{-- <footer style="background:var(--gdeep);padding:14px 28px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
            <span style="font-size:11px;color:rgba(255,255,255,0.3);letter-spacing:0.5px;">
                &copy; {{ date('Y') }} SUPERADMIN PANEL &nbsp;|&nbsp; SISTEM PERPUSTAKAAN SEKOLAH DIGITAL
            </span>
            <div style="display:flex;gap:20px;">
                <a href="#" style="font-size:10px;color:rgba(255,255,255,0.25);text-decoration:none;letter-spacing:1px;text-transform:uppercase;">Terms of Service</a>
                <a href="#" style="font-size:10px;color:rgba(255,255,255,0.25);text-decoration:none;letter-spacing:1px;text-transform:uppercase;">Privacy Policy</a>
                <a href="#" style="font-size:10px;color:rgba(255,255,255,0.25);text-decoration:none;letter-spacing:1px;text-transform:uppercase;">System Status</a>
            </div>
        </footer> --}}
    </div>
</div>

{{-- Sidebar overlay (mobile) --}}
<div id="sa-sidebar-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:39;" onclick="toggleSidebar()"></div>

@stack('scripts')
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sa-sidebar');
        const overlay = document.getElementById('sa-sidebar-overlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.style.display = overlay.style.display === 'none' ? 'block' : 'none';
    }

    function toggleDropdown(id) {
        document.querySelectorAll('[id$="-dropdown"]').forEach(el => {
            if (el.id !== id) el.classList.add('hidden');
        });
        document.getElementById(id).classList.toggle('hidden');
    }
    document.addEventListener('click', function(e) {
        if (!e.target.closest('[onclick*="toggleDropdown"]') && !e.target.closest('[id$="-dropdown"]')) {
            document.querySelectorAll('[id$="-dropdown"]').forEach(el => el.classList.add('hidden'));
        }
    });
</script>
</body>
</html>
