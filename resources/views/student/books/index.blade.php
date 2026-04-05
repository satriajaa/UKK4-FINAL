@extends('layouts.student')
@section('title', 'Katalog Buku Digital')

@push('styles')
    <style>
        /* ── Heart ── */
        @keyframes heartPop {
            0% {
                transform: scale(1)
            }

            40% {
                transform: scale(1.6)
            }

            70% {
                transform: scale(.9)
            }

            100% {
                transform: scale(1)
            }
        }

        .heart-pop {
            animation: heartPop .35s ease forwards
        }

        /* ── Card ── */
        .book-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #f0f0f0;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .05);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .book-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, .09)
        }

        /* Cover: fixed 3:4 ratio */
        .cover-wrap {
            position: relative;
            width: 100%;
            padding-top: 133.333%;
            overflow: hidden;
            background: #f3f4f6;
            flex-shrink: 0;
        }

        .cover-wrap img,
        .cover-wrap .cover-gradient {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }

        .cover-wrap img {
            object-fit: cover;
            transition: transform .45s ease;
            z-index: 1;
            /* img di atas gradient */
        }

        .cover-wrap .cover-gradient {
            z-index: 0;
            /* gradient di bawah img */
        }

        .book-card:hover .cover-wrap img {
            transform: scale(1.06)
        }

        /* Badge */
        .cover-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            z-index: 10;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: .06em;
            padding: 3px 8px;
            border-radius: 20px;
            backdrop-filter: blur(4px);
            line-height: 1.4;
        }

        /* Heart button */
        .heart-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            z-index: 10;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .9);
            border: 1px solid rgba(0, 0, 0, .06);
            box-shadow: 0 2px 6px rgba(0, 0, 0, .12);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background .15s, transform .15s;
        }

        .heart-btn:hover {
            background: #fff;
            transform: scale(1.1)
        }

        .heart-btn i {
            font-size: 12px;
            color: #cbd5e1;
            transition: color .15s
        }

        .heart-btn:hover i,
        .heart-btn.on i {
            color: #ef4444
        }

        /* Card body */
        .card-body {
            padding: 12px 14px 14px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0
        }

        .cat-tag {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .07em;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 4px
        }

        .book-title {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 2px
        }

        .book-author {
            font-size: 11px;
            color: #9ca3af;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 8px
        }

        .stars-row {
            color: #facc15;
            font-size: 11px;
            letter-spacing: -1px
        }

        .spacer {
            flex: 1
        }

        /* Pinjam button */
        .btn-pinjam {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            padding: 9px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 800;
            transition: background .15s;
        }

        .btn-pinjam.avail {
            background: #166534;
            color: #fff
        }

        .btn-pinjam.avail:hover {
            background: #14532d
        }

        .btn-pinjam.out {
            background: #f1f5f9;
            color: #94a3b8;
            cursor: not-allowed
        }

        .btn-pinjam.mine {
            background: #eff6ff;
            color: #3b82f6;
            cursor: not-allowed
        }

        /* Filter pills */
        .cat-pill {
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            border: 1.5px solid #e5e7eb;
            color: #6b7280;
            background: #fff;
            cursor: pointer;
            transition: all .15s;
            white-space: nowrap;
        }

        .cat-pill:hover {
            border-color: #166534;
            color: #166534
        }

        .cat-pill.on {
            background: #166534;
            color: #fff;
            border-color: #166534
        }

        /* View toggle */
        .view-btn {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            border: 1.5px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            cursor: pointer;
            transition: all .15s;
        }

        .view-btn:hover {
            border-color: #166534;
            color: #166534
        }

        .view-btn.on {
            background: #166534;
            color: #fff;
            border-color: #166534
        }

        /* List row */
        .list-row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 13px 20px;
            border-bottom: 1px solid #f3f4f6;
            transition: background .12s
        }

        .list-row:last-child {
            border-bottom: none
        }

        .list-row:hover {
            background: #fafafa
        }

        /* Sort */
        select.sort-sel {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 16px;
            padding-right: 32px;
        }

        /* Pagination */
        .pg-btn {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            border: 1.5px solid #e5e7eb;
            color: #374151;
            transition: all .15s;
        }

        .pg-btn:hover:not(.dis):not(.cur) {
            border-color: #166534;
            color: #166534
        }

        .pg-btn.cur {
            background: #166534;
            color: #fff;
            border-color: #166534
        }

        .pg-btn.dis {
            color: #d1d5db;
            cursor: not-allowed
        }

        /* Backdrop */
        #borrow-modal {
            backdrop-filter: blur(4px)
        }
    </style>
@endpush

@section('content')
    @php
        $userId = auth()->id();
        $schoolId = auth()->user()->school_id;
        $wishlistedIds = \App\Models\Wishlist::where('user_id', $userId)->pluck('book_id')->toArray();
        $categories = \App\Models\Category::where('school_id', $schoolId)->orderBy('name')->get();
        $gradients = [
            'from-teal-600 to-teal-800',
            'from-indigo-500 to-indigo-700',
            'from-emerald-500 to-emerald-700',
            'from-cyan-600 to-cyan-800',
            'from-blue-500 to-blue-700',
            'from-violet-500 to-violet-700',
            'from-rose-500 to-rose-700',
            'from-amber-600 to-amber-800',
        ];

        $q = \App\Models\Book::with(['category', 'reviews'])
            ->where('school_id', $schoolId)
            ->where('is_available', true);
        if (request('search')) {
            $q->where(
                fn($x) => $x
                    ->where('title', 'like', '%' . request('search') . '%')
                    ->orWhere('author', 'like', '%' . request('search') . '%')
                    ->orWhere('isbn', 'like', '%' . request('search') . '%'),
            );
        }
        if (request('category')) {
            $q->where('category_id', request('category'));
        }
        match (request('sort', 'newest')) {
            'oldest' => $q->oldest(),
            'rating' => $q->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating'),
            'title' => $q->orderBy('title'),
            default => $q->latest(),
        };
        $books = $q->paginate(12)->withQueryString();
        $view = request('view', 'grid');

        // setting sekolah (sudah di-pass dari controller, fallback kalau belum)
        if (!isset($setting)) {
            $setting = \App\Models\Setting::getForSchool($schoolId);
        }
        $maxDays = $setting->max_borrow_days ?? 14;
        $finePerDay = (int) ($setting->fine_per_day ?? 1000);
        $dueDate = now()->addDays($maxDays)->format('d M Y');
    @endphp

    {{-- Header --}}
    <div class="mb-5">
        <h1 class="text-2xl font-bold text-gray-900">Katalog Buku Digital</h1>
        <p class="text-sm text-gray-500 mt-0.5">Temukan ribuan koleksi buku untuk menunjang belajarmu hari ini.</p>
    </div>

    {{-- Form --}}
    <form method="GET" action="{{ route('student.books.index') }}" id="ff">
        <input type="hidden" name="category" id="fi-cat" value="{{ request('category') }}">
        <input type="hidden" name="sort" id="fi-sort" value="{{ request('sort', 'newest') }}">
        <input type="hidden" name="view" id="fi-view" value="{{ $view }}">

        {{-- Search --}}
        <div class="relative mb-4">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari judul, penulis, atau ISBN..."
                class="w-full pl-11 pr-4 py-3 bg-white border border-gray-200 rounded-2xl text-sm outline-none focus:ring-2 focus:ring-evergreen-500 shadow-sm">
        </div>

        {{-- Toolbar --}}
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <div class="flex flex-wrap gap-2">
                <button type="button" onclick="setCat('')"
                    class="cat-pill {{ !request('category') ? 'on' : '' }}">Semua</button>
                @foreach ($categories->take(5) as $cat)
                    <button type="button" onclick="setCat('{{ $cat->id }}')"
                        class="cat-pill {{ request('category') == $cat->id ? 'on' : '' }}">{{ $cat->name }}</button>
                @endforeach
                @if ($categories->count() > 5)
                    <div class="relative" id="more-wrap">
                        <button type="button" onclick="document.getElementById('more-dd').classList.toggle('hidden')"
                            class="cat-pill"><i class="fas fa-ellipsis-h"></i></button>
                        <div id="more-dd"
                            class="hidden absolute left-0 top-full mt-1 z-20 bg-white rounded-xl border border-gray-100 shadow-lg p-2 flex flex-col gap-0.5 min-w-[150px]">
                            @foreach ($categories->skip(5) as $cat)
                                <button type="button" onclick="setCat('{{ $cat->id }}')"
                                    class="text-left text-xs font-semibold px-3 py-2 rounded-lg hover:bg-gray-50 {{ request('category') == $cat->id ? 'text-evergreen-700 bg-evergreen-50' : 'text-gray-600' }}">{{ $cat->name }}</button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <select onchange="setSort(this.value)"
                    class="sort-sel text-xs bg-white border border-gray-200 rounded-xl px-3 py-2 text-gray-700 outline-none focus:ring-2 focus:ring-evergreen-500">
                    <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Terlama</option>
                    <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>Rating Tertinggi</option>
                    <option value="title" {{ request('sort') === 'title' ? 'selected' : '' }}>A – Z</option>
                </select>
                <button type="button" onclick="setView('grid')" id="vg"
                    class="view-btn {{ $view === 'grid' ? 'on' : '' }}"><i class="fas fa-th text-xs"></i></button>
                <button type="button" onclick="setView('list')" id="vl"
                    class="view-btn {{ $view === 'list' ? 'on' : '' }}"><i class="fas fa-list text-xs"></i></button>
            </div>
        </div>
    </form>

    {{-- Result label --}}
    @if ($books->count())
        <div class="flex items-center justify-between mb-4">
            <p class="text-sm text-gray-500">
                Menampilkan <strong class="text-gray-900">{{ $books->firstItem() }}–{{ $books->lastItem() }}</strong>
                dari <strong class="text-gray-900">{{ $books->total() }}</strong> buku
                @if (request('search'))
                    untuk "<strong class="text-evergreen-700">{{ request('search') }}</strong>"
                @endif
            </p>
            @if (request('search') || request('category'))
                <a href="{{ route('student.books.index') }}"
                    class="text-xs font-bold text-red-500 hover:text-red-700 flex items-center gap-1">
                    <i class="fas fa-times-circle"></i> Reset
                </a>
            @endif
        </div>
    @endif

    {{-- ══ GRID ════════════════════════════════════════════════════ --}}
    <div id="vw-grid" class="{{ $view === 'list' ? 'hidden' : '' }} mb-8">
        @if ($books->count())
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-5">
                @foreach ($books as $i => $book)
                    @php
                        $avg = round($book->reviews->avg('rating') ?? 0, 1);
                        $rcnt = $book->reviews->count();
                        $isWL = in_array($book->id, $wishlistedIds);
                        $ok = $book->stock > 0;
                        $mine = \App\Models\Borrowing::where('user_id', $userId)
                            ->where('book_id', $book->id)
                            ->whereIn('status', ['pending', 'borrowed', 'late', 'return_requested'])
                            ->first();
                        $grad = $gradients[$i % count($gradients)];
                    @endphp
                    <div class="book-card">
                        {{-- Cover --}}
                        <div class="cover-wrap">
                            @if ($book->cover)
                                <img src="{{ asset('storage/' . $book->cover) }}" alt="{{ $book->title }}"
                                    loading="lazy">
                            @else
                                <div
                                    class="cover-gradient bg-gradient-to-b {{ $grad }} flex flex-col justify-end p-3">
                                    <span
                                        class="text-white/90 text-xs font-bold leading-snug line-clamp-4">{{ $book->title }}</span>
                                </div>
                            @endif

                            {{-- Badge top-left --}}
                            @if ($mine)
                                @if ($mine->status === 'pending')
                                    <span class="cover-badge bg-amber-500/80 text-white">MENUNGGU ACC</span>
                                @elseif($mine->status === 'return_requested')
                                    <span class="cover-badge bg-purple-600/80 text-white">PROSES KEMBALI</span>
                                @else
                                    <span class="cover-badge bg-blue-600/80 text-white">DIPINJAM</span>
                                @endif
                            @elseif($ok)
                                <span class="cover-badge bg-evergreen-600/80 text-white">TERSEDIA</span>
                            @else
                                <span class="cover-badge bg-gray-600/80 text-white">HABIS</span>
                            @endif

                            {{-- Heart top-right --}}
                            <button class="heart-btn {{ $isWL ? 'on' : '' }}" data-book-id="{{ $book->id }}"
                                onclick="toggleWL(this, {{ $book->id }})"
                                title="{{ $isWL ? 'Hapus Wishlist' : 'Tambah Wishlist' }}">
                                <i class="{{ $isWL ? 'fas' : 'far' }} fa-heart"></i>
                            </button>
                        </div>

                        {{-- Body --}}
                        <div class="card-body">
                            <p class="cat-tag">{{ $book->category->name ?? '' }}</p>
                            <a href="{{ route('student.books.show', $book->id) }}">
                                <h3 class="book-title hover:text-evergreen-700 transition">{{ $book->title }}</h3>
                            </a>
                            <p class="book-author">{{ $book->author }}</p>

                            @if ($rcnt > 0)
                                <div class="flex items-center gap-1 mb-3">
                                    <span class="stars-row">
                                        @for ($s = 1; $s <= 5; $s++)
                                            {{ $s <= round($avg) ? '★' : '☆' }}
                                        @endfor
                                    </span>
                                    <span class="text-xs font-bold text-gray-700">{{ number_format($avg, 1) }}</span>
                                    <span class="text-xs text-gray-400">({{ $rcnt }})</span>
                                </div>
                            @else
                                <p class="text-xs text-gray-300 italic mb-3">Belum ada ulasan</p>
                            @endif

                            <div class="spacer"></div>

                            @if ($mine)
                                @if ($mine->status === 'pending')
                                    <button disabled class="btn-pinjam"
                                        style="background:#fef3c7;color:#92400e;cursor:not-allowed">
                                        <i class="fas fa-hourglass-half text-xs"></i> Menunggu ACC Admin
                                    </button>
                                @elseif($mine->status === 'return_requested')
                                    <button disabled class="btn-pinjam"
                                        style="background:#f3e8ff;color:#7c3aed;cursor:not-allowed">
                                        <i class="fas fa-clock text-xs"></i> Proses Pengembalian
                                    </button>
                                @else
                                    <button disabled class="btn-pinjam mine">
                                        <i class="fas fa-check-circle text-xs"></i> Sedang Dipinjam
                                    </button>
                                @endif
                            @elseif($ok)
                                <button class="btn-pinjam avail" onclick="openBorrow({{ $book->id }},'{{ addslashes($book->title) }}','{{ addslashes($book->author) }}','{{ addslashes($book->publisher ?? '') }}',{{ $book->stock }},'{{ $book->cover ? asset('storage/' . $book->cover) : '' }}')">
    <i class="fas fa-bookmark text-xs"></i> Pinjam Buku
</button>
                            @else
                                <button disabled class="btn-pinjam out">
                                    <i class="fas fa-times text-xs"></i> Stok Habis
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-2xl border border-gray-100 py-24 text-center shadow-sm">
                <i class="fas fa-search text-5xl text-gray-200 mb-4 block"></i>
                <p class="text-gray-600 font-semibold">Tidak ada buku ditemukan</p>
                <p class="text-sm text-gray-400 mt-1">Coba ubah kata kunci atau filter</p>
                <a href="{{ route('student.books.index') }}"
                    class="mt-4 inline-block bg-evergreen-600 hover:bg-evergreen-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition">
                    Lihat Semua
                </a>
            </div>
        @endif
    </div>

    {{-- ══ LIST ════════════════════════════════════════════════════ --}}
    <div id="vw-list"
        class="{{ $view !== 'list' ? 'hidden' : '' }} bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-8">
        @forelse($books as $i => $book)
            @php
                $avg = round($book->reviews->avg('rating') ?? 0, 1);
                $rcnt = $book->reviews->count();
                $isWL = in_array($book->id, $wishlistedIds);
                $ok = $book->stock > 0;
                $mine = \App\Models\Borrowing::where('user_id', $userId)
                    ->where('book_id', $book->id)
                    ->whereIn('status', ['pending', 'borrowed', 'late', 'return_requested'])
                    ->first();
                $grad = $gradients[$i % count($gradients)];
            @endphp
            <div class="list-row">
                <a href="{{ route('student.books.show', $book->id) }}" class="flex-shrink-0">
                    <div class="w-10 rounded-lg overflow-hidden" style="height:52px">
                        @if ($book->cover)
                            <img src="{{ asset('storage/' . $book->cover) }}" class="w-full h-full object-cover"
                                loading="lazy" alt="">
                        @else
                            <div
                                class="w-full h-full bg-gradient-to-b {{ $grad }} flex items-center justify-center">
                                <i class="fas fa-book text-white text-sm"></i>
                            </div>
                        @endif
                    </div>
                </a>
                <div class="flex-1 min-w-0">
                    <p class="cat-tag">{{ $book->category->name ?? '' }}</p>
                    <a href="{{ route('student.books.show', $book->id) }}">
                        <p class="text-sm font-bold text-gray-900 truncate hover:text-evergreen-700 transition">
                            {{ $book->title }}</p>
                    </a>
                    <p class="text-xs text-gray-400 truncate">{{ $book->author }}</p>
                    @if ($rcnt > 0)
                        <div class="flex items-center gap-1 mt-0.5">
                            <span class="stars-row text-xs">★</span>
                            <span class="text-xs font-bold text-gray-700">{{ number_format($avg, 1) }}</span>
                            <span class="text-xs text-gray-400">({{ $rcnt }})</span>
                        </div>
                    @endif
                </div>
                <div class="hidden sm:flex">
                    @if ($mine)
                        <span class="text-xs font-bold bg-blue-50 text-blue-600 px-3 py-1 rounded-full">Dipinjam</span>
                    @elseif($ok)
                        <span class="text-xs font-bold bg-evergreen-50 text-evergreen-700 px-3 py-1 rounded-full">Tersedia
                            ({{ $book->stock }})
                        </span>
                    @else
                        <span class="text-xs font-bold bg-gray-100 text-gray-500 px-3 py-1 rounded-full">Habis</span>
                    @endif
                    {{-- @if ($mine)
                                @if ($mine->status === 'pending')
                                    <span class="cover-badge bg-amber-500/80 text-white">Menunggu Acc</span>
                                @elseif($mine->status === 'return_requested')
                                    <span class="cover-badge bg-purple-600/80 text-white">Proses Kembali</span>
                                @else
                                    <span class="cover-badge bg-blue-600/80 text-white">Dipinjam</span>
                                @endif
                            @elseif($ok)
                                <span class="cover-badge bg-evergreen-600/80 text-white">Tersedia({{ $book->stock }})</span>
                            @else
                                <span class="cover-badge bg-gray-600/80 text-white">Habis</span>
                            @endif --}}
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <button data-book-id="{{ $book->id }}" onclick="toggleWL(this, {{ $book->id }})"
                        class="heart-btn {{ $isWL ? 'on' : '' }}"
                        style="position:static;box-shadow:none;border-color:#f1f5f9">
                        <i class="{{ $isWL ? 'fas' : 'far' }} fa-heart" style="font-size:12px"></i>
                    </button>
                    @if ($mine)
                        @if ($mine->status === 'pending')
                            <button disabled class="btn-pinjam text-xs px-4 py-1.5 w-auto rounded-xl"
                                style="background:#fef3c7;color:#92400e;cursor:not-allowed">
                                <i class="fas fa-hourglass-half text-xs"></i> Menunggu ACC
                            </button>
                        @elseif($mine->status === 'return_requested')
                            <button disabled class="btn-pinjam text-xs px-4 py-1.5 w-auto rounded-xl"
                                style="background:#f3e8ff;color:#7c3aed;cursor:not-allowed">
                                Proses Kembali
                            </button>
                        @else
                            <button disabled
                                class="btn-pinjam mine text-xs px-4 py-1.5 w-auto rounded-xl">Dipinjam</button>
                        @endif
                    @elseif($ok)
                        <button class="btn-pinjam avail text-xs px-4 py-1.5 w-auto rounded-xl"
                            onclick="openBorrow({{ $book->id }},'{{ addslashes($book->title) }}','{{ addslashes($book->author) }}','{{ addslashes($book->publisher ?? '') }}',{{ $book->stock }},'{{ $book->cover ? asset('storage/' . $book->cover) : '' }}')">
                            <i class="fas fa-bookmark text-xs"></i> Pinjam
                        </button>
                    @else
                        <button disabled class="btn-pinjam out text-xs px-4 py-1.5 w-auto rounded-xl">Habis</button>
                    @endif
                </div>
            </div>
        @empty
            <div class="py-20 text-center">
                <i class="fas fa-search text-4xl text-gray-200 mb-3 block"></i>
                <p class="text-gray-500">Tidak ada buku</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($books->hasPages())
        <div class="flex justify-center gap-1 mb-6">
            @if ($books->onFirstPage())
                <span class="pg-btn dis"><i class="fas fa-chevron-left text-xs"></i></span>
            @else
                <a href="{{ $books->previousPageUrl() }}" class="pg-btn"><i class="fas fa-chevron-left text-xs"></i></a>
            @endif

            @foreach ($books->getUrlRange(1, $books->lastPage()) as $page => $url)
                @if ($page === $books->currentPage())
                    <span class="pg-btn cur">{{ $page }}</span>
                @elseif($page === 1 || $page === $books->lastPage() || abs($page - $books->currentPage()) <= 1)
                    <a href="{{ $url }}" class="pg-btn">{{ $page }}</a>
                @elseif(abs($page - $books->currentPage()) === 2)
                    <span class="pg-btn dis" style="border:none;color:#9ca3af">…</span>
                @endif
            @endforeach

            @if ($books->hasMorePages())
                <a href="{{ $books->nextPageUrl() }}" class="pg-btn"><i class="fas fa-chevron-right text-xs"></i></a>
            @else
                <span class="pg-btn dis"><i class="fas fa-chevron-right text-xs"></i></span>
            @endif
        </div>
    @endif

    {{-- ══ Borrow Modal ════════════════════════════════════════════ --}}
    <div id="borrow-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 bg-evergreen-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book-open text-white text-xs"></i>
                    </div>
                    <span class="font-bold text-sm text-gray-900">RUANG BACA</span>
                </div>
                <button onclick="closeBorrow()"
                    class="w-7 h-7 flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <div class="px-6 py-5">
                <h2 class="text-lg font-bold text-gray-900 mb-0.5">Konfirmasi Peminjaman</h2>
                <p class="text-sm text-gray-400 mb-5">Tinjau data sebelum melanjutkan.</p>
                <div class="bg-gray-50 rounded-xl p-4 mb-4 flex gap-3">
                    <div id="mc"
                        class="w-14 rounded-xl overflow-hidden flex-shrink-0 bg-evergreen-100 flex items-center justify-center"
                        style="height:72px">
                        <i class="fas fa-book text-evergreen-500 text-xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-evergreen-600 mb-0.5" id="mb">TERSEDIA</p>
                        <p class="font-bold text-gray-900 text-sm line-clamp-2" id="mt">—</p>
                        <p class="text-xs text-gray-400 mt-0.5" id="ma">—</p>
                        <p class="text-xs text-gray-300 mt-0.5" id="mp"></p>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4 mb-4">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Data Peminjam</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs text-gray-400">Nama</p>
                            <p class="text-sm font-bold text-gray-900">{{ auth()->user()->full_name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Kelas</p>
                            <p class="text-sm font-bold text-gray-900">{{ auth()->user()->class->name ?? '—' }}</p>
                        </div>
                    </div>
                </div>
                {{-- Masa Pinjam dari setting sekolah --}}
                <div class="bg-evergreen-50 border border-evergreen-100 rounded-xl p-4 mb-4">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Masa Pinjam</p>
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div>
                            <p class="text-xs text-gray-400 mb-1">PINJAM</p>
                            <p class="text-xs font-bold text-gray-900">{{ now()->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">KEMBALI</p>
                            <p class="text-xs font-bold text-gray-900">{{ $dueDate }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">DURASI</p>
                            <span class="bg-evergreen-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                                {{ $maxDays }} Hari
                            </span>
                        </div>
                    </div>
                </div>
                {{-- Denda dari setting sekolah --}}
                <div class="bg-amber-50 border border-amber-100 rounded-xl p-3 mb-5 flex gap-2">
                    <i class="fas fa-triangle-exclamation text-amber-500 text-sm mt-0.5 flex-shrink-0"></i>
                    <div class="text-xs text-amber-700 space-y-0.5">
                        <p>• Denda <strong>Rp {{ number_format($finePerDay, 0, ',', '.') }}/hari</strong> jika terlambat
                        </p>
                        <p>• Kerusakan buku wajib mengganti</p>
                        <p>• Maks. <strong>3 buku</strong> per siswa</p>
                    </div>
                </div>
                <form id="borrow-form" method="POST" action="">
                    @csrf
                    <div class="flex gap-3">
                        <button type="button" onclick="closeBorrow()"
                            class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 bg-evergreen-600 hover:bg-evergreen-700 text-white text-sm font-bold py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                            <i class="fas fa-check text-xs"></i> Konfirmasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function setCat(id) {
            document.getElementById('fi-cat').value = id;
            document.getElementById('ff').submit()
        }

        function setSort(v) {
            document.getElementById('fi-sort').value = v;
            document.getElementById('ff').submit()
        }

        function setView(v) {
            document.getElementById('fi-view').value = v;
            document.getElementById('vw-grid').classList.toggle('hidden', v !== 'grid');
            document.getElementById('vw-list').classList.toggle('hidden', v !== 'list');
            document.getElementById('vg').classList.toggle('on', v === 'grid');
            document.getElementById('vl').classList.toggle('on', v === 'list');
        }

        function openBorrow(id, title, author, pub, stock, cover) {
            document.getElementById('mt').textContent = title;
            document.getElementById('ma').textContent = author;
            document.getElementById('mp').textContent = pub ? `Penerbit: ${pub}` : '';
            document.getElementById('mb').textContent = `TERSEDIA • ${stock} EKS`;
            document.getElementById('borrow-form').action = `/student/borrow/${id}`;
            const c = document.getElementById('mc');
            c.innerHTML = cover ?
                `<img src="${cover}" class="w-full h-full object-cover" alt="">` :
                `<i class="fas fa-book text-evergreen-500 text-xl"></i>`;
            document.getElementById('borrow-modal').classList.replace('hidden', 'flex');
            document.body.style.overflow = 'hidden';
        }

        function closeBorrow() {
            document.getElementById('borrow-modal').classList.replace('flex', 'hidden');
            document.body.style.overflow = '';
        }
        document.getElementById('borrow-modal').addEventListener('click', e => {
            if (e.target === document.getElementById('borrow-modal')) closeBorrow()
        });

        /**
         * FIX: toggleWL sekarang update SEMUA button dengan data-book-id yang sama
         * supaya grid + list view tetap sync tanpa perlu reload
         */
        function toggleWL(btn, id) {
            fetch(`/student/wishlist/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(r => r.json())
                .then(d => {
                    if (!d.success) return;

                    document.querySelectorAll(`.heart-btn[data-book-id="${id}"]`).forEach(b => {
                        const icon = b.querySelector('i');

                        if (d.in_wishlist) {
                            b.classList.add('on');
                            icon.classList.replace('far', 'fas');
                        } else {
                            b.classList.remove('on');
                            icon.classList.replace('fas', 'far');
                        }
                    });
                });
        }

        function toast(msg, t = 'success') {
            const c = {
                success: 'bg-evergreen-600',
                info: 'bg-gray-600'
            };
            const el = document.createElement('div');
            el.className =
                `fixed bottom-6 left-1/2 -translate-x-1/2 z-[9999] ${c[t]} text-white text-sm font-semibold px-5 py-3 rounded-2xl shadow-xl flex items-center gap-2 transition-all duration-300 opacity-0 translate-y-2`;
            el.innerHTML = msg;
            document.body.appendChild(el);
            requestAnimationFrame(() => el.classList.remove('opacity-0', 'translate-y-2'));
            setTimeout(() => {
                el.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => el.remove(), 300)
            }, 2500);
        }

        document.addEventListener('click', e => {
            const w = document.getElementById('more-wrap');
            if (w && !w.contains(e.target)) document.getElementById('more-dd')?.classList.add('hidden');
        });
    </script>
@endpush
