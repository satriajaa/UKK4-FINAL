@extends('layouts.superadmin')

@section('title', 'Detail Sekolah')

@section('topbar-nav')
    <span class="text-xs text-gray-300 mx-1">/</span>
    <a href="{{ route('superadmin.dashboard') }}" class="text-xs text-gray-500 hover:text-evergreen-600 font-medium px-2 py-1 transition">Dashboard</a>
    <span class="text-xs text-gray-300 mx-1">/</span>
    <span class="text-xs text-evergreen-700 font-bold px-3 py-1.5 bg-evergreen-50 rounded-lg truncate max-w-[200px] inline-block align-bottom">
        {{ $school->name }}
    </span>
@endsection

@section('content')
<div class="max-w-6xl mx-auto pb-8">

    {{-- ── HEADER SEKOLAH ── --}}
    <div class="bg-white border border-gray-100 rounded-2xl p-6 mb-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-evergreen-50 border border-evergreen-100 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">
                🏫
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $school->name }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">NPSN: <span class="font-mono font-semibold">{{ $school->npsn }}</span></p>
            </div>
        </div>
        <div class="flex gap-2">
            {{-- <a href="{{ route('superadmin.schools.edit', $school->id) }}" class="px-4 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 text-sm font-bold rounded-xl transition flex items-center gap-2">
                <i class="fas fa-edit text-xs"></i> Edit
            </a> --}}
        </div>
    </div>

    {{-- ── STATISTIK UTAMA ── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3 mb-6">
        <div class="bg-white border border-gray-100 rounded-xl p-4 text-center shadow-sm">
            <span class="block text-2xl font-black text-gray-900">{{ number_format($school->students_count) }}</span>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-1 block">Siswa Aktif</span>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 text-center shadow-sm">
            <span class="block text-2xl font-black text-gray-900">{{ number_format($school->books_count) }}</span>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-1 block">Total Buku</span>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 text-center shadow-sm">
            <span class="block text-2xl font-black text-gray-900">{{ number_format($school->active_borrows_count) }}</span>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-1 block">Trx Pinjam</span>
        </div>
        <div class="bg-red-50 border border-red-100 rounded-xl p-4 text-center shadow-sm md:col-span-2">
            <span class="block text-2xl font-black text-red-600">Rp {{ number_format($school->total_fine ?? 0, 0, ',', '.') }}</span>
            <span class="text-[10px] font-bold text-red-400 uppercase tracking-wider mt-1 block">Total Denda Terkumpul</span>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 text-center shadow-sm">
            <span class="block text-2xl font-black text-gray-900">{{ $school->classes_count }}</span>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-1 block">Kelas</span>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 text-center shadow-sm">
            <span class="block text-2xl font-black text-gray-900">{{ $majorsCount }}</span>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-1 block">Jurusan</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── KOLOM KIRI: DAFTAR ADMIN ── --}}
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h2 class="text-xs font-bold text-gray-900 tracking-wider flex items-center gap-2">
                        <i class="fas fa-user-shield text-blue-500"></i> ADMIN SEKOLAH
                    </h2>
                    <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $admins->count() }}</span>
                </div>
                <div class="p-4 space-y-3">
                    @forelse($admins as $i => $admin)
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-white border border-gray-100 shadow-sm hover:border-blue-200 transition">
                            <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                {{ strtoupper(substr($admin->full_name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-gray-900 truncate">{{ $admin->full_name }}</p>
                                <p class="text-[11px] text-gray-500 truncate mt-0.5">{{ $admin->username }} &bull; {{ $admin->email ?? 'Tanpa Email' }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-center text-gray-400 py-4">Belum ada admin yang terdaftar.</p>
                    @endforelse
                </div>
            </div>

            {{-- Info Tambahan --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h2 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3">Kontak & Alamat</h2>
                <div class="space-y-3 text-sm">
                    <p><i class="fas fa-envelope text-gray-400 w-5"></i> {{ $school->email }}</p>
                    <p><i class="fas fa-phone text-gray-400 w-5"></i> {{ $school->phone ?? 'Belum diatur' }}</p>
                    <p class="flex items-start"><i class="fas fa-map-marker-alt text-gray-400 w-5 mt-1"></i> <span class="flex-1">{{ $school->address }}</span></p>
                </div>
            </div>
        </div>

        {{-- ── KOLOM KANAN: DAFTAR SISWA PER KELAS ── --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden h-full flex flex-col">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 sm:flex sm:items-center justify-between gap-4">
                    <h2 class="text-xs font-bold text-gray-900 tracking-wider flex items-center gap-2 mb-3 sm:mb-0">
                        <i class="fas fa-users text-evergreen-500"></i> DAFTAR SISWA
                    </h2>
                    <select id="classSelector" onchange="showClassStudents(this.value)" class="w-full sm:w-64 bg-white border border-gray-200 text-sm rounded-xl px-3 py-2 focus:ring-2 focus:ring-evergreen-500/20 focus:border-evergreen-500 outline-none transition">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classes as $class)
                            @if($class->name !== '__level_template__' && $class->name !== '__major_template__')
                                <option value="{{ $class->id }}">{{ $class->name }} ({{ $class->users->count() }} Siswa)</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="p-5 flex-1 bg-gray-50/30">
                    {{-- Placeholder saat belum milih kelas --}}
                    <div id="empty-state" class="h-full flex flex-col items-center justify-center py-12 text-center">
                        <i class="fas fa-chalkboard-teacher text-5xl text-gray-200 mb-3 block"></i>
                        <p class="text-gray-500 font-medium text-sm">Pilih kelas di atas untuk melihat daftar siswa.</p>
                    </div>

                    {{-- Data siswa per kelas (Di-hide otomatis pakai CSS) --}}
                    @foreach($classes as $class)
                        @if($class->name !== '__level_template__' && $class->name !== '__major_template__')
                            <div id="class-{{ $class->id }}" class="class-list hidden space-y-2">
                                @forelse($class->users as $student)
                                    <div class="flex items-center justify-between p-3 rounded-xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500">
                                                {{ $loop->iteration }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-900">{{ $student->full_name }}</p>
                                                <p class="text-[11px] text-gray-500">NIS/Username: {{ $student->username }}</p>
                                            </div>
                                        </div>
                                        <span class="text-[10px] font-bold bg-evergreen-50 text-evergreen-600 px-2 py-1 rounded-md">Aktif</span>
                                    </div>
                                @empty
                                    <div class="text-center py-8 bg-white rounded-xl border border-gray-100">
                                        <p class="text-xs text-gray-400 font-medium">Belum ada siswa di kelas ini.</p>
                                    </div>
                                @endforelse
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // Logika buat nampilin siswa sesuai kelas yang dipilih di Dropdown
    function showClassStudents(classId) {
        // Hide semua div kelas
        document.querySelectorAll('.class-list').forEach(el => el.classList.add('hidden'));

        const emptyState = document.getElementById('empty-state');

        if (classId) {
            emptyState.classList.add('hidden'); // Sembunyiin gambar placeholder
            document.getElementById('class-' + classId).classList.remove('hidden'); // Tunjukin list siswanya
        } else {
            emptyState.classList.remove('hidden'); // Kalau milih "-- Pilih Kelas --", munculin placeholder lagi
        }
    }
</script>
@endpush
