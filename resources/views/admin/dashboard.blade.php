@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan aktivitas perpustakaan ' . auth()->user()->school->name ?? '')

@section('content')
{{-- ── Stats Grid ─────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-evergreen-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-books text-evergreen-600"></i>
            </div>
            <span class="text-xs font-semibold text-evergreen-600 bg-evergreen-50 px-2 py-0.5 rounded-full">+2%</span>
        </div>
        <div class="text-3xl font-bold text-gray-900 font-mono">{{ number_format($totalBooks) }}</div>
        <div class="text-sm text-gray-500 mt-0.5">Total Buku</div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-check-circle text-blue-600"></i>
            </div>
            <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">+5%</span>
        </div>
        <div class="text-3xl font-bold text-gray-900 font-mono">{{ number_format($availableBooks) }}</div>
        <div class="text-sm text-gray-500 mt-0.5">Buku Tersedia</div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-users text-amber-600"></i>
            </div>
            @if($pendingMembers > 0)
            <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">{{ $pendingMembers }} pending</span>
            @endif
        </div>
        <div class="text-3xl font-bold text-gray-900 font-mono">{{ number_format($totalMembers) }}</div>
        <div class="text-sm text-gray-500 mt-0.5">Total Anggota</div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $lateBorrows > 0 ? 'bg-red-100' : 'bg-purple-100' }}">
                <i class="fas fa-book-reader {{ $lateBorrows > 0 ? 'text-red-600' : 'text-purple-600' }}"></i>
            </div>
            @if($lateBorrows > 0)
            <span class="text-xs font-semibold text-red-600 bg-red-50 px-2 py-0.5 rounded-full">{{ $lateBorrows }} telat</span>
            @endif
        </div>
        <div class="text-3xl font-bold text-gray-900 font-mono">{{ number_format($activeBorrows) }}</div>
        <div class="text-sm text-gray-500 mt-0.5">Sedang Dipinjam</div>
    </div>
</div>

{{-- ── Quick Actions ───────────────────────────────────────── --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <a href="{{ route('admin.books.index') }}" class="flex items-center gap-3 bg-evergreen-600 hover:bg-evergreen-700 text-white rounded-2xl px-5 py-4 transition group">
        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-white/30 transition">
            <i class="fas fa-plus"></i>
        </div>
        <div>
            <div class="font-bold text-sm">Input Buku Baru</div>
            <div class="text-xs text-evergreen-200">Tambah koleksi ke sistem</div>
        </div>
    </a>
    <a href="{{ route('admin.transactions.index') }}" class="flex items-center gap-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl px-5 py-4 transition group">
        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-white/30 transition">
            <i class="fas fa-exchange-alt"></i>
        </div>
        <div>
            <div class="font-bold text-sm">Peminjaman Baru</div>
            <div class="text-xs text-blue-200">Catat transaksi sirkulasi</div>
        </div>
    </a>
    <a href="{{ route('admin.members.index') }}?status=pending" class="flex items-center gap-3 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl px-5 py-4 transition group">
        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-white/30 transition">
            <i class="fas fa-user-clock"></i>
        </div>
        <div>
            <div class="font-bold text-sm">Anggota Pending</div>
            <div class="text-xs text-amber-200">{{ $pendingMembers }} akun menunggu</div>
        </div>
    </a>
</div>

{{-- ── Two-col Section ─────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-5 mb-5">

    {{-- Recent Transactions --}}
    <div class="lg:col-span-3 bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-exchange-alt text-gray-400"></i>
                Transaksi Terbaru
            </h3>
            <a href="{{ route('admin.transactions.index') }}" class="text-sm text-evergreen-600 hover:underline font-medium">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-5 py-3 text-left">No</th>
                        <th class="px-5 py-3 text-left">Anggota</th>
                        <th class="px-5 py-3 text-left">Buku</th>
                        <th class="px-5 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentTransactions as $trx)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 font-mono text-xs font-semibold text-gray-400">{{ $trx->transaction_code }}</td>
                        <td class="px-5 py-3 font-medium text-gray-900">{{ $trx->user->full_name ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-600 truncate max-w-[150px]">{{ $trx->book->title ?? '—' }}</td>
                        <td class="px-5 py-3">
                            @if($trx->status === 'returned')
                                <span class="inline-flex items-center gap-1 bg-evergreen-100 text-evergreen-700 text-xs font-semibold px-2.5 py-1 rounded-full">Kembali</span>
                            @elseif($trx->status === 'borrowed')
                                <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 text-xs font-semibold px-2.5 py-1 rounded-full">Dipinjam</span>
                            @elseif($trx->status === 'late')
                                <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-xs font-semibold px-2.5 py-1 rounded-full">Terlambat</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-gray-400 text-sm">
                            <i class="fas fa-inbox text-3xl mb-2 block text-gray-300"></i>
                            Belum ada transaksi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Monthly Stats --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-chart-bar text-gray-400"></i>
                Statistik Bulanan
            </h3>
            <span class="text-xs text-gray-400">{{ now()->translatedFormat('F Y') }}</span>
        </div>

        <div class="space-y-4">
            @php $maxVal = max($monthlyBorrows, $monthlyReturns, $monthlyLate, 1); @endphp

            <div>
                <div class="flex justify-between text-sm mb-1.5">
                    <span class="font-medium text-gray-700">Dipinjam</span>
                    <span class="font-bold text-evergreen-600">{{ $monthlyBorrows }}</span>
                </div>
                <div class="h-2.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-evergreen-500 rounded-full transition-all duration-700" style="width: {{ ($monthlyBorrows / $maxVal) * 100 }}%"></div>
                </div>
            </div>

            <div>
                <div class="flex justify-between text-sm mb-1.5">
                    <span class="font-medium text-gray-700">Dikembalikan</span>
                    <span class="font-bold text-blue-600">{{ $monthlyReturns }}</span>
                </div>
                <div class="h-2.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 rounded-full transition-all duration-700" style="width: {{ ($monthlyReturns / $maxVal) * 100 }}%"></div>
                </div>
            </div>

            <div>
                <div class="flex justify-between text-sm mb-1.5">
                    <span class="font-medium text-gray-700">Terlambat</span>
                    <span class="font-bold text-red-500">{{ $monthlyLate }}</span>
                </div>
                <div class="h-2.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-red-400 rounded-full transition-all duration-700" style="width: {{ ($monthlyLate / $maxVal) * 100 }}%"></div>
                </div>
            </div>
        </div>

        <div class="mt-5 pt-5 border-t border-gray-100 grid grid-cols-3 text-center gap-2">
            <div>
                <div class="text-xs text-gray-400 uppercase tracking-wider">Trend</div>
                <div class="text-sm font-bold text-evergreen-600">+12.5%</div>
            </div>
            <div>
                <div class="text-xs text-gray-400 uppercase tracking-wider">Total</div>
                <div class="text-sm font-bold text-gray-900">{{ $monthTotal }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-400 uppercase tracking-wider">Bulan</div>
                <div class="text-sm font-bold text-gray-900">{{ now()->translatedFormat('M') }}</div>
            </div>
        </div>

        {{-- <a href="{{ route('admin.reports') }}" class="mt-4 w-full flex items-center justify-center gap-2 bg-evergreen-600 hover:bg-evergreen-700 text-white text-sm font-bold rounded-xl py-2.5 transition">
            <i class="fas fa-download"></i> Unduh Laporan
        </a> --}}
    </div>
</div>

{{-- ── Pending Approvals ───────────────────────────────────── --}}
@if($pendingList->count() > 0)
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <h3 class="font-bold text-gray-900 flex items-center gap-2">
            <i class="fas fa-user-clock text-amber-500"></i>
            Pendaftaran Menunggu Persetujuan
            <span class="bg-amber-100 text-amber-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingMembers }}</span>
        </h3>
        <a href="{{ route('admin.members.index') }}?status=pending" class="text-sm text-evergreen-600 hover:underline font-medium">Kelola Semua</a>
    </div>
    <div class="divide-y divide-gray-50">
        @foreach($pendingList as $member)
        <div class="flex items-center gap-4 px-5 py-4">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-sm font-bold text-white flex-shrink-0">
                {{ strtoupper(substr($member->full_name, 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="font-semibold text-gray-900 text-sm">{{ $member->full_name }}</div>
                <div class="text-xs text-gray-500">{{ $member->class->name ?? '—' }} · Daftar {{ $member->created_at->diffForHumans() }}</div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <form method="POST" action="{{ route('admin.members.approve', $member) }}" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="bg-evergreen-100 hover:bg-evergreen-600 text-evergreen-700 hover:text-white text-xs font-bold px-3 py-1.5 rounded-lg transition">
                        Setujui
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.members.reject', $member) }}" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="bg-red-100 hover:bg-red-600 text-red-700 hover:text-white text-xs font-bold px-3 py-1.5 rounded-lg transition">
                        Tolak
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection
