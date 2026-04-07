@extends('layouts.student')

@section('title', 'Dashboard')

@section('content')

{{-- ── Header Row ───────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">
            Halo, {{ explode(' ', auth()->user()->full_name)[0] }}!
        </h1>
        <p class="text-sm text-gray-500 mt-0.5">Selamat datang kembali di dashboard Ruang Baca.</p>
    </div>

    {{-- Search Bar --}}
    <form method="GET" action="{{ route('student.books.index') }}" class="w-full sm:w-72">
        <div class="relative">
            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="search"
                   placeholder="Cari judul, penulis, atau kategori..."
                   class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none shadow-sm">
        </div>
    </form>
</div>

{{-- ── Alert: Buku Terlambat ───────────────────────────── --}}
@php
    $lateBorrows = $activeBorrows->where('status', 'late');
@endphp
@if($lateBorrows->count() > 0)
<div class="mb-5 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4">
    <i class="fas fa-exclamation-triangle text-red-500 mt-0.5 flex-shrink-0"></i>
    <div class="flex-1">
        <p class="font-semibold text-sm">{{ $lateBorrows->count() }} buku terlambat dikembalikan!</p>
        <p class="text-xs text-red-500 mt-0.5">Segera kembalikan untuk menghindari denda tambahan.</p>
    </div>
    <a href="{{ route('student.history') }}" class="text-xs font-bold text-red-600 hover:underline whitespace-nowrap">Lihat Detail →</a>
</div>
@endif

{{-- ── Stats Cards ──────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-7">

    {{-- Buku Dipinjam --}}
    <div class="bg-evergreen-600 rounded-2xl p-5 text-white relative overflow-hidden">
        <div class="absolute right-4 top-4 opacity-20">
            <i class="fas fa-book-open text-5xl"></i>
        </div>
        <div class="text-sm font-medium text-evergreen-100 mb-1">Buku Dipinjam</div>
        <div class="text-5xl font-black leading-none mb-1">{{ $activeBorrows->count() }}</div>
        <div class="text-sm text-evergreen-200">Buku</div>
    </div>

    {{-- Tenggat Terdekat --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5 relative overflow-hidden shadow-sm">
        <div class="absolute right-4 top-4 text-evergreen-400 opacity-40">
            <i class="fas fa-calendar-check text-4xl"></i>
        </div>
        @php
            $nearestDue = $activeBorrows->sortBy('due_date')->first();
            $daysLeft   = $nearestDue ? now()->startOfDay()->diffInDays($nearestDue->due_date, false) : null;
        @endphp
        <div class="text-sm font-medium text-gray-500 mb-1">Tenggat Terdekat</div>
        @if($nearestDue)
        <div class="text-5xl font-black text-gray-900 leading-none mb-1">
            {{ max($daysLeft, 0) }}
        </div>
        <div class="text-sm {{ $daysLeft < 0 ? 'text-red-500 font-semibold' : 'text-gray-400' }}">
            {{ $daysLeft < 0 ? 'Hari Terlambat' : 'Hari Lagi' }}
        </div>
        @else
        <div class="text-3xl font-black text-gray-300 leading-none mt-2">—</div>
        <div class="text-sm text-gray-400 mt-1">Tidak ada pinjaman</div>
        @endif
    </div>

    {{-- Total Denda --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5 relative overflow-hidden shadow-sm">
        <div class="absolute right-4 top-4 text-gray-300 opacity-60">
            <i class="fas fa-money-bill-wave text-4xl"></i>
        </div>
        <div class="text-sm font-medium text-gray-500 mb-1">Total Denda</div>
        <div class="text-3xl font-black text-gray-900 leading-none mb-1">
            Rp {{ number_format($totalFine, 0, ',', '.') }}
        </div>
        @if($totalFine > 0)
        {{-- <div class="text-xs text-red-500 font-semibold mt-1">
            <i class="fas fa-circle text-xs"></i> Harap segera dilunasi
        </div> --}}
        @else
        <div class="text-xs text-evergreen-600 font-semibold mt-1">
            <i class="fas fa-check-circle"></i> Tidak ada tunggakan
        </div>
        @endif
    </div>
</div>

{{-- ── Buku Populer ─────────────────────────────────────── --}}
<div class="mb-7">
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-bold text-gray-900 flex items-center gap-2">
            <i class="fas fa-star text-yellow-400 text-sm"></i>
            Buku Populer
        </h2>
        <a href="{{ route('student.books.index') }}"
           class="text-sm font-semibold text-evergreen-600 hover:underline">
            Lihat Semua →
        </a>
    </div>

    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-4">
        @forelse($newBooks as $i => $book)
        <a href="{{ route('student.books.show', $book->id) }}" class="group">
            {{-- Cover --}}
            <div class="aspect-[3/4] rounded-xl overflow-hidden shadow-sm group-hover:shadow-md transition-shadow mb-2 relative bg-gray-100">
                @if($book->cover)
                <img src="{{ asset('storage/'.$book->cover) }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                     alt="{{ $book->title }}">
                @else
                @php
                    $colors = ['from-teal-600 to-teal-800','from-indigo-500 to-indigo-700','from-emerald-500 to-emerald-700','from-cyan-600 to-cyan-800','from-blue-500 to-blue-700','from-violet-500 to-violet-700'];
                    $c = $colors[$i % count($colors)];
                @endphp
                <div class="w-full h-full bg-gradient-to-b {{ $c }} flex items-end p-2">
                    <span class="text-white text-xs font-semibold leading-tight line-clamp-3">{{ $book->title }}</span>
                </div>
                @endif

                {{-- BARU badge (first 2) --}}
                @if($i < 2)
                <span class="absolute top-2 left-2 bg-evergreen-500 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow">BARU</span>
                @endif
            </div>

            <div class="text-xs font-semibold text-gray-800 leading-snug line-clamp-2 group-hover:text-evergreen-600 transition-colors">
                {{ $book->title }}
            </div>
            <div class="text-xs text-gray-400 mt-0.5 truncate">{{ $book->author }}</div>
        </a>
        @empty
        <div class="col-span-6 text-center py-10 text-gray-400 text-sm">
            <i class="fas fa-books text-4xl mb-3 block text-gray-200"></i>
            Belum ada buku tersedia
        </div>
        @endforelse
    </div>
</div>

{{-- ── Riwayat Transaksi Terakhir ───────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <h2 class="font-bold text-gray-900 flex items-center gap-2">
            <i class="fas fa-history text-evergreen-500 text-sm"></i>
            Riwayat Transaksi Terakhir
        </h2>
        <a href="{{ route('student.history') }}" class="text-sm font-semibold text-evergreen-600 hover:underline">
            Lihat Semua →
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-50 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                    <th class="px-6 py-3 text-left">Judul Buku</th>
                    <th class="px-6 py-3 text-left">Tgl Pinjam</th>
                    <th class="px-6 py-3 text-left">Tgl Kembali</th>
                    <th class="px-6 py-3 text-left">Status</th>
                </tr>
            </thead>
            <tbody>
                {{-- Active borrows first --}}
                @foreach($activeBorrows->take(3) as $trx)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-semibold text-sm text-gray-900">{{ $trx->book->title ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $trx->borrow_date?->format('d M Y') ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm {{ $trx->status === 'late' ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                        {{ $trx->due_date?->format('d M Y') ?? '—' }}
                    </td>
                    <td class="px-6 py-4">
                        @if($trx->status === 'late')
                            <span class="inline-flex items-center bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full">Terlambat</span>
                        @else
                            <span class="inline-flex items-center bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">DIPINJAM</span>
                        @endif
                    </td>
                </tr>
                @endforeach

                {{-- Return history --}}
                @foreach($borrowHistory->take(5 - min($activeBorrows->count(), 3)) as $trx)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition last:border-0">
                    <td class="px-6 py-4 font-semibold text-sm text-gray-900">{{ $trx->book->title ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $trx->borrow_date?->format('d M Y') ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $trx->due_date?->format('d M Y') ?? '—' }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center bg-evergreen-100 text-evergreen-700 text-xs font-bold px-3 py-1 rounded-full">Kembali</span>
                    </td>
                </tr>
                @endforeach

                @if($activeBorrows->isEmpty() && $borrowHistory->isEmpty())
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 text-sm">
                        <i class="fas fa-inbox text-4xl mb-3 block text-gray-200"></i>
                        Belum ada riwayat peminjaman
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

@endsection
