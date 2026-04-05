{{-- SIDEBAR SUPERADMIN --}}
<aside
    id="superadmin-sidebar"
    class="fixed lg:relative z-40 flex flex-col w-64 h-full bg-slate-900 text-white transition-transform duration-300 -translate-x-full lg:translate-x-0 flex-shrink-0"
>
    {{-- Logo --}}
    <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10">
        <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-crown text-white text-sm"></i>
        </div>
        <div>
            <div class="font-bold text-sm leading-tight tracking-wide">RUANG BACA</div>
            <div class="text-xs text-indigo-400 leading-tight">Super Administrator</div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">
        <p class="px-3 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-widest">Panel Kontrol</p>

        <a href="{{ route('superadmin.dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                  {{ request()->routeIs('superadmin.dashboard') ? 'bg-indigo-600/20 text-indigo-400' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
            <i class="fas fa-th-large w-4 text-center {{ request()->routeIs('superadmin.dashboard') ? 'text-indigo-400' : 'text-slate-500' }}"></i>
            Dashboard
        </a>

        <a href="{{ route('superadmin.schools.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                  {{ request()->routeIs('superadmin.schools.*') ? 'bg-indigo-600/20 text-indigo-400' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
            <i class="fas fa-school w-4 text-center {{ request()->routeIs('superadmin.schools.*') ? 'text-indigo-400' : 'text-slate-500' }}"></i>
            Kelola Sekolah
        </a>

        <a href="{{ route('superadmin.users.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                  {{ request()->routeIs('superadmin.users.*') ? 'bg-indigo-600/20 text-indigo-400' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
            <i class="fas fa-users-cog w-4 text-center {{ request()->routeIs('superadmin.users.*') ? 'text-indigo-400' : 'text-slate-500' }}"></i>
            Kelola Pengguna
        </a>

        <a href="{{ route('superadmin.analytics') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                  {{ request()->routeIs('superadmin.analytics') ? 'bg-indigo-600/20 text-indigo-400' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
            <i class="fas fa-chart-line w-4 text-center {{ request()->routeIs('superadmin.analytics') ? 'text-indigo-400' : 'text-slate-500' }}"></i>
            Analitik
        </a>

        <p class="px-3 pt-4 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-widest">Konfigurasi</p>

        <a href="{{ route('superadmin.settings') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                  {{ request()->routeIs('superadmin.settings') ? 'bg-indigo-600/20 text-indigo-400' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
            <i class="fas fa-sliders-h w-4 text-center {{ request()->routeIs('superadmin.settings') ? 'text-indigo-400' : 'text-slate-500' }}"></i>
            Pengaturan Sistem
        </a>
    </nav>

    {{-- User + Logout --}}
    <div class="px-3 py-4 border-t border-white/10">
        <div class="flex items-center gap-3 px-3 py-2.5 bg-white/5 rounded-xl mb-2">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center flex-shrink-0 text-xs font-bold text-white">
                SA
            </div>
            <div class="overflow-hidden">
                <div class="text-sm font-semibold text-white truncate">{{ auth()->user()->full_name ?? 'Super Admin' }}</div>
                <div class="text-xs text-slate-400 truncate">Super Administrator</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-red-400 hover:bg-red-500/10 transition-all">
                <i class="fas fa-sign-out-alt w-4 text-center"></i>
                Keluar
            </button>
        </form>
    </div>
</aside>    
