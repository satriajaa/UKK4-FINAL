@extends('layouts.admin')

@section('title', 'Kelola Kelas')
@section('page-title', 'Manajemen Kelas & Jurusan')
@section('page-subtitle', 'Kelola struktur kelas, jurusan, dan tingkatan sekolah Anda.')

@section('breadcrumb')
    <span class="text-gray-700 font-medium">Kelola Kelas</span>
@endsection

@section('content')

    {{-- ── Tabs ───────────────────────────────────────────────── --}}
    <div class="flex gap-1 mb-5 border-b border-gray-200">
        <button onclick="switchTab('tab-classes', this)"
            class="tab-btn px-5 py-2.5 text-sm font-bold border-b-2 border-evergreen-600 text-evergreen-600 -mb-px transition">
            Daftar Kelas
        </button>
        <button onclick="switchTab('tab-majors', this)"
            class="tab-btn px-5 py-2.5 text-sm font-bold border-b-2 border-transparent text-gray-400 -mb-px transition hover:text-gray-700">
            Daftar Jurusan
        </button>
        <button onclick="switchTab('tab-levels', this)"
            class="tab-btn px-5 py-2.5 text-sm font-bold border-b-2 border-transparent text-gray-400 -mb-px transition hover:text-gray-700">
            Daftar Tingkat
        </button>
    </div>

    {{-- ════════════ TAB: KELAS ════════════ --}}
    <div id="tab-classes">

        {{-- Search + Filter + Add --}}
        <div class="flex flex-col sm:flex-row gap-3 mb-5">
            <form method="GET" action="{{ route('admin.classes.index') }}" class="flex gap-3 flex-1 flex-wrap">
                <div class="relative flex-1 min-w-48">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama kelas..."
                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 focus:border-transparent outline-none">
                </div>

                @if(count($availableLevels) > 0)
                <select name="level" onchange="this.form.submit()"
                    class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-600 focus:ring-2 focus:ring-evergreen-500 outline-none cursor-pointer">
                    <option value="">Semua Level</option>
                    @foreach ($availableLevels as $lvl)
                        <option value="{{ $lvl }}" {{ request('level') == $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                    @endforeach
                </select>
                @endif

                @if(count($availableMajors) > 0)
                <select name="major" onchange="this.form.submit()"
                    class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-600 focus:ring-2 focus:ring-evergreen-500 outline-none cursor-pointer">
                    <option value="">Semua Jurusan</option>
                    @foreach ($availableMajors as $maj)
                        <option value="{{ $maj }}" {{ request('major') == $maj ? 'selected' : '' }}>{{ $maj }}</option>
                    @endforeach
                </select>
                @endif

                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <span class="whitespace-nowrap">Tampilkan</span>
                    <select name="per_page" onchange="this.form.submit()"
                        class="px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-600 focus:ring-2 focus:ring-evergreen-500 outline-none cursor-pointer">
                        @foreach ([10, 25, 50, 100] as $n)
                            <option value="{{ $n }}" {{ request('per_page', 10) == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                    <span class="whitespace-nowrap">entri</span>
                </div>
            </form>

            <button onclick="document.getElementById('modal-add-class').classList.remove('hidden')"
                class="flex items-center gap-2 bg-evergreen-600 hover:bg-evergreen-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition whitespace-nowrap">
                <i class="fas fa-plus"></i> Tambah Kelas
            </button>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-5 py-3 text-left w-12">No</th>
                            <th class="px-5 py-3 text-left">Nama Kelas</th>
                            <th class="px-5 py-3 text-left">Level</th>
                            <th class="px-5 py-3 text-left">Jurusan</th>
                            <th class="px-5 py-3 text-left">Jumlah Siswa</th>
                            <th class="px-5 py-3 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($classes as $i => $class)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-5 py-3.5 font-bold text-gray-400 text-xs">
                                    {{ str_pad($classes->firstItem() + $i, 2, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-5 py-3.5 font-semibold text-gray-900">{{ $class->name }}</td>
                                <td class="px-5 py-3.5">
                                    @if($class->level)
                                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-blue-100 text-blue-700">
                                            {{ $class->level }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($class->major)
                                        <span class="bg-evergreen-50 text-evergreen-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                            {{ $class->major }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-1.5">
                                        <i class="fas fa-users text-gray-300 text-xs"></i>
                                        <span class="font-semibold text-gray-900">{{ $class->users_count }}</span>
                                        <span class="text-gray-400 text-xs">siswa</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button onclick='openEditClassModal(@json($class))'
                                            class="w-8 h-8 flex items-center justify-center bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white rounded-lg transition text-sm"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="{{ route('admin.classes.destroy', $class) }}"
                                            onsubmit="return confirm('Hapus kelas \'{{ addslashes($class->name) }}\'?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="w-8 h-8 flex items-center justify-center bg-red-50 hover:bg-red-600 text-red-600 hover:text-white rounded-lg transition text-sm"
                                                title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-16 text-center text-gray-400">
                                    <i class="fas fa-chalkboard text-4xl mb-3 block text-gray-200"></i>
                                    <div class="font-medium">Belum ada kelas</div>
                                    <p class="text-sm mt-1">Tambahkan kelas pertama sekolah Anda</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($classes->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between flex-wrap gap-3">
                    <span class="text-sm text-gray-500">
                        Menampilkan <strong>{{ $classes->firstItem() }}–{{ $classes->lastItem() }}</strong> dari
                        <strong>{{ $classes->total() }}</strong> entri
                    </span>
                    {{ $classes->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- ════════════ TAB: JURUSAN ════════════ --}}
    <div id="tab-majors" class="hidden">
        <div class="flex justify-between gap-3 mb-5">
            <div class="relative flex-1 max-w-xs">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="major-search" placeholder="Cari jurusan..."
                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
            </div>
            <button onclick="document.getElementById('modal-add-major').classList.remove('hidden')"
                class="flex items-center gap-2 bg-evergreen-600 hover:bg-evergreen-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition whitespace-nowrap">
                <i class="fas fa-plus"></i> Tambah Jurusan
            </button>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-5 py-3 text-left w-12">No</th>
                        <th class="px-5 py-3 text-left">Nama Jurusan</th>
                        <th class="px-5 py-3 text-left">Jumlah Kelas</th>
                        <th class="px-5 py-3 text-left">Jumlah Siswa</th>
                        <th class="px-5 py-3 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($availableMajors as $i => $major)
                        <tr class="hover:bg-gray-50 transition major-row">
                            <td class="px-5 py-3.5 text-xs font-bold text-gray-400">
                                {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-gray-900">{{ $major }}</td>
                            <td class="px-5 py-3.5 text-gray-600">
                                <strong>{{ $majorStats[$major]['classes_count'] ?? 0 }}</strong> kelas
                            </td>
                            <td class="px-5 py-3.5 text-gray-600">
                                <strong>{{ $majorStats[$major]['students_count'] ?? 0 }}</strong> siswa
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button onclick='openEditMajorModal("{{ addslashes($major) }}")'
                                        class="w-8 h-8 flex items-center justify-center bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white rounded-lg transition text-sm">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.majors.destroy') }}"
                                        onsubmit="return confirm('Hapus jurusan \'{{ addslashes($major) }}\'? Kelas yang memiliki jurusan ini akan kehilangan datanya.')">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="major" value="{{ $major }}">
                                        <button type="submit"
                                            class="w-8 h-8 flex items-center justify-center bg-red-50 hover:bg-red-600 text-red-600 hover:text-white rounded-lg transition text-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center text-gray-400">
                                <i class="fas fa-sitemap text-4xl mb-3 block text-gray-200"></i>
                                <div class="font-medium">Belum ada jurusan</div>
                                <p class="text-sm mt-1">Tambahkan jurusan, lalu gunakan saat membuat kelas</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ════════════ TAB: LEVEL ════════════ --}}
    <div id="tab-levels" class="hidden">
        <div class="flex justify-between gap-3 mb-5">
            <div class="relative flex-1 max-w-xs">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="level-search" placeholder="Cari level..."
                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
            </div>
            <button onclick="document.getElementById('modal-add-level').classList.remove('hidden')"
                class="flex items-center gap-2 bg-evergreen-600 hover:bg-evergreen-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition whitespace-nowrap">
                <i class="fas fa-plus"></i> Tambah Tingkatan
            </button>
        </div>

        {{-- Info --}}
        {{-- <div class="mb-4 flex items-start gap-2.5 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 text-sm text-blue-700">
            <i class="fas fa-info-circle mt-0.5 flex-shrink-0"></i>
            <span>Level yang sudah ditambahkan di sini akan muncul sebagai pilihan saat membuat kelas. Contoh: SMP bisa buat level <strong>7, 8, 9</strong> — SMA/SMK bisa buat <strong>X, XI, XII</strong>.</span>
        </div> --}}

        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-5 py-3 text-left w-12">No</th>
                        <th class="px-5 py-3 text-left">Nama Level</th>
                        <th class="px-5 py-3 text-left">Jumlah Kelas</th>
                        <th class="px-5 py-3 text-left">Jumlah Siswa</th>
                        <th class="px-5 py-3 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($availableLevels as $i => $level)
                        <tr class="hover:bg-gray-50 transition level-row">
                            <td class="px-5 py-3.5 text-xs font-bold text-gray-400">
                                {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-blue-100 text-blue-700">
                                    {{ $level }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-gray-600">
                                <strong>{{ $levelStats[$level]['classes_count'] ?? 0 }}</strong> kelas
                            </td>
                            <td class="px-5 py-3.5 text-gray-600">
                                <strong>{{ $levelStats[$level]['students_count'] ?? 0 }}</strong> siswa
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button onclick='openEditLevelModal("{{ addslashes($level) }}")'
                                        class="w-8 h-8 flex items-center justify-center bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white rounded-lg transition text-sm">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.levels.destroy') }}"
                                        onsubmit="return confirm('Hapus level \'{{ addslashes($level) }}\'?')">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="level" value="{{ $level }}">
                                        <button type="submit"
                                            class="w-8 h-8 flex items-center justify-center bg-red-50 hover:bg-red-600 text-red-600 hover:text-white rounded-lg transition text-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center text-gray-400">
                                <i class="fas fa-layer-group text-4xl mb-3 block text-gray-200"></i>
                                <div class="font-medium">Belum ada level</div>
                                <p class="text-sm mt-1">Tambahkan level tingkatan kelas sekolah Anda</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ════════════ MODAL: TAMBAH KELAS ════════════ --}}
    <div id="modal-add-class" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white z-10">
                <h3 class="text-lg font-bold text-gray-900">Tambah Kelas Baru</h3>
                <button onclick="document.getElementById('modal-add-class').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.classes.store') }}" class="px-6 py-5 space-y-4">
                @csrf

                {{-- Nama Kelas --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Nama Kelas <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" required placeholder="Contoh: 7A, X RPL 1, XII IPA 2"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    <p class="text-xs text-gray-400 mt-1">Nama bebas sesuai format sekolah Anda</p>
                </div>

                {{-- Level --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Level / Tingkat</label>
                    @if(count($availableLevels) > 0)
                        <select name="level"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                            <option value="">— Tanpa Level —</option>
                            @foreach ($availableLevels as $lvl)
                                <option value="{{ $lvl }}">{{ $lvl }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">
                            Pilih dari level yang sudah dibuat. Belum ada?
                            <button type="button"
                                onclick="document.getElementById('modal-add-class').classList.add('hidden'); switchTab('tab-levels', document.querySelectorAll('.tab-btn')[2]); document.getElementById('modal-add-level').classList.remove('hidden')"
                                class="text-evergreen-600 font-semibold hover:underline">
                                Tambah level dulu →
                            </button>
                        </p>
                    @else
                        <div class="flex items-center gap-2 px-4 py-3 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-700">
                            <i class="fas fa-info-circle flex-shrink-0"></i>
                            Belum ada level.
                            <button type="button"
                                onclick="document.getElementById('modal-add-class').classList.add('hidden'); switchTab('tab-levels', document.querySelectorAll('.tab-btn')[2]); document.getElementById('modal-add-level').classList.remove('hidden')"
                                class="underline font-semibold hover:text-amber-900">
                                Tambah level dulu →
                            </button>
                        </div>
                    @endif
                </div>

                {{-- Jurusan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jurusan</label>
                    @if(count($availableMajors) > 0)
                        <select name="major"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                            <option value="">— Tanpa Jurusan —</option>
                            @foreach ($availableMajors as $maj)
                                <option value="{{ $maj }}">{{ $maj }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">
                            Pilih dari jurusan yang sudah dibuat. Belum ada?
                            <button type="button"
                                onclick="document.getElementById('modal-add-class').classList.add('hidden'); switchTab('tab-majors', document.querySelectorAll('.tab-btn')[1]); document.getElementById('modal-add-major').classList.remove('hidden')"
                                class="text-evergreen-600 font-semibold hover:underline">
                                Tambah jurusan dulu →
                            </button>
                        </p>
                    @else
                        <div class="flex items-center gap-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-500">
                            <i class="fas fa-info-circle flex-shrink-0"></i>
                            Belum ada jurusan.
                            <button type="button"
                                onclick="document.getElementById('modal-add-class').classList.add('hidden'); switchTab('tab-majors', document.querySelectorAll('.tab-btn')[1]); document.getElementById('modal-add-major').classList.remove('hidden')"
                                class="underline font-semibold hover:text-gray-700">
                                Tambah jurusan dulu →
                            </button>
                        </div>
                    @endif
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-add-class').classList.add('hidden')"
                        class="flex-1 py-2.5 border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition text-sm">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-[2] py-2.5 bg-evergreen-600 hover:bg-evergreen-700 text-white font-bold rounded-xl transition text-sm">
                        Simpan Kelas
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════ MODAL: EDIT KELAS ════════════ --}}
    <div id="modal-edit-class" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white z-10">
                <h3 class="text-lg font-bold text-gray-900">Edit Data Kelas</h3>
                <button onclick="document.getElementById('modal-edit-class').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="form-edit-class" method="POST" class="px-6 py-5 space-y-4">
                @csrf @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Nama Kelas <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="edit-class-name" required
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Level / Tingkat</label>
                    <select name="level" id="edit-class-level"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                        <option value="">— Tanpa Level —</option>
                        @foreach ($availableLevels as $lvl)
                            <option value="{{ $lvl }}">{{ $lvl }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jurusan</label>
                    <select name="major" id="edit-class-major"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                        <option value="">— Tanpa Jurusan —</option>
                        @foreach ($availableMajors as $maj)
                            <option value="{{ $maj }}">{{ $maj }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-edit-class').classList.add('hidden')"
                        class="flex-1 py-2.5 border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 text-sm">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-[2] py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-sm">
                        Update Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════ MODAL: TAMBAH JURUSAN ════════════ --}}
    <div id="modal-add-major" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">Tambah Jurusan Baru</h3>
                <button onclick="document.getElementById('modal-add-major').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.majors.store') }}" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Nama Jurusan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" required placeholder="Contoh: Rekayasa Perangkat Lunak"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    <p class="text-xs text-gray-400 mt-1">Nama bebas, misal: IPA, IPS, Multimedia, Akuntansi</p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-add-major').classList.add('hidden')"
                        class="flex-1 py-2.5 border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition text-sm">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-[2] py-2.5 bg-evergreen-600 hover:bg-evergreen-700 text-white font-bold rounded-xl transition text-sm">
                        Simpan Jurusan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════ MODAL: EDIT JURUSAN ════════════ --}}
    <div id="modal-edit-major" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">Edit Jurusan</h3>
                <button onclick="document.getElementById('modal-edit-major').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="form-edit-major" method="POST" action="{{ route('admin.majors.store') }}" class="px-6 py-5 space-y-4">
                @csrf
                {{-- Karena major hanya string, "edit" = buat entry baru + hapus lama via hidden field --}}
                <input type="hidden" name="old_major" id="edit-major-old">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Nama Jurusan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="edit-major-name" required
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-edit-major').classList.add('hidden')"
                        class="flex-1 py-2.5 border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 text-sm">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-[2] py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-sm">
                        Update Jurusan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════ MODAL: TAMBAH LEVEL ════════════ --}}
    <div id="modal-add-level" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">Tambah Level Baru</h3>
                <button onclick="document.getElementById('modal-add-level').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.levels.store') }}" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Nama Level <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" required placeholder="Contoh: 7, 8, 9 atau X, XI, XII"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    <p class="text-xs text-gray-400 mt-1">Bisa angka, huruf, atau kombinasi. Sesuaikan dengan jenjang sekolah.</p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-add-level').classList.add('hidden')"
                        class="flex-1 py-2.5 border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition text-sm">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-[2] py-2.5 bg-evergreen-600 hover:bg-evergreen-700 text-white font-bold rounded-xl transition text-sm">
                        Simpan Level
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════ MODAL: EDIT LEVEL ════════════ --}}
    <div id="modal-edit-level" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">Edit Level</h3>
                <button onclick="document.getElementById('modal-edit-level').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="form-edit-level" method="POST" action="{{ route('admin.levels.store') }}" class="px-6 py-5 space-y-4">
                @csrf
                <input type="hidden" name="old_level" id="edit-level-old">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Nama Level <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="edit-level-name" required
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-edit-level').classList.add('hidden')"
                        class="flex-1 py-2.5 border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 text-sm">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-[2] py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-sm">
                        Update Level
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Restore active tab dari session ────────────────────────
    @if(session('last_tab'))
        const activeTabId = "{{ session('last_tab') }}";
        const tabButton = document.querySelector(`button[onclick*='${activeTabId}']`);
        if (tabButton) switchTab(activeTabId, tabButton);
    @endif

    // ── Close modals on backdrop click ─────────────────────────
    ['modal-add-class','modal-edit-class','modal-add-major','modal-edit-major','modal-add-level','modal-edit-level'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('click', function (e) {
            if (e.target === this) this.classList.add('hidden');
        });
    });

    // ── Major search ────────────────────────────────────────────
    const majorSearch = document.getElementById('major-search');
    if (majorSearch) {
        majorSearch.addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.major-row').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }

    // ── Level search ────────────────────────────────────────────
    const levelSearch = document.getElementById('level-search');
    if (levelSearch) {
        levelSearch.addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.level-row').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }
});

// ── Tab Switching ───────────────────────────────────────────
function switchTab(id, btn) {
    ['tab-classes', 'tab-majors', 'tab-levels'].forEach(t => document.getElementById(t).classList.add('hidden'));
    document.getElementById(id).classList.remove('hidden');
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('border-evergreen-600', 'text-evergreen-600');
        b.classList.add('border-transparent', 'text-gray-400');
    });
    btn.classList.add('border-evergreen-600', 'text-evergreen-600');
    btn.classList.remove('border-transparent', 'text-gray-400');
}

// ── Buka Modal Edit Kelas ───────────────────────────────────
function openEditClassModal(classData) {
    const form = document.getElementById('form-edit-class');
    form.action = `/admin/classes/${classData.id}`;
    document.getElementById('edit-class-name').value  = classData.name  ?? '';
    document.getElementById('edit-class-level').value = classData.level ?? '';
    document.getElementById('edit-class-major').value = classData.major ?? '';
    document.getElementById('modal-edit-class').classList.remove('hidden');
}

// ── Buka Modal Edit Jurusan ─────────────────────────────────
function openEditMajorModal(major) {
    document.getElementById('edit-major-old').value  = major;
    document.getElementById('edit-major-name').value = major;
    document.getElementById('modal-edit-major').classList.remove('hidden');
}

// ── Buka Modal Edit Level ───────────────────────────────────
function openEditLevelModal(level) {
    document.getElementById('edit-level-old').value  = level;
    document.getElementById('edit-level-name').value = level;
    document.getElementById('modal-edit-level').classList.remove('hidden');
}
</script>
@endpush
