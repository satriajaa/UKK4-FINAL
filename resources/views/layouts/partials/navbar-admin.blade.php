{{-- NAVBAR ADMIN SMK --}}
<header class="flex items-center justify-between h-16 px-6 bg-white border-b border-gray-200 flex-shrink-0">

    {{-- Left: Hamburger + Page Title --}}
    <div class="flex items-center gap-4">
        {{-- Mobile hamburger --}}
        <button onclick="toggleSidebar()"
            class="lg:hidden p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
            <i class="fas fa-bars text-lg"></i>
        </button>

        {{-- Page Title --}}
        <div>
            <h1 class="text-lg font-bold text-gray-900 leading-tight">@yield('page-title', 'Dashboard')</h1>
            <p class="text-xs text-gray-500 leading-tight hidden sm:block">@yield('page-subtitle', 'Selamat datang di panel admin perpustakaan')</p>
        </div>
    </div>

    {{-- Right: Actions --}}
    <div class="flex items-center gap-3">

        {{-- Date --}}
        <div class="hidden md:flex items-center gap-2 text-sm text-gray-500">
            <i class="fas fa-calendar-alt text-gray-400"></i>
            <span id="navbar-date"></span>
        </div>

        {{-- Notification Bell --}}
        {{-- <div class="relative">
            <button class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition"
                onclick="toggleDropdown('notif-dropdown')">
                <i class="fas fa-bell text-lg"></i>
                @php
                    $pendingCount = \App\Models\User::where('school_id', auth()->user()->school_id)
                        ->where('status', 'pending')
                        ->count();
                @endphp
                @if ($pendingCount > 0)
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
                @endif
            </button>
            Notif Dropdown
            <div id="notif-dropdown"
                class="hidden absolute right-0 top-full mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 z-50">
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <span class="font-semibold text-sm text-gray-900">Notifikasi</span>
                    @if ($pendingCount > 0)
                        <span
                            class="bg-red-100 text-red-600 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $pendingCount }}
                            pending</span>
                    @endif
                </div>
                @if ($pendingCount > 0)
                    <a href="{{ route('admin.members.index') }}"
                        class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition">
                        <div
                            class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-user-clock text-amber-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $pendingCount }} anggota menunggu
                                persetujuan</p>
                            <p class="text-xs text-gray-500 mt-0.5">Klik untuk kelola pendaftaran</p>
                        </div>
                    </a>
                @else
                    <div class="px-4 py-8 text-center text-sm text-gray-400">
                        <i class="fas fa-bell-slash text-2xl mb-2 block text-gray-300"></i>
                        Tidak ada notifikasi baru
                    </div>
                @endif
            </div>
        </div> --}}
{{-- Ganti isi notif-dropdown di navbar admin --}}
<div class="relative">
    @php
    $pendingMemberCount = \App\Models\User::where('school_id', auth()->user()->school_id)->where('status','pending')->count();
    $pendingBorrowCount = \App\Models\Borrowing::where('school_id', auth()->user()->school_id)->where('status','pending')->count();
    $pendingReturnCount = \App\Models\Borrowing::where('school_id', auth()->user()->school_id)->where('status','return_requested')->count();
    $totalNotif = $pendingMemberCount + $pendingBorrowCount + $pendingReturnCount;
@endphp

{{-- Bell button --}}
<button class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition"
    onclick="toggleDropdown('notif-dropdown')">
    <i class="fas fa-bell text-lg"></i>
    @if($totalNotif > 0)
    <span class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-red-500 text-white text-[9px] font-black rounded-full flex items-center justify-center border-2 border-white">
        {{ min($totalNotif, 9) }}{{ $totalNotif > 9 ? '+' : '' }}
    </span>
    @endif
</button>

{{-- Dropdown --}}
<div id="notif-dropdown" class="hidden absolute right-0 top-full mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 z-50">
    <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
        <span class="font-semibold text-sm text-gray-900">Notifikasi</span>
        @if($totalNotif > 0)
        <span class="bg-red-100 text-red-600 text-xs font-bold px-2 py-0.5 rounded-full">{{ $totalNotif }} baru</span>
        @endif
    </div>

    @if($pendingBorrowCount > 0)
    <a href="{{ route('admin.transactions.index') }}"
        class="flex items-start gap-3 px-4 py-3 hover:bg-amber-50 transition border-b border-gray-50">
        <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
            <i class="fas fa-book text-amber-600 text-xs"></i>
        </div>
        <div class="flex-1">
            <p class="text-sm font-semibold text-gray-900">{{ $pendingBorrowCount }} permintaan pinjam buku</p>
            <p class="text-xs text-gray-400 mt-0.5">Menunggu persetujuan kamu</p>
        </div>
        <span class="bg-amber-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full self-center">
            {{ $pendingBorrowCount }}
        </span>
    </a>
    @endif

    @if($pendingReturnCount > 0)
    <a href="{{ route('admin.transactions.index', ['tab' => 'return']) }}"
        class="flex items-start gap-3 px-4 py-3 hover:bg-purple-50 transition border-b border-gray-50">
        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
            <i class="fas fa-undo-alt text-purple-600 text-xs"></i>
        </div>
        <div class="flex-1">
            <p class="text-sm font-semibold text-gray-900">{{ $pendingReturnCount }} permintaan pengembalian</p>
            <p class="text-xs text-gray-400 mt-0.5">Menunggu konfirmasi kamu</p>
        </div>
        <span class="bg-purple-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full self-center">
            {{ $pendingReturnCount }}
        </span>
    </a>
    @endif

    @if($pendingMemberCount > 0)
    <a href="{{ route('admin.members.index') }}"
        class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition">
        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
            <i class="fas fa-user-clock text-blue-600 text-xs"></i>
        </div>
        <div class="flex-1">
            <p class="text-sm font-semibold text-gray-900">{{ $pendingMemberCount }} anggota baru</p>
            <p class="text-xs text-gray-400 mt-0.5">Menunggu persetujuan pendaftaran</p>
        </div>
        <span class="bg-blue-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full self-center">
            {{ $pendingMemberCount }}
        </span>
    </a>
    @endif

    @if($totalNotif === 0)
    <div class="px-4 py-8 text-center text-sm text-gray-400">
        <i class="fas fa-check-circle text-3xl mb-2 block text-gray-200"></i>
        Semua sudah beres!
    </div>
    @endif
</div>
</div>


        {{-- Profile Dropdown --}}
        <div class="relative">
            <button onclick="toggleDropdown('profile-dropdown')"
                class="flex items-center gap-2.5 pl-3 pr-2 py-1.5 bg-gray-50 hover:bg-gray-100 rounded-xl border border-gray-200 transition">
                <div
                    class="w-7 h-7 rounded-full bg-gradient-to-br from-evergreen-400 to-evergreen-600 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->full_name ?? 'A', 0, 2)) }}
                </div>
                <div class="hidden sm:block text-left">
                    <div class="text-xs font-semibold text-gray-900 leading-tight">
                        {{ auth()->user()->full_name ?? 'Admin' }}</div>
                    <div class="text-xs text-gray-500 leading-tight">Admin Perpustakaan</div>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-400"></i>
            </button>

            <div id="profile-dropdown"
                class="hidden absolute right-0 top-full mt-2 w-52 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100">
                    <div class="text-sm font-semibold text-gray-900">{{ auth()->user()->full_name }}</div>
                    <div class="text-xs text-gray-500">{{ auth()->user()->school->name ?? '' }}</div>
                </div>
                <a href="{{ route('admin.settings') }}"
                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                    <i class="fas fa-cog w-4 text-gray-400"></i> Pengaturan
                </a>
                <div class="border-t border-gray-100">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                            <i class="fas fa-sign-out-alt w-4"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    // Set date
    const d = new Date();
    const el = document.getElementById('navbar-date');
    if (el) el.textContent = d.toLocaleDateString('id-ID', {
        weekday: 'short',
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });

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
