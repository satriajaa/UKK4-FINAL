{{-- SIDEBAR STUDENT --}}
<aside
    id="student-sidebar"
    class="fixed lg:relative z-40 flex flex-col w-64 h-full bg-gray-900 text-white transition-transform duration-300 -translate-x-full lg:translate-x-0 flex-shrink-0"
>
    {{-- Logo --}}
    <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10">
        <div class="w-9 h-9 bg-evergreen-600 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-book-open text-white text-base"></i>
        </div>
        <div>
            <div class="font-bold text-sm leading-tight tracking-wide">RUANG BACA</div>
            <div class="text-xs text-gray-400 leading-tight">Portal Siswa</div>
        </div>
    </div>

    {{-- Student Info --}}
    <div class="px-5 py-3 border-b border-white/10">
        <div class="bg-white/5 rounded-xl px-3 py-2.5">
            <div class="text-xs text-gray-400 uppercase tracking-widest mb-0.5">Kelas</div>
            <div class="text-sm font-semibold text-white truncate">{{ auth()->user()->class->name ?? '—' }}</div>
            <div class="text-xs text-gray-500 truncate">{{ auth()->user()->school->name ?? '' }}</div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">
        <p class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-widest">Menu</p>

        <a href="{{ route('student.dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                  {{ request()->routeIs('student.dashboard') ? 'bg-evergreen-600/20 text-evergreen-400' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
            <i class="fas fa-home w-4 text-center {{ request()->routeIs('student.dashboard') ? 'text-evergreen-400' : 'text-gray-500' }}"></i>
            Beranda
        </a>

        <a href="{{ route('student.books.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                  {{ request()->routeIs('student.books.*') ? 'bg-evergreen-600/20 text-evergreen-400' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
            <i class="fas fa-search w-4 text-center {{ request()->routeIs('student.books.*') ? 'text-evergreen-400' : 'text-gray-500' }}"></i>
            Cari Buku
        </a>

        <a href="{{ route('student.history') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                  {{ request()->routeIs('student.history') ? 'bg-evergreen-600/20 text-evergreen-400' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
            <i class="fas fa-history w-4 text-center {{ request()->routeIs('student.history') ? 'text-evergreen-400' : 'text-gray-500' }}"></i>
            Riwayat Pinjam
            {{-- Active borrow count badge --}}
            @php
                $activeBorrows = \App\Models\Borrowing::where('user_id', auth()->id())->where('status','borrowed')->count();
            @endphp
            @if($activeBorrows > 0)
            <span class="ml-auto bg-evergreen-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                {{ $activeBorrows }}
            </span>
            @endif
        </a>

        <a href="{{ route('student.wishlist') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                  {{ request()->routeIs('student.wishlist') ? 'bg-evergreen-600/20 text-evergreen-400' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
            <i class="fas fa-heart w-4 text-center {{ request()->routeIs('student.wishlist') ? 'text-evergreen-400' : 'text-gray-500' }}"></i>
            Wishlist
        </a>

        <p class="px-3 pt-4 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-widest">Akun</p>

        <a href="{{ route('student.profile') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                  {{ request()->routeIs('student.profile') ? 'bg-evergreen-600/20 text-evergreen-400' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
            <i class="fas fa-user w-4 text-center {{ request()->routeIs('student.profile') ? 'text-evergreen-400' : 'text-gray-500' }}"></i>
            Profil Saya
        </a>
    </nav>

    {{-- User + Logout --}}
    <div class="px-3 py-4 border-t border-white/10">
        <div class="flex items-center gap-3 px-3 py-2.5 bg-white/5 rounded-xl mb-2">
            @if(auth()->user()->photo)
            <img src="{{ asset('storage/'.auth()->user()->photo) }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0" alt="">
            @else
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-evergreen-400 to-evergreen-600 flex items-center justify-center flex-shrink-0 text-xs font-bold text-white">
                {{ strtoupper(substr(auth()->user()->full_name ?? 'S', 0, 2)) }}
            </div>
            @endif
            <div class="overflow-hidden">
                <div class="text-sm font-semibold text-white truncate">{{ auth()->user()->full_name ?? 'Siswa' }}</div>
                <div class="text-xs text-gray-400 truncate">{{ auth()->user()->student_id ?? 'Siswa' }}</div>
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
