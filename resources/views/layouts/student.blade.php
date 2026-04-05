<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Ruang Baca</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
</head>

<body class="bg-gray-50 font-sans antialiased min-h-screen">

    {{-- ═══════════ TOP NAVBAR ═══════════ --}}
    <nav class="sticky top-0 z-50 bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex items-center h-14">

                {{-- Logo --}}
                <a href="{{ route('student.dashboard') }}" class="flex items-center gap-2.5 flex-shrink-0 mr-8">
                    <div class="w-8 h-8 bg-evergreen-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book-open text-white text-sm"></i>
                    </div>
                    <span class="font-bold text-gray-900 text-sm tracking-wide hidden sm:block">RUANG BACA</span>
                </a>

                {{-- Desktop Nav Links --}}
                <div class="hidden md:flex items-stretch h-full flex-1">
                    @php
                        $navLinks = [
                            ['route' => 'student.dashboard', 'match' => 'student.dashboard', 'label' => 'Dashboard'],
                            ['route' => 'student.books.index', 'match' => 'student.books.*', 'label' => 'Koleksi Buku'],
                            ['route' => 'student.history', 'match' => 'student.history', 'label' => 'Riwayat Saya'],
                            ['route' => 'student.profile', 'match' => 'student.profile', 'label' => 'Profile'],
                        ];
                    @endphp
                    @foreach ($navLinks as $link)
                        <a href="{{ route($link['route']) }}"
                            class="relative flex items-center px-4 text-sm font-medium transition-colors
                              {{ request()->routeIs($link['match'])
                                  ? 'text-evergreen-700 font-semibold'
                                  : 'text-gray-500 hover:text-gray-900' }}">
                            {{ $link['label'] }}
                            @if (request()->routeIs($link['match']))
                                <span
                                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-evergreen-600 rounded-full"></span>
                            @endif
                        </a>
                    @endforeach
                </div>

                {{-- Right Side --}}
                <div class="flex items-center gap-2 ml-auto">

                    {{-- Notification Bell --}}
                    {{-- @php
                        $lateCount = \App\Models\Borrowing::where('user_id', auth()->id())->where('status','late')->count();
                        $wlCount   = \App\Models\Wishlist::where('user_id', auth()->id())->count();
                    @endphp

                    <div class="relative hidden sm:block">
                        <button onclick="toggleDropdown('notif-dropdown')"
                            class="w-9 h-9 flex items-center justify-center text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full transition">
                            <i class="fas fa-bell"></i>
                            @if ($lateCount > 0)
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
                            @endif
                        </button>

                        <div id="notif-dropdown"
                             class="hidden absolute right-0 top-full mt-2 w-72 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden">
                            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                                <span class="font-semibold text-sm">Notifikasi</span>
                                @if ($lateCount > 0)
                                <span class="bg-red-100 text-red-600 text-xs font-bold px-2 py-0.5 rounded-full">{{ $lateCount }} terlambat</span>
                                @endif
                            </div>
                            @if ($lateCount > 0)
                            <a href="{{ route('student.history') }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition">
                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i class="fas fa-exclamation-triangle text-red-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $lateCount }} buku terlambat</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Segera kembalikan untuk menghindari denda</p>
                                </div>
                            </a>
                            @else
                            <div class="px-4 py-8 text-center text-sm text-gray-400">
                                <i class="fas fa-bell-slash text-3xl mb-2 block text-gray-200"></i>
                                Tidak ada notifikasi
                            </div>
                            @endif
                        </div>
                    </div> --}}
                    {{-- Ganti isi notif-dropdown di layouts/student.blade.php --}}
                    @php
                        $lateCount = \App\Models\Borrowing::where('user_id', auth()->id())
                            ->where('status', 'late')
                            ->count();
                        $pendingCount = \App\Models\Borrowing::where('user_id', auth()->id())
                            ->where('status', 'pending')
                            ->count();
                        $returnAccCount = \App\Models\Borrowing::where('user_id', auth()->id())
                            ->where('status', 'return_requested')
                            ->count();
                        $wlCount = \App\Models\Wishlist::where('user_id', auth()->id())->count();
                        $rejectedCount = \App\Models\Borrowing::where('user_id', auth()->id())
                            ->where('status', 'rejected')
                            ->where('updated_at', '>=', now()->subDay()) // notif 24 jam terakhir
                            ->count();
                        $approvedCount = \App\Models\Borrowing::where('user_id', auth()->id())
                            ->where('status', 'borrowed')
                            ->where('approved_at', '>=', now()->subDay())
                            ->count();
                        $studentNotifCount =
                            $lateCount + $pendingCount + $returnAccCount + $rejectedCount + $approvedCount;
                    @endphp

                    {{-- Bell button --}}
                    <div class="relative hidden sm:block">
                        <button onclick="toggleDropdown('notif-dropdown')"
                            class="w-9 h-9 flex items-center justify-center text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full transition relative">
                            <i class="fas fa-bell"></i>
                            @if ($studentNotifCount > 0)
                                <span
                                    class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-[8px] font-black rounded-full flex items-center justify-center border border-white">
                                    {{ min($studentNotifCount, 9) }}
                                </span>
                            @endif
                        </button>


                        {{-- Dropdown --}}
                        <div id="notif-dropdown"
                            class="hidden absolute right-0 top-full mt-2 w-72 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden">
                            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                                <span class="font-semibold text-sm">Notifikasi</span>
                                @if ($studentNotifCount > 0)
                                    <span
                                        class="bg-red-100 text-red-600 text-xs font-bold px-2 py-0.5 rounded-full">{{ $studentNotifCount }}</span>
                                @endif
                            </div>

                            @if ($approvedCount > 0)
                                <a href="{{ route('student.history') }}"
                                    class="flex items-start gap-3 px-4 py-3 hover:bg-evergreen-50 transition border-b border-gray-50">
                                    <div
                                        class="w-8 h-8 bg-evergreen-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <i class="fas fa-check-circle text-evergreen-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">Peminjaman disetujui!</p>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $approvedCount }} buku berhasil
                                            disetujui
                                            hari ini</p>
                                    </div>
                                </a>
                            @endif

                            @if ($rejectedCount > 0)
                                <a href="{{ route('student.history') }}"
                                    class="flex items-start gap-3 px-4 py-3 hover:bg-red-50 transition border-b border-gray-50">
                                    <div
                                        class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <i class="fas fa-times-circle text-red-500 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">Permintaan ditolak</p>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $rejectedCount }} permintaan
                                            ditolak,
                                            lihat alasannya</p>
                                    </div>
                                </a>
                            @endif

                            @if ($pendingCount > 0)
                                <a href="{{ route('student.history') }}"
                                    class="flex items-start gap-3 px-4 py-3 hover:bg-amber-50 transition border-b border-gray-50">
                                    <div
                                        class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <i class="fas fa-hourglass-half text-amber-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $pendingCount }} pinjaman
                                            menunggu ACC</p>
                                        <p class="text-xs text-gray-400 mt-0.5">Pustakawan sedang memproses permintaanmu
                                        </p>
                                    </div>
                                </a>
                            @endif

                            @if ($returnAccCount > 0)
                                <a href="{{ route('student.history') }}"
                                    class="flex items-start gap-3 px-4 py-3 hover:bg-purple-50 transition border-b border-gray-50">
                                    <div
                                        class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <i class="fas fa-clock text-purple-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $returnAccCount }}
                                            pengembalian
                                            diproses</p>
                                        <p class="text-xs text-gray-400 mt-0.5">Menunggu konfirmasi pustakawan</p>
                                    </div>
                                </a>
                            @endif

                            @if ($lateCount > 0)
                                <a href="{{ route('student.history') }}"
                                    class="flex items-start gap-3 px-4 py-3 hover:bg-red-50 transition">
                                    <div
                                        class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <i class="fas fa-exclamation-triangle text-red-500 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $lateCount }} buku
                                            terlambat!
                                        </p>
                                        <p class="text-xs text-gray-400 mt-0.5">Segera kembalikan untuk hindari denda
                                        </p>
                                    </div>
                                </a>
                            @endif

                            @if ($studentNotifCount === 0)
                                <div class="px-4 py-8 text-center text-sm text-gray-400">
                                    <i class="fas fa-bell-slash text-3xl mb-2 block text-gray-200"></i>
                                    Tidak ada notifikasi
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Wishlist Heart --}}
                    <a href="{{ route('student.wishlist') }}"
                        class="relative hidden sm:flex w-9 h-9 items-center justify-center text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full transition">
                        <i class="fas fa-heart"></i>
                        @if ($wlCount > 0)
                            <span
                                class="absolute top-0.5 right-0.5 w-4 h-4 bg-red-500 text-white text-center rounded-full font-bold border border-white flex items-center justify-center"
                                style="font-size:9px">
                                {{ min($wlCount, 9) }}{{ $wlCount > 9 ? '+' : '' }}
                            </span>
                        @endif
                    </a>

                    {{-- Divider --}}
                    <div class="hidden sm:block w-px h-5 bg-gray-200 mx-1"></div>

                    {{-- User Info --}}
                    <div class="hidden sm:flex items-center gap-2.5">
                        <div class="text-right">
                            <div class="text-xs font-bold text-gray-900 leading-tight max-w-[120px] truncate">
                                {{ auth()->user()->full_name }}</div>
                            <div class="text-xs text-gray-400 leading-tight">
                                {{ auth()->user()->class->name ?? 'Siswa' }}</div>
                        </div>

                        @if (auth()->user()->photo)
                            <img src="{{ asset('storage/' . auth()->user()->photo) }}"
                                class="w-8 h-8 rounded-full object-cover ring-2 ring-evergreen-100 flex-shrink-0">
                        @else
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                                style="background: linear-gradient(135deg, #22c55e, #15803d)">
                                {{ strtoupper(substr(auth()->user()->full_name ?? 'S', 0, 2)) }}
                            </div>
                        @endif
                    </div>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-bold px-3.5 py-2 rounded-full transition">
                            <i class="fas fa-sign-out-alt text-xs"></i>
                            <span class="hidden sm:inline">Logout</span>
                        </button>
                    </form>

                    {{-- Mobile Hamburger --}}
                    <button onclick="toggleMobileMenu()"
                        class="md:hidden w-9 h-9 flex items-center justify-center text-gray-500 hover:bg-gray-100 rounded-full transition">
                        <i class="fas fa-bars" id="hamburger-icon"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Dropdown Menu --}}
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-1">
            <a href="{{ route('student.dashboard') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('student.dashboard') ? 'bg-evergreen-50 text-evergreen-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fas fa-home w-4 text-center"></i> Dashboard
            </a>
            <a href="{{ route('student.books.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('student.books.*') ? 'bg-evergreen-50 text-evergreen-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fas fa-book w-4 text-center"></i> Koleksi Buku
            </a>
            <a href="{{ route('student.history') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('student.history') ? 'bg-evergreen-50 text-evergreen-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fas fa-history w-4 text-center"></i> Riwayat Saya
            </a>
            <a href="{{ route('student.wishlist') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('student.wishlist') ? 'bg-evergreen-50 text-evergreen-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fas fa-heart w-4 text-center"></i> Wishlist
            </a>
            <a href="{{ route('student.profile') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('student.profile') ? 'bg-evergreen-50 text-evergreen-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fas fa-user w-4 text-center"></i> Profile
            </a>
        </div>
    </nav>

    {{-- ═══════════ CONTENT AREA ═══════════ --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-6">

        @if (session('success'))
            <div
                class="mb-5 flex items-center gap-3 bg-evergreen-50 border border-evergreen-200 text-evergreen-700 rounded-xl px-4 py-3 text-sm">
                <i class="fas fa-check-circle text-evergreen-500 flex-shrink-0"></i>
                {{ session('success') }}
                <button onclick="this.parentElement.remove()"
                    class="ml-auto text-evergreen-400 hover:text-evergreen-600"><i class="fas fa-times"></i></button>
            </div>
        @endif
        @if (session('error'))
            <div
                class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                <i class="fas fa-exclamation-circle text-red-500 flex-shrink-0"></i>
                {{ session('error') }}
                <button onclick="this.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600"><i
                        class="fas fa-times"></i></button>
            </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const icon = document.getElementById('hamburger-icon');
            menu.classList.toggle('hidden');
            icon.classList.toggle('fa-bars');
            icon.classList.toggle('fa-times');
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
