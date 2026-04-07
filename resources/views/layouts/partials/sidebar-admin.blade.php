{{-- SIDEBAR ADMIN SMK --}}
<aside id="admin-sidebar"
    class="fixed lg:relative z-40 flex flex-col w-64 h-full bg-gray-900 text-white transition-transform duration-300 -translate-x-full lg:translate-x-0 flex-shrink-0">
    {{-- Logo --}}
    <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10">

        {{-- Kotak Hijau --}}
        <div class="w-10 h-10 bg-evergreen-600 rounded-xl flex items-center justify-center flex-shrink-0">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-6 h-6 object-contain">
        </div>

        {{-- Text --}}
        <div class="leading-tight">
            <div class="font-bold text-sm tracking-wide">RUANG</div>
            <div class="font-bold text-sm tracking-wide">BACA</div>
            <div class="text-xs text-gray-400">Administrator Area</div>
        </div>
    </div>

    {{-- School Info --}}
    <div class="px-5 py-3 border-b border-white/10">
        <div class="bg-white/5 rounded-xl px-3 py-2.5">
            <div class="text-xs text-gray-400 uppercase tracking-widest mb-0.5">Sekolah</div>
            <div class="text-sm font-semibold text-white truncate">
                {{ auth()->user()->school->name ?? 'SMK Negeri Digital' }}</div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">
        <p class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-widest">Menu Utama</p>

        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                  {{ request()->routeIs('admin.dashboard') ? 'bg-evergreen-600/20 text-evergreen-400' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
            <i
                class="fas fa-th-large w-4 text-center {{ request()->routeIs('admin.dashboard') ? 'text-evergreen-400' : 'text-gray-500' }}"></i>
            Dashboard
        </a>

        <a href="{{ route('admin.books.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                  {{ request()->routeIs('admin.books.*') ? 'bg-evergreen-600/20 text-evergreen-400' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
            <i
                class="fas fa-book w-4 text-center {{ request()->routeIs('admin.books.*') ? 'text-evergreen-400' : 'text-gray-500' }}"></i>
            Katalog Buku
        </a>

        <a href="{{ route('admin.transactions.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                  {{ request()->routeIs('admin.transactions.*') ? 'bg-evergreen-600/20 text-evergreen-400' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
            <i
                class="fas fa-exchange-alt w-4 text-center {{ request()->routeIs('admin.transactions.*') ? 'text-evergreen-400' : 'text-gray-500' }}"></i>
            Transaksi
        </a>

        <a href="{{ route('admin.members.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all group
                  {{ request()->routeIs('admin.members.*') ? 'bg-evergreen-600/20 text-evergreen-400' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
            <i
                class="fas fa-users w-4 text-center {{ request()->routeIs('admin.members.*') ? 'text-evergreen-400' : 'text-gray-500' }}"></i>
            Anggota
            {{-- Pending badge --}}
            @php
                $pendingCount = \App\Models\User::where('school_id', auth()->user()->school_id)
                    ->where('status', 'pending')
                    ->count();
            @endphp
            @if ($pendingCount > 0)
                <span
                    class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full min-w-[20px] text-center">
                    {{ $pendingCount }}
                </span>
            @endif
        </a>

        {{-- ── KELAS (NEW) ── --}}
        <a href="{{ route('admin.classes.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                  {{ request()->routeIs('admin.classes.*') ? 'bg-evergreen-600/20 text-evergreen-400' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
            <i
                class="fas fa-chalkboard w-4 text-center {{ request()->routeIs('admin.classes.*') ? 'text-evergreen-400' : 'text-gray-500' }}"></i>
            Kelas & Jurusan
        </a>

        <p class="px-3 pt-4 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-widest">Sistem</p>

        <a href="{{ route('admin.settings') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                  {{ request()->routeIs('admin.settings') ? 'bg-evergreen-600/20 text-evergreen-400' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
            <i
                class="fas fa-cog w-4 text-center {{ request()->routeIs('admin.settings') ? 'text-evergreen-400' : 'text-gray-500' }}"></i>
            Pengaturan
        </a>
    </nav>

    {{-- User Info + Logout --}}
    <div class="px-3 py-4 border-t border-white/10">
        <div class="flex items-center gap-3 px-3 py-2.5 bg-white/5 rounded-xl mb-2">
            <div
                class="w-8 h-8 rounded-full bg-gradient-to-br from-evergreen-400 to-evergreen-600 flex items-center justify-center flex-shrink-0 text-xs font-bold text-white">
                {{ strtoupper(substr(auth()->user()->full_name ?? 'A', 0, 2)) }}
            </div>
            <div class="overflow-hidden">
                <div class="text-sm font-semibold text-white truncate">{{ auth()->user()->full_name ?? 'Admin' }}</div>
                <div class="text-xs text-gray-400 truncate">Admin Perpustakaan</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-red-400 hover:bg-red-500/10 transition-all">
                <i class="fas fa-sign-out-alt w-4 text-center"></i>
                Keluar
            </button>
        </form>
    </div>
</aside>
