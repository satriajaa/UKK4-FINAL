    @extends('layouts.student')
    @section('title', 'Riwayat Peminjaman')

    @push('styles')
        <style>
            .tab-btn.active {
                background: #166534;
                color: white;
            }

            .tab-btn {
                transition: all 0.2s ease;
            }

            .return-modal-overlay {
                backdrop-filter: blur(4px);
            }
        </style>
    @endpush

    @section('content')
        @php
            $userId = auth()->id();
        $schoolId = auth()->user()->school_id;

        // Ambil setting sekolah untuk fine_per_day
        $setting   = \App\Models\Setting::getForSchool($schoolId);
        $finePerDay = (int) ($setting->fine_per_day ?? 1000);

        $activeBorrows = \App\Models\Borrowing::with('book.category')
            ->where('user_id', $userId)
            ->whereIn('status', ['pending', 'borrowed', 'late', 'return_requested'])
            ->orderByRaw("FIELD(status, 'late', 'return_requested', 'borrowed', 'pending')")
            ->orderBy('due_date')
            ->get()
            ->map(function ($trx) {
                if ($trx->status === 'borrowed' && now()->isAfter($trx->due_date)) {
                    $trx->update(['status' => 'late']);
                }
                return $trx;
            });

        // $returnedBorrows = \App\Models\Borrowing::with('book.category')
        //     ->where('user_id', $userId)
        //     ->whereIn('status', ['returned', 'rejected'])
        //     ->latest('updated_at')
        //     ->get();

        $totalBorrowed = \App\Models\Borrowing::where('user_id', $userId)->count();
        $totalActive   = $activeBorrows->whereIn('status', ['borrowed', 'late'])->count();
        $totalLate     = $activeBorrows->where('status', 'late')->count();
        $totalFine     = \App\Models\Borrowing::where('user_id', $userId)->sum('fine');
            // $totalPending = $activeBorrows->where('status', 'pending')->count();
        @endphp

        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Riwayat Peminjaman</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola dan pantau semua aktivitas peminjaman bukumu</p>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
                <div class="flex items-start justify-between mb-3">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Pinjam</div>
                    <div class="w-8 h-8 bg-blue-50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-book text-blue-500 text-sm"></i>
                    </div>
                </div>
                <div class="text-4xl font-black text-gray-900 leading-none mb-1">{{ $totalBorrowed }}</div>
                <div class="text-xs text-gray-400 font-medium">Semua waktu</div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
                <div class="flex items-start justify-between mb-3">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Sedang Dipinjam</div>
                    <div class="w-8 h-8 bg-orange-50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-book-open text-orange-500 text-sm"></i>
                    </div>
                </div>
                <div class="text-4xl font-black text-gray-900 leading-none mb-1">{{ $totalActive }}</div>
                <div class="text-xs text-gray-400 font-medium">Aktif saat ini</div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
                <div class="flex items-start justify-between mb-3">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Terlambat</div>
                    <div
                        class="w-8 h-8 {{ $totalLate > 0 ? 'bg-red-50' : 'bg-gray-50' }} rounded-xl flex items-center justify-center">
                        <i
                            class="fas fa-exclamation-circle {{ $totalLate > 0 ? 'text-red-500' : 'text-gray-300' }} text-sm"></i>
                    </div>
                </div>
                <div class="text-4xl font-black {{ $totalLate > 0 ? 'text-red-600' : 'text-gray-900' }} leading-none mb-1">
                    {{ $totalLate }}</div>
                @if ($totalLate > 0)
                    <div class="text-xs text-red-500 font-semibold">Butuh perhatian</div>
                @else
                    <div class="text-xs text-evergreen-600 font-semibold">Tepat waktu semua</div>
                @endif
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
                <div class="flex items-start justify-between mb-3">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Denda</div>
                    <div
                        class="w-8 h-8 {{ $totalFine > 0 ? 'bg-red-50' : 'bg-evergreen-50' }} rounded-xl flex items-center justify-center">
                        <i class="fas fa-money-bill {{ $totalFine > 0 ? 'text-red-500' : 'text-evergreen-500' }} text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl font-black {{ $totalFine > 0 ? 'text-red-600' : 'text-gray-900' }} leading-none mb-1">
                    Rp {{ number_format($totalFine, 0, ',', '.') }}
                </div>
                @if ($totalFine > 0)
                    <div class="text-xs text-red-500 font-semibold">denda keseluruhan</div>
                @else
                    <div class="text-xs text-evergreen-600 font-semibold">Tidak ada denda</div>
                @endif
            </div>
        </div>

        {{-- Table Section --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

            {{-- Toolbar --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-4 border-b border-gray-100">
                <div class="flex items-center bg-gray-100 rounded-xl p-1 gap-1">
                    <button onclick="switchTab('active')" id="tab-active"
                        class="tab-btn active relative text-xs font-bold px-4 py-2 rounded-lg">
                        Aktif & Pending
                        @if ($totalPending > 0)
                            <span
                                class="inline-flex items-center justify-center w-4 h-4 bg-amber-500 text-white text-[8px] font-black rounded-full ml-1">
                                {{ $totalPending }}
                            </span>
                        @endif
                    </button>
                    <button onclick="switchTab('returned')" id="tab-returned"
                        class="tab-btn text-xs font-bold px-4 py-2 rounded-lg text-gray-600 hover:text-gray-900">
                        Selesai & Ditolak
                    </button>
                </div>
            </div>

            {{-- TAB: Aktif & Pending --}}
            <div id="tab-content-active">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-50 text-xs font-bold text-gray-400 uppercase tracking-wider">
                                <th class="px-5 py-3 text-left">Buku</th>
                                <th class="px-5 py-3 text-left">Tgl Pinjam</th>
                                <th class="px-5 py-3 text-left">Batas Kembali</th>
                                <th class="px-5 py-3 text-left">Status</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeBorrows as $trx)
                                {{-- @php
                                    $dueDate = $trx->due_date->startOfDay();
                                    $today = now()->startOfDay();
                                    $isLate = $trx->status === 'late' || $today->greaterThan($dueDate);
                                    $daysLeft = $today->diffInDays($dueDate, false);
                                @endphp --}}
                                @php
        // FIX: Bandingkan hanya tanggal (tanpa jam) untuk menghindari false positive
        $dueDate  = $trx->due_date->startOfDay();
        $today    = now()->startOfDay();
        $isLate   = $trx->status === 'late' || $today->greaterThan($dueDate);
        $daysLeft = $today->diffInDays($dueDate, false); // negatif = terlambat, positif = sisa
    @endphp
                                <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                                    {{-- Buku --}}
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex-shrink-0 rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center"
                                                style="height:52px;width:40px">
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
                                                <div class="font-semibold text-sm text-gray-900">{{ $trx->book->title ?? '—' }}
                                                </div>
                                                <div class="text-xs text-gray-400">{{ $trx->book->author ?? '' }}</div>
                                                <div class="text-xs text-gray-300 font-mono">{{ $trx->transaction_code }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Tgl Pinjam --}}
                                    <td class="px-5 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $trx->borrow_date?->format('d M Y') ?? '—' }}
                                    </td>

                                    {{-- Batas Kembali --}}
                                    {{-- <td class="px-5 py-4 whitespace-nowrap">
                                        @if (in_array($trx->status, ['pending']))
                                            <span class="text-sm text-gray-400 italic">—</span>
                                        @else
                                            <div
                                                class="text-sm {{ $isLate ? 'text-red-600 font-bold' : 'text-gray-700 font-semibold' }}">
                                                {{ $trx->due_date?->format('d M Y') ?? '—' }}
                                            </div>
                                            @if ($isLate)
                                                <div class="text-xs text-red-500 font-semibold mt-0.5">{{ abs($daysLeft) }}
                                                    hari terlambat</div>
                                            @elseif($daysLeft <= 3)
                                                <div class="text-xs text-amber-500 font-semibold mt-0.5">{{ $daysLeft }}
                                                    hari lagi</div>
                                            @else
                                                <div class="text-xs text-gray-400 mt-0.5">{{ $daysLeft }} hari lagi</div>
                                            @endif
                                        @endif
                                    </td> --}}
                                    {{-- Kolom Due Date --}}
    <td class="px-5 py-4 whitespace-nowrap">
        <div class="text-sm {{ $isLate ? 'text-red-600 font-bold' : 'text-gray-700 font-semibold' }}">
            {{ $trx->due_date?->format('d M Y') ?? '—' }}
        </div>
        @if ($isLate)
            <div class="text-xs text-red-500 font-semibold mt-0.5">{{ abs($daysLeft) }} hari terlambat</div>
        @elseif($daysLeft <= 3)
            <div class="text-xs text-amber-500 font-semibold mt-0.5">{{ $daysLeft }} hari lagi</div>
        @else
            <div class="text-xs text-gray-400 mt-0.5">{{ $daysLeft }} hari lagi</div>
        @endif
    </td>

                                    {{-- Status Badge --}}
                                    <td class="px-5 py-4">
                                        @if ($trx->status === 'pending')
                                            <span
                                                class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-700 text-xs font-bold px-3 py-1.5 rounded-full">
                                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                                                Menunggu ACC
                                            </span>
                                        @elseif($trx->status === 'return_requested')
                                            <span
                                                class="inline-flex items-center gap-1.5 bg-purple-100 text-purple-700 text-xs font-bold px-3 py-1.5 rounded-full">
                                                <span class="w-1.5 h-1.5 bg-purple-500 rounded-full animate-pulse"></span>
                                                Proses Kembali
                                            </span>
                                        @elseif($isLate)
                                            <span
                                                class="inline-flex items-center gap-1.5 bg-red-100 text-red-700 text-xs font-bold px-3 py-1.5 rounded-full">
                                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span> Terlambat
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 bg-evergreen-100 text-evergreen-700 text-xs font-bold px-3 py-1.5 rounded-full">
                                                <span class="w-1.5 h-1.5 bg-evergreen-500 rounded-full animate-pulse"></span>
                                                Aktif
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-5 py-4 text-right">
                                        @if ($trx->status === 'pending')
                                            <div class="flex flex-col items-end gap-1">
                                                <span
                                                    class="inline-flex items-center gap-1.5 bg-amber-50 border border-amber-200 text-amber-700 text-xs font-bold px-4 py-2 rounded-xl">
                                                    <i class="fas fa-hourglass-half text-xs animate-pulse"></i> Menunggu ACC
                                                    Pinjam
                                                </span>
                                                <span class="text-xs text-gray-400">Pustakawan sedang memproses</span>
                                            </div>
                                        @elseif($trx->status === 'return_requested')
                                            <div class="flex flex-col items-end gap-1">
                                                <span
                                                    class="inline-flex items-center gap-1.5 bg-purple-50 border border-purple-200 text-purple-700 text-xs font-bold px-4 py-2 rounded-xl">
                                                    <i class="fas fa-clock text-xs animate-pulse"></i> Menunggu ACC Kembali
                                                </span>
                                                <span class="text-xs text-gray-400">Pustakawan sedang memverifikasi</span>
                                            </div>
                                        @else
                                            {{-- Di kolom Aksi, ganti onclick openReturnModal --}}
    <button
        onclick="openReturnModal(
            {{ $trx->id }},
            '{{ addslashes($trx->book->title ?? '') }}',
            '{{ addslashes($trx->book->author ?? '') }}',
            '{{ $trx->borrow_date?->format('d M Y') }}',
            '{{ $trx->due_date?->format('d M Y') }}',
            {{ $isLate ? 'true' : 'false' }},
            {{ abs($daysLeft) }},
            {{ $isLate ? abs($daysLeft) * $finePerDay : 0 }}
        )"
        class="inline-flex items-center gap-2 bg-evergreen-600 hover:bg-evergreen-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition">
        <i class="fas fa-undo-alt text-xs"></i> Kembalikan
    </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-16 text-center">
                                        <i class="fas fa-book-open text-5xl text-gray-200 mb-3 block"></i>
                                        <p class="text-gray-500 font-medium">Tidak ada buku yang sedang dipinjam</p>
                                        <a href="{{ route('student.books.index') }}"
                                            class="mt-3 inline-block text-sm text-evergreen-600 font-semibold hover:underline">
                                            Jelajahi Katalog Buku →
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($activeBorrows->count() > 0)
                    <div class="px-5 py-3 border-t border-gray-50 text-xs text-gray-400">
                        Menampilkan {{ $activeBorrows->count() }} item
                        @if ($totalPending > 0)
                            · <span class="text-amber-600 font-semibold">{{ $totalPending }} menunggu ACC</span>
                        @endif
                    </div>
                @endif
            </div>

            {{-- TAB: Selesai & Ditolak --}}
            <div id="tab-content-returned" class="hidden">
                <div class="px-5 py-3 bg-gray-50 border-b border-gray-100">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">Histori Pengembalian &
                        Penolakan</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-50 text-xs font-bold text-gray-400 uppercase tracking-wider">
                                <th class="px-5 py-3 text-left">Buku</th>
                                <th class="px-5 py-3 text-left">Tgl Pinjam</th>
                                <th class="px-5 py-3 text-left">Tgl Kembali / Ditolak</th>
                                <th class="px-5 py-3 text-left">Status</th>
                                <th class="px-5 py-3 text-right">Denda</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($returnedBorrows as $trx)
                                @php
                                    $wasLate =
                                        $trx->fine > 0 || ($trx->return_date && $trx->return_date->isAfter($trx->due_date));
                                @endphp
                                <tr class="border-b border-gray-50 hover:bg-gray-50 transition last:border-0">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex-shrink-0 rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center"
                                                style="height:48px;width:36px">
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
                                                <div class="font-semibold text-sm text-gray-900">
                                                    {{ $trx->book->title ?? '—' }}</div>
                                                <div class="text-xs text-gray-400">{{ $trx->book->author ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $trx->borrow_date?->format('d M Y') ?? '—' }}
                                    </td>
                                    <td class="px-5 py-4 text-sm whitespace-nowrap">
                                        @if ($trx->status === 'rejected')
                                            <span class="text-gray-500">{{ $trx->updated_at?->format('d M Y') ?? '—' }}</span>
                                        @else
                                            <span
                                                class="text-gray-500">{{ $trx->return_date?->format('d M Y') ?? ($trx->due_date?->format('d M Y') ?? '—') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        @if ($trx->status === 'rejected')
                                            {{-- Status Ditolak --}}
                                            <div>
                                                <span
                                                    class="inline-flex items-center gap-1.5 bg-red-100 text-red-600 text-xs font-bold px-3 py-1.5 rounded-full">
                                                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span> Ditolak
                                                </span>
                                                @if ($trx->rejection_reason)
                                                    <div class="text-xs text-gray-400 mt-1 max-w-[200px]">
                                                        <i class="fas fa-info-circle text-gray-300"></i>
                                                        {{ $trx->rejection_reason }}
                                                    </div>
                                                @endif
                                            </div>
                                        @elseif($wasLate)
                                            <span
                                                class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-700 text-xs font-bold px-3 py-1.5 rounded-full">
                                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span> Terlambat
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1.5 rounded-full">
                                                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span> Selesai
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        @if ($trx->status === 'rejected')
                                            <span class="text-xs text-gray-300 italic">—</span>
                                        @elseif($trx->fine > 0)
                                            <span class="text-sm font-bold text-red-600">Rp
                                                {{ number_format($trx->fine, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-sm text-gray-400">Rp 0</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-16 text-center">
                                        <i class="fas fa-inbox text-5xl text-gray-200 mb-3 block"></i>
                                        <p class="text-gray-500 font-medium">Belum ada histori</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                 @if ($returnedBorrows->hasPages())
        <div class="px-5 py-4 border-t border-gray-50 flex items-center justify-between">
            <span class="text-xs text-gray-400">
                Menampilkan <strong>{{ $returnedBorrows->firstItem() }}–{{ $returnedBorrows->lastItem() }}</strong>
                dari <strong>{{ $returnedBorrows->total() }}</strong> riwayat
            </span>
            {{ $returnedBorrows->links() }}
        </div>
    @endif
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-8 text-center text-xs text-gray-300 space-y-1">
            <div>© {{ date('Y') }} Ruang Baca {{ auth()->user()->school->name ?? '' }}. Seluruh hak cipta dilindungi.
            </div>
        </div>

        {{-- Return Modal --}}
        <div id="return-modal"
            class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 return-modal-overlay p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 bg-evergreen-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-book-open text-white text-xs"></i>
                        </div>
                        <span class="font-bold text-gray-900 text-sm">RUANG BACA</span>
                    </div>
                    <button onclick="closeReturnModal()" class="text-gray-400 hover:text-gray-600 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="px-6 py-5">
                    <h2 class="text-lg font-bold text-gray-900 mb-1">Konfirmasi Pengembalian</h2>
                    <p class="text-sm text-gray-500 mb-5">Pastikan buku dalam kondisi baik sebelum dikembalikan.</p>

                    <div class="bg-gray-50 rounded-xl p-4 mb-4">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Data Buku</div>
                        <div class="flex gap-3">
                            <div class="w-12 h-16 bg-evergreen-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-book text-evergreen-600"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-bold text-gray-900 text-sm" id="return-book-title">—</div>
                                <div class="text-xs text-gray-500 mt-0.5" id="return-book-author">—</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4 mb-4">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Informasi Pengembalian</div>
                        <div class="grid grid-cols-3 gap-3 text-center">
                            <div>
                                <div class="text-xs text-gray-400 mb-1">Tgl Pinjam</div>
                                <div class="text-sm font-bold text-gray-900" id="return-borrow-date">—</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 mb-1">Batas Kembali</div>
                                <div class="text-sm font-bold" id="return-due-date">—</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 mb-1">Hari Ini</div>
                                <div class="text-sm font-bold text-gray-900">{{ now()->format('d M Y') }}</div>
                            </div>
                        </div>
                    </div>

                    <div id="return-status-ok" class="hidden bg-evergreen-50 border border-evergreen-100 rounded-xl p-3 mb-4">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-evergreen-600"></i>
                            <div>
                                <div class="text-sm font-bold text-evergreen-700">TEPAT WAKTU</div>
                                <div class="text-xs text-evergreen-600">Tidak ada denda. Terima kasih!</div>
                            </div>
                        </div>
                    </div>

                    <div id="return-status-late" class="hidden bg-red-50 border border-red-100 rounded-xl p-3 mb-4">
                        <div class="flex items-start gap-2">
                            <i class="fas fa-exclamation-triangle text-red-500 mt-0.5"></i>
                            <div>
                                <div class="text-sm font-bold text-red-700">TERLAMBAT <span id="return-days-late">0</span>
                                    HARI</div>
                                <div class="text-xs text-red-600 mt-1">
                                    Total denda: <span class="font-black text-red-700 text-sm" id="return-fine-amount">Rp
                                        0</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Info box --}}
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 mb-4 flex gap-2">
                        <i class="fas fa-info-circle text-blue-500 text-sm mt-0.5 flex-shrink-0"></i>
                        <p class="text-xs text-blue-700">
                            Setelah konfirmasi, permintaan pengembalianmu akan <strong>menunggu verifikasi pustakawan</strong>.
                            Buku dianggap dikembalikan setelah pustakawan mengkonfirmasi.
                        </p>
                    </div>

                    <label class="flex items-start gap-3 mb-5 cursor-pointer">
                        <input type="checkbox" id="return-confirm-check" class="mt-0.5 w-4 h-4 rounded accent-evergreen-600">
                        <span class="text-xs text-gray-600">Saya menyatakan buku dalam kondisi baik dan tidak ada
                            kerusakan</span>
                    </label>

                    <form id="return-form" method="POST" action="">
                        @csrf
                        @method('PATCH')
                        <div class="flex gap-3">
                            <button type="button" onclick="closeReturnModal()"
                                class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50 transition">
                                Batal
                            </button>
                            <button type="submit" id="return-submit-btn" disabled
                                class="flex-1 text-white text-sm font-bold py-2.5 px-4 rounded-xl transition flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed bg-evergreen-600 hover:bg-evergreen-700">
                                <i class="fas fa-paper-plane text-xs"></i> Kirim Permintaan Kembali
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @endsection

    @push('scripts')
        <script>
            function switchTab(tab) {
                ['active', 'returned'].forEach(t => {
                    document.getElementById(`tab-${t}`).classList.toggle('active', t === tab);
                    document.getElementById(`tab-content-${t}`).classList.toggle('hidden', t !== tab);
                });
            }

            function openReturnModal(borrowingId, title, author, borrowDate, dueDate, isLate, daysLate, fine) {
                document.getElementById('return-book-title').textContent = title;
                document.getElementById('return-book-author').textContent = author;
                document.getElementById('return-borrow-date').textContent = borrowDate;

                const dueDateEl = document.getElementById('return-due-date');
                dueDateEl.textContent = dueDate;
                dueDateEl.className = isLate ? 'text-sm font-bold text-red-600' : 'text-sm font-bold text-gray-900';

                document.getElementById('return-status-ok').classList.toggle('hidden', isLate);
                document.getElementById('return-status-late').classList.toggle('hidden', !isLate);

                if (isLate) {
                    document.getElementById('return-days-late').textContent = daysLate;
                    document.getElementById('return-fine-amount').textContent = 'Rp ' + fine.toLocaleString('id-ID');
                }

                document.getElementById('return-form').action = `/student/return/${borrowingId}`;
                document.getElementById('return-confirm-check').checked = false;
                document.getElementById('return-submit-btn').disabled = true;
                document.getElementById('return-modal').classList.remove('hidden');
                document.getElementById('return-modal').classList.add('flex');
            }

            function closeReturnModal() {
                document.getElementById('return-modal').classList.add('hidden');
                document.getElementById('return-modal').classList.remove('flex');
            }

            document.getElementById('return-confirm-check').addEventListener('change', function() {
                document.getElementById('return-submit-btn').disabled = !this.checked;
            });

            document.getElementById('return-modal').addEventListener('click', function(e) {
                if (e.target === this) closeReturnModal();
            });
        </script>
    @endpush
