@extends('layouts.admin')

@section('title', 'Kelola Anggota')
@section('page-title', 'Kelola Anggota')
@section('page-subtitle', 'Daftar lengkap siswa yang terdaftar di sistem Ruang Baca.')

@section('breadcrumb')
    <span class="text-gray-700 font-medium">Kelola Anggota</span>
@endsection

@section('content')

    {{-- ── Header Row ───────────────────────────────────────── --}}
    {{-- <div class="flex items-center justify-between mb-5"> --}}
    {{-- <div>
            <h2 class="text-xl font-bold text-gray-900">Kelola Anggota</h2>
            <p class="text-sm text-gray-500 mt-0.5">Daftar lengkap siswa dan guru yang terdaftar di sistem Ruang Baca.</p>
        </div> --}}
    {{-- <button onclick="openModal('modal-add-member')"
            class="flex items-center gap-2 bg-evergreen-600 hover:bg-evergreen-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition">
            <i class="fas fa-user-plus"></i> Tambah Anggota
        </button> --}}
    {{-- </div> --}}

    {{-- ── Status Tabs ──────────────────────────────────────── --}}
    <div class="flex mb-5 flex-wrap justify-between items-center gap-4">
        {{-- Bungkus menu status dalam satu div agar nempel ke kiri --}}
        <div class="flex gap-2 flex-wrap">
            @php
                $statuses = [
                    '' => ['label' => 'Semua', 'count' => $counts['all'], 'color' => 'gray'],
                    'approved' => ['label' => 'Aktif', 'count' => $counts['approved'], 'color' => 'green'],
                    'pending' => ['label' => 'Menunggu', 'count' => $counts['pending'], 'color' => 'amber'],
                    'rejected' => ['label' => 'Ditolak', 'count' => $counts['rejected'], 'color' => 'red'],
                ];
                $currentStatus = request('status', '');
            @endphp

            @foreach ($statuses as $val => $s)
                <a href="{{ route('admin.members.index', array_merge(request()->except('status', 'page'), $val ? ['status' => $val] : [])) }}"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold border transition
              {{ $currentStatus === $val
                  ? 'bg-gray-900 text-white border-gray-900'
                  : 'bg-white text-gray-600 border-gray-200 hover:border-gray-400' }}">
                    {{ $s['label'] }}
                    <span
                        class="text-xs px-1.5 py-0.5 rounded-full
                    {{ $currentStatus === $val ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500' }}">
                        {{ $s['count'] }}
                    </span>
                </a>
            @endforeach
        </div>

        {{-- Tombol ini otomatis akan terdorong ke paling kanan karena justify-between di parent --}}
        <button onclick="openModal('modal-add-member')"
            class="flex items-center gap-2 bg-evergreen-600 hover:bg-evergreen-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition">
            <i class="fas fa-user-plus"></i> Tambah Anggota
        </button>
    </div>

    {{-- ── Search & Filter ─────────────────────────────────── --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-4 mb-5 shadow-sm">
        <form method="GET" action="{{ route('admin.members.index') }}" class="flex flex-wrap items-center gap-3">
            <input type="hidden" name="status" value="{{ request('status') }}">

            <div class="relative flex-1 min-w-[300px]">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama, username, atau email..."
                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 focus:border-transparent outline-none">
            </div>

            <select name="class_id" onchange="this.form.submit()"
                class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-600 focus:ring-2 focus:ring-evergreen-500 outline-none cursor-pointer min-w-[160px]">
                <option value="">Semua Kelas</option>
                @foreach (\App\Models\ClassModel::where('school_id', auth()->user()->school_id)->get() as $cls)
                    <option value="{{ $cls->id }}" {{ request('class_id') == $cls->id ? 'selected' : '' }}>
                        {{ $cls->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit"
                class="px-5 py-2.5 bg-gray-900 hover:bg-gray-700 text-white text-sm font-semibold rounded-xl transition">
                <i class="fas fa-search mr-1.5"></i> Cari
            </button>
        </form>
    </div>

    {{-- ── Table ─────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr
                        class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                        <th class="px-4 py-3 text-left w-10">No</th>
                        <th class="px-4 py-3 text-left">Nama Lengkap</th>
                        <th class="px-4 py-3 text-left">Username</th>
                        <th class="px-4 py-3 text-left hidden md:table-cell">Email Sekolah</th>
                        <th class="px-4 py-3 text-left">Kelas</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($members as $i => $member)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3.5 text-xs font-bold text-gray-400">
                                {{ $members->firstItem() + $i }}
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                                        style="background: hsl({{ crc32($member->full_name) % 360 }}, 60%, 50%)">
                                        {{ strtoupper(substr($member->full_name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $member->full_name }}</div>
                                        <div class="text-xs text-gray-400">NIS: {{ $member->student_id ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-gray-600 font-mono text-xs">{{ $member->username }}</td>
                            <td class="px-4 py-3.5 text-gray-500 text-xs hidden md:table-cell">{{ $member->email ?? '—' }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="text-xs font-medium text-gray-700">{{ $member->class->name ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                @if ($member->status === 'approved')
                                    <span
                                        class="inline-flex items-center gap-1 bg-evergreen-100 text-evergreen-700 text-xs font-bold px-2.5 py-1 rounded-full">AKTIF</span>
                                @elseif($member->status === 'pending')
                                    <span
                                        class="inline-flex items-center gap-1 bg-amber-100 text-amber-700 text-xs font-bold px-2.5 py-1 rounded-full">MENUNGGU</span>
                                @elseif($member->status === 'rejected')
                                    <span
                                        class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-xs font-bold px-2.5 py-1 rounded-full">DITOLAK</span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 bg-gray-100 text-gray-600 text-xs font-bold px-2.5 py-1 rounded-full">{{ strtoupper($member->status) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-center gap-1">
                                    @if ($member->status === 'pending')
                                        <form method="POST" action="{{ route('admin.members.approve', $member) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" title="Setujui"
                                                class="w-8 h-8 flex items-center justify-center bg-evergreen-50 hover:bg-evergreen-600 text-evergreen-600 hover:text-white rounded-lg transition text-xs">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <button onclick='openEditMember(@json($member))' title="Edit"
                                        class="w-8 h-8 flex items-center justify-center bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white rounded-lg transition text-xs">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <form method="POST" action="{{ route('admin.members.destroy', $member) }}"
                                        onsubmit="return confirm('Hapus anggota {{ $member->full_name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Hapus"
                                            class="w-8 h-8 flex items-center justify-center bg-red-50 hover:bg-red-600 text-red-600 hover:text-white rounded-lg transition text-xs">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center text-gray-400">
                                <i class="fas fa-users text-4xl mb-3 block text-gray-200"></i>
                                <div class="font-medium text-sm">Tidak ada anggota ditemukan</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($members->hasPages() || $members->total() > 0)
            <div
                class="px-6 py-5 border-t border-gray-100 flex items-center justify-between flex-wrap gap-4 bg-white rounded-b-2xl">
                <p class="text-sm text-gray-500">
                    Menampilkan <span class="font-semibold text-gray-900">{{ $members->firstItem() ?? 0 }}</span>
                    hingga <span class="font-semibold text-gray-900">{{ $members->lastItem() ?? 0 }}</span>
                    dari <span class="font-semibold text-gray-900">{{ number_format($members->total()) }}</span> entri
                </p>
                <div class="flex items-center gap-1.5">
                    @if ($members->onFirstPage())
                        <span
                            class="w-9 h-9 flex items-center justify-center text-gray-300 bg-gray-50 rounded-lg cursor-not-allowed">
                            <i class="fas fa-chevron-left text-xs"></i>
                        </span>
                    @else
                        <a href="{{ $members->previousPageUrl() }}"
                            class="w-9 h-9 flex items-center justify-center text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-all">
                            <i class="fas fa-chevron-left text-xs"></i>
                        </a>
                    @endif

                    @foreach ($members->getUrlRange(max(1, $members->currentPage() - 2), min($members->lastPage(), $members->currentPage() + 2)) as $page => $url)
                        @if ($page == $members->currentPage())
                            <span
                                class="w-9 h-9 flex items-center justify-center bg-evergreen-600 text-white rounded-lg text-sm font-bold shadow-md shadow-evergreen-200">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                                class="w-9 h-9 flex items-center justify-center text-gray-600 bg-white border border-gray-200 rounded-lg hover:border-evergreen-400 hover:text-evergreen-600 transition-all text-sm font-medium">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    @if ($members->hasMorePages())
                        <a href="{{ $members->nextPageUrl() }}"
                            class="w-9 h-9 flex items-center justify-center text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-all">
                            <i class="fas fa-chevron-right text-xs"></i>
                        </a>
                    @else
                        <span
                            class="w-9 h-9 flex items-center justify-center text-gray-300 bg-gray-50 rounded-lg cursor-not-allowed">
                            <i class="fas fa-chevron-right text-xs"></i>
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- ── INFO ──────────────────────────────────────────────── --}}
    <div class="mt-4 flex items-start gap-3 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 text-sm text-blue-700">
        <i class="fas fa-info-circle mt-0.5 text-blue-400 flex-shrink-0"></i>
        <p>Akun dengan status <strong>Menunggu</strong> tidak dapat login sampai disetujui oleh admin. Klik tombol
            <strong>✓</strong> untuk menyetujui.
        </p>
    </div>

    {{-- ════════════ MODAL: TAMBAH ANGGOTA ════════════ --}}
    <div id="modal-add-member" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">Tambah Anggota Manual</h3>
                <button onclick="closeModal('modal-add-member')"
                    class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.members.store') }}" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="full_name" required placeholder="Nama lengkap siswa"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Username <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="username" required placeholder="username unik"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">NIS / NISN</label>
                        <input type="text" name="student_id" placeholder="Opsional"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" placeholder="email@sekolah.sch.id"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kelas <span
                            class="text-red-500">*</span></label>
                    <select name="class_id" required
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                        <option value="">Pilih Kelas</option>

                        {{-- Tambahkan filter whereNotIn untuk menyembunyikan template --}}
                        @foreach (\App\Models\ClassModel::where('school_id', auth()->user()->school_id)->whereNotIn('name', ['__level_template__', '__major_template__'])->orderBy('name', 'asc')->get() as $cls)
                            <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password <span
                            class="text-red-500">*</span></label>
                    <input type="password" name="password" required placeholder="Min. 8 karakter"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeModal('modal-add-member')"
                        class="flex-1 py-2.5 border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition text-sm">Batal</button>
                    <button type="submit"
                        class="flex-[2] py-2.5 bg-evergreen-600 hover:bg-evergreen-700 text-white font-bold rounded-xl transition text-sm">Simpan
                        Anggota</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════ MODAL: EDIT ANGGOTA ════════════ --}}
    <div id="modal-edit-member" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">Edit Anggota</h3>
                <button onclick="closeModal('modal-edit-member')"
                    class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" id="form-edit-member" class="px-6 py-5 space-y-4">
                @csrf @method('PATCH')
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="full_name" id="edit-full-name" required
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Username</label>
                        <input type="text" name="username" id="edit-username" required
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">NIS / NISN</label>
                        <input type="text" name="student_id" id="edit-student-id"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" id="edit-email"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kelas</label>
                    <select name="class_id" id="edit-class-id"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                        @foreach (\App\Models\ClassModel::where('school_id', auth()->user()->school_id)->get() as $cls)
                            <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status</label>
                    <select name="status" id="edit-status"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                        <option value="approved">Aktif</option>
                        <option value="pending">Menunggu</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeModal('modal-edit-member')"
                        class="flex-1 py-2.5 border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition text-sm">Batal</button>
                    <button type="submit"
                        class="flex-[2] py-2.5 bg-evergreen-600 hover:bg-evergreen-700 text-white font-bold rounded-xl transition text-sm">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        document.querySelectorAll('[id^="modal-"]').forEach(m => {
            m.addEventListener('click', e => {
                if (e.target === m) m.classList.add('hidden');
            });
        });

        function openEditMember(member) {
            document.getElementById('edit-full-name').value = member.full_name || '';
            document.getElementById('edit-username').value = member.username || '';
            document.getElementById('edit-student-id').value = member.student_id || '';
            document.getElementById('edit-email').value = member.email || '';
            document.getElementById('edit-class-id').value = member.class_id || '';
            document.getElementById('edit-status').value = member.status || 'pending';
            document.getElementById('form-edit-member').action = `/admin/members/${member.id}`;
            openModal('modal-edit-member');
        }
    </script>
@endpush
