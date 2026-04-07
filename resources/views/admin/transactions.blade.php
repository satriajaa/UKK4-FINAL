{{-- resources/views/admin/transactions.blade.php --}}
@extends('layouts.admin')

@section('title', 'Kelola Transaksi')
@section('page-title', 'Kelola Transaksi')
@section('page-subtitle', 'Manajemen peminjaman dan pengembalian buku perpustakaan.')

@section('breadcrumb')
    <span class="text-gray-700 font-medium">Transaksi</span>
@endsection

@push('styles')
    <style>
        .tab-nav-btn {
            transition: all .15s;
        }

        .tab-nav-btn.active {
            background: #166534;
            color: #fff;
        }

        .badge-pulse {
            animation: pulse 2s infinite;
        }
    </style>
@endpush

@section('content')

    {{-- ── Tab Navigation ─────────────────────────────────────────── --}}
    <div class="flex items-center gap-1 mb-5 bg-white border border-gray-200 rounded-2xl p-1.5 w-fit">
        <button onclick="switchAdminTab('pending-borrow')" id="nav-pending-borrow"
            class="tab-nav-btn active relative flex items-center gap-2 text-xs font-bold px-4 py-2.5 rounded-xl">
            <i class="fas fa-clock text-[10px]"></i> Menunggu Acc Pinjam
            @if ($pendingBorrowCount > 0)
                <span
                    class="inline-flex items-center justify-center w-5 h-5 bg-amber-500 text-white text-[9px] font-black rounded-full badge-pulse">
                    {{ $pendingBorrowCount }}
                </span>
            @endif
        </button>
        <button onclick="switchAdminTab('pending-return')" id="nav-pending-return"
            class="tab-nav-btn relative flex items-center gap-2 text-xs font-bold px-4 py-2.5 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-gray-100">
            <i class="fas fa-undo-alt text-[10px]"></i> Menunggu Acc Kembali
            @if ($pendingReturnCount > 0)
                <span
                    class="inline-flex items-center justify-center w-5 h-5 bg-red-500 text-white text-[9px] font-black rounded-full badge-pulse">
                    {{ $pendingReturnCount }}
                </span>
            @endif
        </button>
        <button onclick="switchAdminTab('all')" id="nav-all"
            class="tab-nav-btn flex items-center gap-2 text-xs font-bold px-4 py-2.5 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-gray-100">
            <i class="fas fa-list text-[10px]"></i> Semua Transaksi
        </button>
    </div>

    {{-- ── Stats Cards ─────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-5">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Total Transaksi</div>
            <div class="flex items-end justify-between">
                <div class="text-3xl font-black text-gray-900">{{ number_format($totalTransactions) }}</div>
                <div class="w-8 h-8 flex items-center justify-center bg-gray-100 rounded-lg mb-0.5">
                    <i class="fas fa-list text-gray-500 text-sm"></i>
                </div>
            </div>
        </div>
        <div class="bg-amber-50 rounded-xl border border-amber-200 p-4">
            <div class="text-[10px] font-bold text-amber-600 uppercase tracking-widest mb-2">Menunggu Acc</div>
            <div class="flex items-end justify-between">
                <div class="text-3xl font-black text-amber-700">{{ $pendingBorrowCount + $pendingReturnCount }}</div>
                <div class="w-8 h-8 flex items-center justify-center bg-amber-100 rounded-lg mb-0.5">
                    <i class="fas fa-hourglass-half text-amber-500 text-sm"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Sedang Dipinjam</div>
            <div class="flex items-end justify-between">
                <div class="text-3xl font-black text-blue-600">
                    {{ \App\Models\Borrowing::where('school_id', auth()->user()->school_id)->where('status', 'borrowed')->count() }}
                </div>
                <div class="w-8 h-8 flex items-center justify-center bg-blue-50 rounded-lg mb-0.5">
                    <i class="fas fa-book-open text-blue-500 text-sm"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Total Terlambat</div>
            <div class="flex items-end justify-between">
                <div class="text-3xl font-black text-red-500">{{ number_format($unpaidFine) }}</div>
                <div class="w-8 h-8 flex items-center justify-center bg-red-50 rounded-lg mb-0.5">
                    <i class="fas fa-clock text-red-400 text-sm"></i>
                </div>
            </div>
        </div>
        <div class="bg-evergreen-50 rounded-xl border border-evergreen-200 p-4">
            <div class="text-[10px] font-bold text-evergreen-600 uppercase tracking-widest mb-2">Total Denda</div>
            <div class="flex items-end justify-between">
                <div>
                    <div class="text-xs font-bold text-evergreen-500">Rp</div>
                    <div class="text-2xl font-black text-evergreen-800">
                        {{ $totalFine >= 1000000 ? number_format($totalFine / 1000, 0, ',', '.') . 'k' : number_format($totalFine, 0, ',', '.') }}
                    </div>
                </div>
                <div class="w-8 h-8 flex items-center justify-center bg-evergreen-200 rounded-lg mb-0.5">
                    <i class="fas fa-coins text-evergreen-700 text-sm"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════
     TAB 1: MENUNGGU ACC PINJAM
════════════════════════════════════════════════════════ --}}
    <div id="tab-pending-borrow">
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900 text-sm">Permintaan Peminjaman</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Siswa yang mengajukan permintaan pinjam buku</p>
                </div>
                @if ($pendingBorrowCount > 0)
                    <span class="bg-amber-100 text-amber-700 text-xs font-bold px-3 py-1.5 rounded-full">
                        {{ $pendingBorrowCount }} menunggu
                    </span>
                @endif
            </div>

            @if ($pendingBorrows->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr
                                class="border-b border-gray-100 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                <th class="px-5 py-3.5 text-left">Siswa</th>
                                <th class="px-5 py-3.5 text-left">Buku</th>
                                <th class="px-5 py-3.5 text-left">Tgl Request</th>
                                <th class="px-5 py-3.5 text-left">Estimasi Kembali</th>
                                <th class="px-5 py-3.5 text-left">Stok</th>
                                <th class="px-5 py-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendingBorrows as $trx)
                                <tr class="border-b border-gray-50 hover:bg-amber-50/30 transition">
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-gray-900 text-sm">{{ $trx->user->full_name ?? '—' }}
                                        </div>
                                        <div class="text-xs text-gray-400">{{ $trx->user->class->name ?? '—' }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-10 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                                @if ($trx->book->cover ?? null)
                                                    <img src="{{ asset('storage/' . $trx->book->cover) }}"
                                                        class="w-full h-full object-cover">
                                                @else
                                                    <div
                                                        class="w-full h-full bg-gradient-to-b from-teal-600 to-teal-800 flex items-center justify-center">
                                                        <i class="fas fa-book text-white text-xs"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div
                                                    class="font-semibold text-gray-900 text-sm leading-tight max-w-[180px]">
                                                    {{ $trx->book->title ?? '—' }}</div>
                                                <div class="text-xs text-gray-400">{{ $trx->book->author ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-600 whitespace-nowrap">
                                        {{ $trx->created_at->format('d M Y') }}
                                        <div class="text-xs text-gray-400">{{ $trx->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-600 whitespace-nowrap">
                                        {{ $trx->due_date?->format('d M Y') }}
                                    </td>
                                    <td class="px-5 py-4">
                                        @if ($trx->book->stock > 0)
                                            <span
                                                class="bg-evergreen-100 text-evergreen-700 text-xs font-bold px-2.5 py-1 rounded-full">
                                                {{ $trx->book->stock }} eks
                                            </span>
                                        @else
                                            <span
                                                class="bg-red-100 text-red-600 text-xs font-bold px-2.5 py-1 rounded-full">Habis</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            {{-- Approve --}}
                                            <form method="POST"
                                                action="{{ route('admin.transactions.approve-borrow', $trx) }}"
                                                onsubmit="return confirm('Setujui peminjaman buku \'{{ addslashes($trx->book->title ?? '') }}\' oleh {{ addslashes($trx->user->full_name ?? '') }}?')">
                                                @csrf
                                                <button type="submit"
                                                    class="flex items-center gap-1.5 bg-evergreen-600 hover:bg-evergreen-700 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg transition whitespace-nowrap"
                                                    {{ $trx->book->stock <= 0 ? 'disabled' : '' }}>
                                                    <i class="fas fa-check text-[9px]"></i> Setujui
                                                </button>
                                            </form>

                                            {{-- Reject --}}
                                            <button
                                                onclick="openRejectModal({{ $trx->id }}, '{{ addslashes($trx->book->title ?? '') }}', '{{ addslashes($trx->user->full_name ?? '') }}')"
                                                class="flex items-center gap-1.5 bg-red-50 hover:bg-red-100 text-red-600 text-[11px] font-bold px-3 py-1.5 rounded-lg transition whitespace-nowrap border border-red-100">
                                                <i class="fas fa-times text-[9px]"></i> Tolak
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-20 text-center">
                    <i class="fas fa-check-circle text-5xl text-evergreen-200 mb-3 block"></i>
                    <p class="text-gray-500 font-medium">Tidak ada permintaan pinjam yang menunggu</p>
                    <p class="text-xs text-gray-400 mt-1">Semua permintaan sudah diproses</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════
     TAB 2: MENUNGGU ACC KEMBALI
════════════════════════════════════════════════════════ --}}
    <div id="tab-pending-return" class="hidden">
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900 text-sm">Permintaan Pengembalian</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Siswa yang mengajukan permintaan kembalikan buku</p>
                </div>
                @if ($pendingReturnCount > 0)
                    <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1.5 rounded-full">
                        {{ $pendingReturnCount }} menunggu
                    </span>
                @endif
            </div>

            @if ($returnRequests->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr
                                class="border-b border-gray-100 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                <th class="px-5 py-3.5 text-left">Siswa</th>
                                <th class="px-5 py-3.5 text-left">Buku</th>
                                <th class="px-5 py-3.5 text-left">Tgl Pinjam</th>
                                <th class="px-5 py-3.5 text-left">Jatuh Tempo</th>
                                <th class="px-5 py-3.5 text-left">Req. Kembali</th>
                                <th class="px-5 py-3.5 text-left">Denda Est.</th>
                                <th class="px-5 py-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($returnRequests as $trx)
                                @php
                                    $isLate = now()->isAfter($trx->due_date);
                                    $daysLate = $isLate ? (int) ceil(now()->diffInDays($trx->due_date)) : 0; // ✅ pembulatan ke atas
                                    $estFine = $daysLate * ($setting->fine_per_day ?? 1000);
                                @endphp
                                <tr class="border-b border-gray-50 hover:bg-blue-50/20 transition">
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-gray-900 text-sm">
                                            {{ $trx->user->full_name ?? '—' }}</div>
                                        <div class="text-xs text-gray-400">{{ $trx->user->class->name ?? '—' }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-10 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                                @if ($trx->book->cover ?? null)
                                                    <img src="{{ asset('storage/' . $trx->book->cover) }}"
                                                        class="w-full h-full object-cover">
                                                @else
                                                    <div
                                                        class="w-full h-full bg-gradient-to-b from-gray-400 to-gray-600 flex items-center justify-center">
                                                        <i class="fas fa-book text-white text-xs"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div
                                                    class="font-semibold text-gray-900 text-sm leading-tight max-w-[160px]">
                                                    {{ $trx->book->title ?? '—' }}</div>
                                                <div class="text-xs text-gray-400">{{ $trx->transaction_code }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-600 whitespace-nowrap">
                                        {{ $trx->borrow_date?->format('d M Y') }}
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="text-sm {{ $isLate ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                                            {{ $trx->due_date?->format('d M Y') }}
                                        </div>
                                        @if ($isLate)
                                            <div class="text-xs text-red-500">{{ $daysLate }} hari terlambat</div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-600 whitespace-nowrap">
                                        {{ $trx->return_requested_at?->format('d M Y H:i') ?? '—' }}
                                        <div class="text-xs text-gray-400">
                                            {{ $trx->return_requested_at?->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-5 py-4">
    @php
        // Hitung estimasi denda real-time pakai function di Model
        $estFine = $trx->calculateFine($finePerDay);
    @endphp

    @if($estFine > 0)
        <span class="text-sm font-bold text-red-600">
            Rp {{ number_format($estFine, 0, ',', '.') }}
        </span>
    @else
        <span class="text-sm font-semibold text-evergreen-600">
            Tidak ada
        </span>
    @endif
</td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center">
                                            <form method="POST"
                                                action="{{ route('admin.transactions.approve-return', $trx) }}"
                                                onsubmit="return confirm('Konfirmasi pengembalian buku \'{{ addslashes($trx->book->title ?? '') }}\'?')">
                                                @csrf
                                                <button type="submit"
                                                    class="flex items-center gap-1.5 bg-evergreen-600 hover:bg-evergreen-700 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg transition whitespace-nowrap">
                                                    <i class="fas fa-check-double text-[9px]"></i> Konfirmasi Terima
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-20 text-center">
                    <i class="fas fa-inbox text-5xl text-gray-200 mb-3 block"></i>
                    <p class="text-gray-500 font-medium">Tidak ada permintaan pengembalian</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════
     TAB 3: SEMUA TRANSAKSI (existing table)
════════════════════════════════════════════════════════ --}}
    <div id="tab-all" class="hidden">

        {{-- Filter Bar --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-5">
            <form method="GET" action="{{ route('admin.transactions.index') }}" id="filter-form">
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                    <div class="sm:col-span-1">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Periode
                            Tanggal</label>
                        <div class="flex items-center gap-1.5">
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                class="flex-1 min-w-0 px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs outline-none focus:ring-2 focus:ring-evergreen-500">
                            <span class="text-gray-400 text-xs flex-shrink-0">s/d</span>
                            <input type="date" name="date_to" value="{{ request('date_to') }}"
                                class="flex-1 min-w-0 px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs outline-none focus:ring-2 focus:ring-evergreen-500">
                        </div>
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Status</label>
                        <select name="status"
                            class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs outline-none focus:ring-2 focus:ring-evergreen-500 cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                                Menunggu Acc</option>
                            <option value="borrowed" {{ request('status') === 'borrowed' ? 'selected' : '' }}>
                                Dipinjam</option>
                            <option value="return_requested"
                                {{ request('status') === 'return_requested' ? 'selected' : '' }}>Minta Kembali</option>
                            <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>
                                Dikembalikan</option>
                            <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>
                                Terlambat</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>
                                Ditolak</option>
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Kelas</label>
                        <select name="class_id"
                            class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs outline-none focus:ring-2 focus:ring-evergreen-500 cursor-pointer">
                            <option value="">Semua Kelas</option>
                            @foreach (\App\Models\ClassModel::where('school_id', auth()->user()->school_id)->orderBy('name')->get() as $cls)
                                <option value="{{ $cls->id }}"
                                    {{ request('class_id') == $cls->id ? 'selected' : '' }}>{{ $cls->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" onclick="switchAdminTab('all')"
                            class="flex items-center gap-2 bg-evergreen-700 hover:bg-evergreen-800 text-white text-xs font-bold px-5 py-2 rounded-lg transition w-full justify-center">
                            <i class="fas fa-filter text-[10px]"></i> Tampilkan
                        </button>
                        @if (request()->hasAny(['status', 'date_from', 'date_to', 'class_id']))
                            <a href="{{ route('admin.transactions.index') }}"
                                class="flex items-center justify-center px-3 py-2 bg-white border border-gray-200 text-gray-500 text-xs rounded-lg hover:bg-gray-50 transition flex-shrink-0">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-2 mb-5 flex-wrap">
            <button onclick="document.getElementById('modal-new-borrow').classList.remove('hidden')"
                class="flex items-center gap-2 bg-evergreen-700 hover:bg-evergreen-800 text-white text-xs font-bold px-4 py-2.5 rounded-lg transition">
                <i class="fas fa-plus text-[10px]"></i> Peminjaman Baru
            </button>
            <div class="flex-1"></div>
            {{-- <a href="{{ route('admin.transactions.index', array_merge(request()->query(), ['export' => 'excel'])) }}"
                class="flex items-center gap-2 bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 text-xs font-bold px-4 py-2.5 rounded-lg transition">
                <i class="fas fa-file-excel text-green-600 text-[10px]"></i> Export Excel
            </a>
            <button onclick="window.print()"
                class="flex items-center gap-2 bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 text-xs font-bold px-4 py-2.5 rounded-lg transition">
                <i class="fas fa-print text-[10px]"></i> Cetak A4
            </button> --}}
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th
                                class="px-5 py-3.5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest w-10">
                                No</th>
                            <th
                                class="px-5 py-3.5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                Nama Siswa</th>
                            <th
                                class="px-5 py-3.5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                Judul Buku</th>
                            <th
                                class="px-5 py-3.5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                Tgl Pinjam</th>
                            <th
                                class="px-5 py-3.5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                Tgl Kembali</th>
                            <th
                                class="px-5 py-3.5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                Status</th>
                            <th
                                class="px-5 py-3.5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                Denda</th>
                            <th
                                class="px-5 py-3.5 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $i => $trx)
                            <tr class="border-b border-gray-50 hover:bg-gray-50/60 transition-colors">
                                <td class="px-5 py-4 text-xs text-gray-400 font-medium">
                                    {{ $transactions->firstItem() + $i }}</td>
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-gray-900 text-sm">{{ $trx->user->full_name ?? '—' }}
                                    </div>
                                    <div class="text-[11px] text-gray-400">{{ $trx->user->class->name ?? '—' }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-medium text-gray-800 text-sm max-w-[200px]">
                                        {{ $trx->book->title ?? '—' }}
                                        @if ($trx->book?->author)
                                            <span class="text-gray-400">- {{ $trx->book->author }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="text-sm text-gray-700 whitespace-nowrap">
                                        {{ $trx->borrow_date?->format('d M') ?? '—' }}</div>
                                    <div class="text-[11px] text-gray-400">{{ $trx->borrow_date?->format('Y') }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div
                                        class="text-sm whitespace-nowrap {{ $trx->status === 'late' ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                                        {{ $trx->due_date?->format('d M') ?? '—' }}
                                    </div>
                                    <div class="text-[11px] text-gray-400">{{ $trx->due_date?->format('Y') }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    @php
                                        $badgeMap = [
                                            'pending' => ['bg-amber-100 text-amber-700', 'Menunggu Acc'],
                                            'borrowed' => ['bg-blue-100 text-blue-700', 'Dipinjam'],
                                            'return_requested' => ['bg-purple-100 text-purple-700', 'Minta Kembali'],
                                            'returned' => ['bg-evergreen-100 text-evergreen-700', 'Dikembalikan'],
                                            'late' => ['bg-red-100 text-red-600', 'Terlambat'],
                                            'rejected' => ['bg-gray-100 text-gray-500', 'Ditolak'],
                                        ];
                                        [$cls, $lbl] = $badgeMap[$trx->status] ?? [
                                            'bg-gray-100 text-gray-400',
                                            $trx->status,
                                        ];
                                    @endphp
                                    <span
                                        class="inline-flex items-center {{ $cls }} text-[11px] font-semibold px-3 py-1 rounded-full">
                                        {{ $lbl }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    @if ($trx->fine > 0)
                                        <div class="text-red-600 font-bold text-sm">Rp
                                            {{ number_format($trx->fine, 0, ',', '.') }}</div>
                                    @else
                                        <span class="text-gray-300 text-sm">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="showDetail({{ $trx->id }})"
                                            class="w-7 h-7 flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-500 rounded-lg transition text-xs">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if ($trx->status === 'pending')
                                            <button onclick="switchAdminTab('pending-borrow')"
                                                class="text-[11px] font-bold px-3 py-1 bg-amber-100 text-amber-700 rounded-lg hover:bg-amber-200 transition whitespace-nowrap">
                                                Lihat Request
                                            </button>
                                        @elseif($trx->status === 'return_requested')
                                            <button onclick="switchAdminTab('pending-return')"
                                                class="text-[11px] font-bold px-3 py-1 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition whitespace-nowrap">
                                                Lihat Request
                                            </button>
                                        @elseif(in_array($trx->status, ['borrowed', 'late']))
                                            <span
                                                class="px-3 py-1 text-[11px] font-bold text-blue-600 bg-blue-50 rounded-lg">Aktif</span>
                                        @else
                                            <span
                                                class="px-3 py-1 text-[11px] font-bold text-gray-300 bg-gray-50 rounded-lg">Selesai</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-20 text-center">
                                    <i class="fas fa-exchange-alt text-4xl text-gray-200 mb-3 block"></i>
                                    <p class="text-gray-400 text-sm font-semibold">Belum ada transaksi</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($transactions->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between flex-wrap gap-3">
                    <span class="text-xs text-gray-400">
                        Menampilkan <strong
                            class="text-gray-600">{{ $transactions->firstItem() }}-{{ $transactions->lastItem() }}</strong>
                        dari <strong class="text-gray-600">{{ $transactions->total() }}</strong> transaksi
                    </span>
                    <div class="flex items-center gap-1">
                        @if ($transactions->onFirstPage())
                            <span class="w-7 h-7 flex items-center justify-center text-gray-300 text-sm">‹</span>
                        @else
                            <a href="{{ $transactions->previousPageUrl() }}"
                                class="w-7 h-7 flex items-center justify-center text-gray-500 hover:bg-gray-100 rounded-lg text-sm transition">‹</a>
                        @endif
                        @foreach ($transactions->getUrlRange(1, $transactions->lastPage()) as $page => $url)
                            @if ($page == $transactions->currentPage())
                                <span
                                    class="w-7 h-7 flex items-center justify-center bg-evergreen-700 text-white rounded-lg text-xs font-bold">{{ $page }}</span>
                            @elseif(abs($page - $transactions->currentPage()) <= 2 || $page == 1 || $page == $transactions->lastPage())
                                <a href="{{ $url }}"
                                    class="w-7 h-7 flex items-center justify-center text-gray-500 hover:bg-gray-100 rounded-lg text-xs transition">{{ $page }}</a>
                            @elseif(abs($page - $transactions->currentPage()) == 3)
                                <span class="text-gray-300 text-xs px-1">...</span>
                            @endif
                        @endforeach
                        @if ($transactions->hasMorePages())
                            <a href="{{ $transactions->nextPageUrl() }}"
                                class="w-7 h-7 flex items-center justify-center text-gray-500 hover:bg-gray-100 rounded-lg text-sm transition">›</a>
                        @else
                            <span class="w-7 h-7 flex items-center justify-center text-gray-300 text-sm">›</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════
     MODAL: REJECT REASON
════════════════════════════════════════════════════════ --}}
    <div id="modal-reject" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Tolak Permintaan</h3>
                <button onclick="closeRejectModal()"
                    class="w-8 h-8 flex items-center justify-center text-gray-400 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <form id="reject-form" method="POST" action="" class="px-6 py-5">
                @csrf
                <p class="text-sm text-gray-500 mb-1">Tolak permintaan peminjaman:</p>
                <p class="text-sm font-bold text-gray-900 mb-4" id="reject-info">—</p>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Alasan
                        Penolakan</label>
                    <textarea name="reason" rows="3" placeholder="Contoh: Stok tidak tersedia, buku sedang dalam perbaikan, dll."
                        class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-400 outline-none resize-none"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeRejectModal()"
                        class="flex-1 py-2.5 border border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition text-sm">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition text-sm">
                        Tolak Permintaan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: DETAIL --}}
    <div id="modal-detail" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Detail Transaksi</h3>
                <button onclick="document.getElementById('modal-detail').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center text-gray-400 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <div class="px-6 py-5" id="detail-content">
                <div class="text-center py-8 text-gray-300"><i class="fas fa-spinner fa-spin text-2xl"></i></div>
            </div>
        </div>
    </div>

    {{-- MODAL: PEMINJAMAN BARU --}}
    <div id="modal-new-borrow" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Catat Peminjaman Baru</h3>
                <button onclick="document.getElementById('modal-new-borrow').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center text-gray-400 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.transactions.store') }}" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Anggota (Siswa)
                        <span class="text-red-500">*</span></label>
                    <select name="user_id" required
                        class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                        <option value="">Pilih Anggota</option>
                        @foreach (\App\Models\User::where('school_id', auth()->user()->school_id)->where('role', 'student')->where('status', 'approved')->orderBy('full_name')->get() as $u)
                            <option value="{{ $u->id }}">{{ $u->full_name }} ({{ $u->class->name ?? '—' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Buku <span
                            class="text-red-500">*</span></label>
                    <select name="book_id" required
                        class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                        <option value="">Pilih Buku</option>
                        @foreach (\App\Models\Book::where('school_id', auth()->user()->school_id)->where('stock', '>', 0)->orderBy('title')->get() as $b)
                            <option value="{{ $b->id }}">{{ $b->title }} (Stok: {{ $b->stock }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Tgl
                            Pinjam</label>
                        <input type="date" name="borrow_date" value="{{ date('Y-m-d') }}" required
                            class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Tgl
                            Kembali</label>
                        <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+14 days')) }}"
                            required
                            class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Catatan</label>
                    <textarea name="notes" rows="2" placeholder="Opsional..."
                        class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none resize-none"></textarea>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="document.getElementById('modal-new-borrow').classList.add('hidden')"
                        class="flex-1 py-2.5 border border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition text-sm">Batal</button>
                    <button type="submit"
                        class="flex-[2] py-2.5 bg-evergreen-700 hover:bg-evergreen-800 text-white font-bold rounded-xl transition text-sm">Simpan
                        Transaksi</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // ── Tab Switching ──────────────────────────────────────────────────────
        const TABS = ['pending-borrow', 'pending-return', 'all'];

        function switchAdminTab(active) {
            TABS.forEach(t => {
                document.getElementById(`tab-${t}`)?.classList.toggle('hidden', t !== active);
                const nav = document.getElementById(`nav-${t}`);
                if (nav) {
                    nav.classList.toggle('active', t === active);
                    if (t !== active) {
                        nav.classList.remove('active');
                        nav.classList.add('text-gray-600', 'hover:text-gray-900', 'hover:bg-gray-100');
                    } else {
                        nav.classList.remove('text-gray-600', 'hover:text-gray-900', 'hover:bg-gray-100');
                    }
                }
            });
        }

        // Auto-switch ke tab pending kalau ada yang nunggu
        window.addEventListener('DOMContentLoaded', () => {
            @if (request('tab') === 'return')
                switchAdminTab('pending-return');
            @elseif (request('tab') === 'all')
                switchAdminTab('all');
            @else
                switchAdminTab('pending-borrow');
            @endif
        });

        // ── Reject Modal ──────────────────────────────────────────────────────
        function openRejectModal(id, bookTitle, studentName) {
            document.getElementById('reject-info').textContent = `"${bookTitle}" oleh ${studentName}`;
            document.getElementById('reject-form').action = `/admin/transactions/${id}/reject-borrow`;
            document.getElementById('modal-reject').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('modal-reject').classList.add('hidden');
        }

        // ── Detail Modal ──────────────────────────────────────────────────────
        async function showDetail(id) {
            document.getElementById('modal-detail').classList.remove('hidden');
            document.getElementById('detail-content').innerHTML =
                '<div class="text-center py-8 text-gray-300"><i class="fas fa-spinner fa-spin text-2xl"></i></div>';
            try {
                const res = await fetch(`/admin/transactions/${id}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();

                const statusMap = {
                    pending: '<span class="bg-amber-100 text-amber-700 text-[11px] font-bold px-2.5 py-1 rounded-full">Menunggu Acc</span>',
                    borrowed: '<span class="bg-blue-100 text-blue-700 text-[11px] font-bold px-2.5 py-1 rounded-full">Dipinjam</span>',
                    return_requested: '<span class="bg-purple-100 text-purple-700 text-[11px] font-bold px-2.5 py-1 rounded-full">Minta Kembali</span>',
                    returned: '<span class="bg-evergreen-100 text-evergreen-700 text-[11px] font-bold px-2.5 py-1 rounded-full">Dikembalikan</span>',
                    late: '<span class="bg-red-100 text-red-600 text-[11px] font-bold px-2.5 py-1 rounded-full">Terlambat</span>',
                    rejected: '<span class="bg-gray-100 text-gray-500 text-[11px] font-bold px-2.5 py-1 rounded-full">Ditolak</span>',
                };

                const row = (label, value) => `
            <div class="flex justify-between items-center py-3 border-b border-gray-50 last:border-0">
                <span class="text-xs text-gray-400 font-medium">${label}</span>
                <span class="text-sm font-semibold text-gray-800 text-right max-w-[60%]">${value ?? '—'}</span>
            </div>`;

                document.getElementById('detail-content').innerHTML = `<div>
            ${data.transaction_code ? row('Kode', `<span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded">${data.transaction_code}</span>`) : ''}
            ${row('Siswa', data.user?.full_name)}
            ${row('Kelas', data.user?.class?.name)}
            ${row('Buku', data.book?.title)}
            ${row('Penulis', data.book?.author)}
            ${row('Tgl Pinjam', data.borrow_date)}
            ${row('Jatuh Tempo', data.due_date)}
            ${row('Tgl Kembali', data.return_date)}
            ${data.approver ? row('Diproses oleh', data.approver.full_name) : ''}
            <div class="flex justify-between items-center py-3 border-b border-gray-50">
                <span class="text-xs text-gray-400 font-medium">Status</span>
                ${statusMap[data.status] ?? data.status}
            </div>
            <div class="flex justify-between items-center py-3">
                <span class="text-xs text-gray-400 font-medium">Denda</span>
                <span class="text-sm font-bold ${data.fine > 0 ? 'text-red-600' : 'text-gray-300'}">
                    ${data.fine > 0 ? 'Rp ' + Number(data.fine).toLocaleString('id-ID') : '—'}
                </span>
            </div>
            ${data.rejection_reason ? `<div class="mt-2 bg-red-50 rounded-xl p-3 text-xs text-red-600"><strong>Alasan Penolakan:</strong> ${data.rejection_reason}</div>` : ''}
            ${data.notes ? `<div class="mt-2 bg-gray-50 rounded-xl p-3 text-xs text-gray-600"><strong>Catatan:</strong> ${data.notes}</div>` : ''}
        </div>`;
            } catch {
                document.getElementById('detail-content').innerHTML =
                    '<div class="text-center py-8 text-red-400 text-sm"><i class="fas fa-exclamation-circle text-2xl mb-2 block"></i>Gagal memuat data.</div>';
            }
        }

        // Close modals on backdrop
        document.querySelectorAll('[id^="modal-"]').forEach(m => {
            m.addEventListener('click', e => {
                if (e.target === m) m.classList.add('hidden');
            });
        });
    </script>
@endpush
