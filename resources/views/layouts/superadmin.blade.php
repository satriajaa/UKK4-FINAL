<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Ruang Baca Superadmin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
</head>
<body class="bg-slate-50 font-sans antialiased">

    <div class="flex h-screen overflow-hidden">

        {{-- ===== SIDEBAR ===== --}}
        @include('layouts.partials.sidebar-superadmin')

        {{-- ===== MAIN AREA ===== --}}
        <div class="flex flex-col flex-1 overflow-hidden" id="main-content">

            {{-- ===== NAVBAR ===== --}}
            @include('layouts.partials.navbar-superadmin')

            {{-- ===== PAGE CONTENT ===== --}}
            <main class="flex-1 overflow-y-auto bg-slate-50 p-6">
                @hasSection('breadcrumb')
                <nav class="mb-4 flex items-center gap-2 text-sm text-gray-500">
                    <a href="{{ route('superadmin.dashboard') }}" class="hover:text-indigo-600 transition">Dashboard</a>
                    <i class="fas fa-chevron-right text-xs text-gray-300"></i>
                    @yield('breadcrumb')
                </nav>
                @endif

                @if(session('success'))
                <div class="mb-4 flex items-center gap-3 bg-indigo-50 border border-indigo-200 text-indigo-700 rounded-xl px-4 py-3 text-sm">
                    <i class="fas fa-check-circle text-indigo-500"></i>
                    {{ session('success') }}
                    <button onclick="this.parentElement.remove()" class="ml-auto text-indigo-400 hover:text-indigo-600"><i class="fas fa-times"></i></button>
                </div>
                @endif
                @if(session('error'))
                <div class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                    {{ session('error') }}
                    <button onclick="this.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600"><i class="fas fa-times"></i></button>
                </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden" onclick="toggleSidebar()"></div>

    @stack('scripts')
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('superadmin-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>
</body>
</html>
