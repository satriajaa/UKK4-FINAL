{{-- NAVBAR SUPERADMIN --}}
<header class="flex items-center justify-between h-16 px-6 bg-white border-b border-gray-200 flex-shrink-0">

    <div class="flex items-center gap-4">
        <button onclick="toggleSidebar()" class="lg:hidden p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition">
            <i class="fas fa-bars text-lg"></i>
        </button>
        <div>
            <h1 class="text-lg font-bold text-gray-900 leading-tight">@yield('page-title', 'Dashboard')</h1>
            <p class="text-xs text-gray-500 leading-tight hidden sm:block">@yield('page-subtitle', 'Panel kontrol sistem Ruang Baca')</p>
        </div>
    </div>

    <div class="flex items-center gap-3">
        {{-- System Status Badge --}}
        <div class="hidden md:flex items-center gap-1.5 bg-indigo-50 text-indigo-700 text-xs font-semibold px-3 py-1.5 rounded-lg border border-indigo-100">
            <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-pulse"></span>
            Sistem Aktif
        </div>

        {{-- Total Schools --}}
        <div class="hidden lg:flex items-center gap-2 text-sm text-gray-500">
            <i class="fas fa-school text-gray-400"></i>
            <span>{{ \App\Models\School::where('status','active')->count() }} Sekolah Aktif</span>
        </div>

        {{-- Notification --}}
        <button class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
            <i class="fas fa-bell text-lg"></i>
        </button>

        {{-- Profile --}}
        <div class="relative">
            <button onclick="toggleDropdown('sa-profile-dropdown')" class="flex items-center gap-2.5 pl-3 pr-2 py-1.5 bg-gray-50 hover:bg-gray-100 rounded-xl border border-gray-200 transition">
                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                    SA
                </div>
                <div class="hidden sm:block text-left">
                    <div class="text-xs font-semibold text-gray-900 leading-tight">{{ auth()->user()->full_name ?? 'Super Admin' }}</div>
                    <div class="text-xs text-indigo-600 leading-tight font-medium">Super Admin</div>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-400"></i>
            </button>

            <div id="sa-profile-dropdown" class="hidden absolute right-0 top-full mt-2 w-52 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden">
                <a href="{{ route('superadmin.settings') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                    <i class="fas fa-cog w-4 text-gray-400"></i> Pengaturan
                </a>
                <div class="border-t border-gray-100">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                            <i class="fas fa-sign-out-alt w-4"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    function toggleDropdown(id) {
        document.querySelectorAll('[id$="-dropdown"]').forEach(el => { if(el.id !== id) el.classList.add('hidden'); });
        document.getElementById(id).classList.toggle('hidden');
    }
    document.addEventListener('click', function(e) {
        if(!e.target.closest('[onclick*="toggleDropdown"]') && !e.target.closest('[id$="-dropdown"]')) {
            document.querySelectorAll('[id$="-dropdown"]').forEach(el => el.classList.add('hidden'));
        }
    });
</script>
