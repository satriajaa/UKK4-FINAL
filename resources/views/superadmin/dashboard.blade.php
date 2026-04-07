@extends('layouts.superadmin')

@section('title', 'Dashboard')

@push('styles')
    <style>
        /* Sisa modal overlay biar blur kayak di history */
        .modal-overlay {
            backdrop-filter: blur(4px);
        }
    </style>
@endpush

@section('content')

    {{-- ── PAGE HEADER ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-th-large text-evergreen-600"></i> Dashboard Superadmin
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Mengelola seluruh sistem perpustakaan sekolah &mdash;
                <span class="font-semibold text-gray-700">{{ now()->format('d M Y, H:i') }}</span>
            </p>
        </div>
        <a href="{{ route('superadmin.schools.create') }}"
            class="inline-flex items-center gap-2 bg-evergreen-600 hover:bg-evergreen-700 text-white text-sm font-bold px-5 py-2.5 rounded-full transition shadow-sm">
            <i class="fas fa-plus text-xs"></i> Tambah Sekolah Baru
        </a>
    </div>

    {{-- ── STAT CARDS ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Card 1: Sekolah --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between mb-3">
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Sekolah</div>
                <div class="w-8 h-8 bg-blue-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-school text-blue-500 text-sm"></i>
                </div>
            </div>
            <div class="text-4xl font-black text-gray-900 leading-none mb-1">{{ $totalSchools }}</div>
            <div class="text-xs font-medium mt-2 flex items-center gap-1">
                <span class="text-evergreen-600 font-bold">{{ $activeSchools }} aktif</span>
                @if ($inactiveSchools > 0)
                    <span class="text-gray-300">•</span>
                    <span class="text-red-500 font-bold">{{ $inactiveSchools }} nonaktif</span>
                @endif
            </div>
        </div>

        {{-- Card 2: Buku --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between mb-3">
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Buku</div>
                <div class="w-8 h-8 bg-purple-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-book text-purple-500 text-sm"></i>
                </div>
            </div>
            <div class="text-4xl font-black text-gray-900 leading-none mb-1">{{ number_format($totalBooks) }}</div>
            <div class="text-xs text-gray-400 font-medium mt-2">
                <span class="font-bold text-gray-600">+{{ $newBooksThisMonth }}</span> buku bulan ini
            </div>
        </div>

        {{-- Card 3: Siswa --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between mb-3">
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Siswa</div>
                <div class="w-8 h-8 bg-amber-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-amber-500 text-sm"></i>
                </div>
            </div>
            <div class="text-4xl font-black text-gray-900 leading-none mb-1">{{ number_format($totalStudents) }}</div>
            <div class="text-xs font-medium mt-2">
                @if ($pendingMembers > 0)
                    <span class="text-amber-500 font-bold"><i class="fas fa-clock text-[10px]"></i> {{ $pendingMembers }} menunggu</span>
                @else
                    <span class="text-gray-400"><span class="font-bold text-gray-600">+{{ $newStudentsThisMonth }}</span> bulan ini</span>
                @endif
            </div>
        </div>

        {{-- Card 4: Transaksi Aktif --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between mb-3">
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Pinjam Aktif</div>
                <div class="w-8 h-8 {{ $lateBorrows > 0 ? 'bg-red-50' : 'bg-orange-50' }} rounded-xl flex items-center justify-center">
                    <i class="fas fa-exchange-alt {{ $lateBorrows > 0 ? 'text-red-500' : 'text-orange-500' }} text-sm"></i>
                </div>
            </div>
            <div class="text-4xl font-black {{ $lateBorrows > 0 ? 'text-red-600' : 'text-gray-900' }} leading-none mb-1">{{ $activeBorrows }}</div>
            <div class="text-xs font-medium mt-2">
                @if ($lateBorrows > 0)
                    <span class="text-red-500 font-bold"><i class="fas fa-exclamation-triangle text-[10px]"></i> {{ $lateBorrows }} terlambat</span>
                @else
                    <span class="text-evergreen-600 font-bold">Semua tepat waktu</span>
                @endif
            </div>
        </div>
    </div>

    {{-- ── MANAGE SCHOOLS FILTER ── --}}
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-bold text-gray-900 flex items-center gap-2">
            <i class="fas fa-building text-evergreen-500 text-sm"></i> Kelola Sekolah
            <span class="bg-gray-100 text-gray-600 text-xs font-bold px-2 py-0.5 rounded-full ml-1">{{ $schools->count() }}</span>
        </h2>
    </div>

    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400 text-sm"></i>
            </div>
            <input type="text" id="schoolSearch" oninput="filterSchools()" placeholder="Cari nama sekolah atau NPSN..."
                class="w-full pl-11 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-evergreen-500/20 focus:border-evergreen-500 transition">
        </div>
        <select id="statusFilter" onchange="filterSchools()"
            class="w-full sm:w-48 px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-evergreen-500/20 focus:border-evergreen-500 transition cursor-pointer text-gray-700">
            <option value="">Semua Status</option>
            <option value="active">Aktif</option>
            <option value="inactive">Nonaktif</option>
        </select>
    </div>

    {{-- ── SCHOOL GRID ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="schoolGrid">
        @forelse($schools as $school)
            <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md hover:border-gray-200 transition cursor-pointer group school-card"
                data-id="{{ $school->id }}" data-name="{{ strtolower($school->name) }}" data-npsn="{{ $school->npsn }}" data-status="{{ $school->status }}">

                <div class="flex items-start gap-4 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center flex-shrink-0 border border-gray-100 group-hover:bg-evergreen-50 transition">
                        <i class="fas fa-school text-gray-400 group-hover:text-evergreen-600 text-lg transition"></i>
                    </div>
                    <div class="flex-1 min-w-0 pt-0.5">
                        <h3 class="text-sm font-bold text-gray-900 truncate group-hover:text-evergreen-700 transition">{{ $school->name }}</h3>
                        <p class="text-xs text-gray-400 mt-0.5">NPSN: {{ $school->npsn }}</p>
                        @if ($school->status === 'active')
                            <span class="inline-flex items-center gap-1.5 mt-2 bg-evergreen-50 border border-evergreen-100 text-evergreen-700 text-[10px] font-bold px-2 py-0.5 rounded-md tracking-wide">
                                <span class="w-1.5 h-1.5 bg-evergreen-500 rounded-full"></span> AKTIF
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 mt-2 bg-red-50 border border-red-100 text-red-700 text-[10px] font-bold px-2 py-0.5 rounded-md tracking-wide">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span> NONAKTIF
                            </span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2 mb-4">
                    <div class="bg-gray-50 rounded-xl py-2 px-1 text-center border border-gray-100/50">
                        <span class="block text-sm font-black text-gray-900">{{ number_format($school->books_count) }}</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-0.5 block">Buku</span>
                    </div>
                    <div class="bg-gray-50 rounded-xl py-2 px-1 text-center border border-gray-100/50">
                        <span class="block text-sm font-black text-gray-900">{{ number_format($school->students_count) }}</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-0.5 block">Siswa</span>
                    </div>
                    <div class="bg-gray-50 rounded-xl py-2 px-1 text-center border border-gray-100/50">
                        <span class="block text-sm font-black text-gray-900">{{ $school->admins_count }}</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-0.5 block">Admin</span>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                    <div class="text-[11px] text-gray-500">
                        Denda: <span class="font-bold {{ ($school->total_fine ?? 0) > 0 ? 'text-red-600' : 'text-gray-900' }}">Rp {{ number_format($school->total_fine ?? 0, 0, ',', '.') }}</span>
                        <span class="mx-1 text-gray-300">•</span>
                        Trx: <span class="font-bold text-gray-900">{{ $school->active_borrows_count }}</span>
                    </div>
                    <button onclick="openDetail({{ $school->id }})" class="text-[11px] font-bold text-evergreen-600 hover:text-evergreen-700 bg-evergreen-50/50 hover:bg-evergreen-50 px-3 py-1.5 rounded-lg border border-evergreen-100/50 transition">Detail →</button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-gray-100">
                <i class="fas fa-school text-5xl text-gray-200 mb-3 block"></i>
                <p class="text-gray-500 font-medium text-sm">Belum ada sekolah terdaftar.</p>
            </div>
        @endforelse
    </div>

    {{-- ── DETAIL MODAL ── --}}
    <div id="detailModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 modal-overlay p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[90vh]">

            {{-- Header --}}
            <div class="bg-gradient-to-r from-evergreen-800 to-evergreen-900 px-6 py-5 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center border border-white/10">
                        <i class="fas fa-building text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white leading-tight" id="dp-name">—</h2>
                        <p class="text-xs text-evergreen-200 mt-0.5" id="dp-npsn">—</p>
                    </div>
                </div>
                <button onclick="closeDetail()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition border border-white/10">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-6 overflow-y-auto flex-1 bg-gray-50/30">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6">

                    {{-- Info Column --}}
                    <div class="md:col-span-2 space-y-5">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 flex items-center gap-2">
                            <i class="fas fa-info-circle text-gray-300"></i> Info Utama
                        </div>

                        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm space-y-4">
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Nama Lengkap Sekolah</p>
                                <p class="text-sm font-bold text-gray-900 mt-0.5" id="dp-full-name">—</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">NPSN</p>
                                <p class="text-sm font-semibold text-gray-900 font-mono mt-0.5" id="dp-npsn-val">—</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Alamat Lengkap</p>
                                <p class="text-sm font-medium text-gray-700 mt-0.5 leading-relaxed" id="dp-addr">—</p>
                            </div>
                            <div class="grid grid-cols-2 gap-3 pt-3 border-t border-gray-50">
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Kontak</p>
                                    <p class="text-sm font-medium text-gray-900 mt-0.5" id="dp-contact">—</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Email</p>
                                    <p class="text-xs font-medium text-gray-900 mt-0.5 truncate" id="dp-email">—</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Stats & Admin Column --}}
                    <div class="md:col-span-3 space-y-5">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 flex items-center gap-2">
                            <i class="fas fa-chart-pie text-gray-300"></i> Statistik & Pengelola
                        </div>

                        {{-- Stats Grid --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div class="bg-white border border-gray-100 rounded-xl p-3 text-center shadow-sm">
                                <span class="block text-xl font-black text-gray-900" id="dp-books">—</span>
                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mt-1 block">Total Buku</span>
                            </div>
                            <div class="bg-white border border-gray-100 rounded-xl p-3 text-center shadow-sm">
                                <span class="block text-xl font-black text-gray-900" id="dp-students">—</span>
                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mt-1 block">Siswa Aktif</span>
                            </div>
                            <div class="bg-white border border-gray-100 rounded-xl p-3 text-center shadow-sm">
                                <span class="block text-xl font-black text-gray-900" id="dp-trx">—</span>
                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mt-1 block">Transaksi</span>
                            </div>
                            <div class="bg-red-50 border border-red-100 rounded-xl p-3 text-center shadow-sm">
                                <span class="block text-xl font-black text-red-600" id="dp-fine">—</span>
                                <span class="text-[9px] font-bold text-red-400 uppercase tracking-wider mt-1 block">Total Denda</span>
                            </div>
                        </div>

                        {{-- Admin List --}}
                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                            <div class="px-4 py-3 bg-gray-50/50 border-b border-gray-100 flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-600">Daftar Admin</span>
                                <span class="text-[10px] font-bold bg-evergreen-100 text-evergreen-700 px-2 py-0.5 rounded-full" id="dp-admins-count">0</span>
                            </div>
                            <div class="p-2 space-y-2 max-h-[160px] overflow-y-auto" id="dp-admin-list">
                                {{-- JS Injected Admins --}}
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Footer / Actions --}}
            <div class="px-6 py-4 bg-white border-t border-gray-100 flex items-center justify-end gap-3 flex-shrink-0">
                <a href="#" id="dp-edit-link" class="px-5 py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 text-sm font-bold rounded-xl transition flex items-center gap-2">
                    <i class="fas fa-edit text-xs"></i> Edit Data
                </a>
                <a href="#" id="dp-delete-link" class="px-5 py-2.5 bg-red-50 hover:bg-red-100 border border-red-100 text-red-600 text-sm font-bold rounded-xl transition flex items-center gap-2">
                    <i class="fas fa-trash text-xs"></i> Hapus Sekolah
                </a>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    // Data sekolah dari controller (tetap sama logic-nya)
    const schoolsData = @json($schools->keyBy('id'));

    // Fitur Filter Search & Status
    function filterSchools() {
        const q = document.getElementById('schoolSearch').value.toLowerCase();
        const st = document.getElementById('statusFilter').value;
        document.querySelectorAll('#schoolGrid .school-card').forEach(card => {
            const nameMatch = card.dataset.name.includes(q) || card.dataset.npsn.includes(q);
            const statusMatch = !st || card.dataset.status === st;
            card.style.display = (nameMatch && statusMatch) ? '' : 'none';
        });
    }

    // Buka Modal Detail
    function openDetail(id) {
        const s = schoolsData[id];
        if (!s) return;

        document.getElementById('dp-name').textContent = s.name;
        document.getElementById('dp-npsn').textContent = 'NPSN: ' + s.npsn;
        document.getElementById('dp-full-name').textContent = s.name;
        document.getElementById('dp-npsn-val').textContent = s.npsn;
        document.getElementById('dp-addr').textContent = s.address || 'Belum diatur';
        document.getElementById('dp-contact').textContent = s.phone || '—';
        document.getElementById('dp-email').textContent = s.email || '—';

        document.getElementById('dp-books').textContent = (s.books_count || 0).toLocaleString('id-ID');
        document.getElementById('dp-students').textContent = (s.students_count || 0).toLocaleString('id-ID');
        document.getElementById('dp-trx').textContent = s.active_borrows_count || 0;

        const denda = s.total_fine || 0;
        document.getElementById('dp-fine').textContent = (denda > 0 ? 'Rp ' : '') + denda.toLocaleString('id-ID');

        // Link actions
        document.getElementById('dp-edit-link').href = `/superadmin/schools/${id}/edit`;
        document.getElementById('dp-delete-link').setAttribute('data-id', id);

        // Render Admin list
        const adminListEl = document.getElementById('dp-admin-list');
        document.getElementById('dp-admins-count').textContent = s.admins_count || 0;

        if (s.admins && s.admins.length > 0) {
            adminListEl.innerHTML = s.admins.map((a, i) => `
                <div class="flex items-center gap-3 p-2 rounded-lg bg-white border border-gray-50 hover:bg-gray-50 transition">
                    <div class="w-8 h-8 rounded-full bg-evergreen-50 border border-evergreen-100 flex items-center justify-center text-xs font-bold text-evergreen-600 flex-shrink-0">
                        ${i + 1}
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-gray-900 truncate">${a.full_name}</div>
                        <div class="text-[10px] text-gray-500 truncate">@${a.username} · ${a.email || '-'}</div>
                    </div>
                </div>
            `).join('');
        } else {
            adminListEl.innerHTML = `
                <div class="text-center py-6">
                    <i class="fas fa-users text-gray-200 text-2xl mb-2"></i>
                    <p class="text-xs font-medium text-gray-400">Data admin belum tersedia</p>
                </div>`;
        }

        // Tampilkan Modal
        document.getElementById('detailModal').classList.remove('hidden');
        document.getElementById('detailModal').classList.add('flex');
        document.body.style.overflow = 'hidden'; // Kunci scroll di background
    }

    // Tutup Modal Detail
    function closeDetail() {
        document.getElementById('detailModal').classList.add('hidden');
        document.getElementById('detailModal').classList.remove('flex');
        document.body.style.overflow = ''; // Balikin scroll
    }

    // Close Modal via klik overlay hitam
    document.getElementById('detailModal').addEventListener('click', function(e) {
        if (e.target === this) closeDetail();
    });

    // Form Delete (Hapus Sekolah)
    document.getElementById('dp-delete-link').addEventListener('click', function(e) {
        e.preventDefault();
        if (!confirm('Yakin ingin menghapus sekolah ini? Aksi ini akan menghapus semua data terkait!')) return;

        const id = this.dataset.id;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/superadmin/schools/${id}`;
        form.innerHTML = `
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        document.body.appendChild(form);
        form.submit();
    });
</script>
@endpush
