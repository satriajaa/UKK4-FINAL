@extends('layouts.student')
@section('title', 'Wishlist Saya')

@push('styles')
    <style>
        @keyframes heartPop {
            0% {
                transform: scale(1)
            }

            40% {
                transform: scale(1.5)
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

        @keyframes fadeOut {
            0% {
                opacity: 1;
                transform: scale(1)
            }

            100% {
                opacity: 0;
                transform: scale(.9)
            }
        }

        .fade-out {
            animation: fadeOut .3s ease forwards
        }

        .wl-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #f0f0f0;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .05);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform .2s, box-shadow .2s;
        }

        .wl-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, .09)
        }

        .cover-wrap {
            position: relative;
            width: 100%;
            padding-top: 133%;
            overflow: hidden;
            background: #f3f4f6;
            flex-shrink: 0
        }

                .cover-wrap img,
.cover-wrap .cover-gradient {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
}

        /* FIX: img di atas gradient */
        .cover-wrap img {
            object-fit: cover;
            transition: transform .45s;
            z-index: 1
        }

        .cover-gradient {
            z-index: 0
        }

        .wl-card:hover .cover-wrap img {
            transform: scale(1.06)
        }

        .remove-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            z-index: 10;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .92);
            border: 1px solid rgba(0, 0, 0, .06);
            box-shadow: 0 2px 6px rgba(0, 0, 0, .12);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background .15s, transform .15s;
        }

        .remove-btn:hover {
            background: #fff5f5;
            transform: scale(1.1)
        }

        .remove-btn i {
            font-size: 12px;
            color: #ef4444
        }

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
        }

        .stars-row {
            color: #facc15;
            font-size: 11px;
            letter-spacing: -1px
        }

        .cat-tag {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .07em;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 4px
        }

        .card-body {
            padding: 12px 14px 14px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0
        }

        .spacer {
            flex: 1
        }

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

        #borrow-modal {
            backdrop-filter: blur(4px)
        }
    </style>
@endpush

@section('content')
    @php
        $userId = auth()->id();
        $schoolId = auth()->user()->school_id;
        $wishlists = \App\Models\Wishlist::with(['book.category', 'book.reviews'])
            ->where('user_id', $userId)
            ->latest()
            ->get();
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

        // Ambil setting sekolah
        if (!isset($setting)) {
            $setting = \App\Models\Setting::getForSchool($schoolId);
        }
        $maxDays = $setting->max_borrow_days ?? 14;
        $finePerDay = (int) ($setting->fine_per_day ?? 1000);
        $dueDate = now()->addDays($maxDays)->format('d M Y');
    @endphp

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-heart text-red-500 text-xl"></i> Wishlist Saya
            </h1>
            <p class="text-sm text-gray-500 mt-0.5" id="wl-count-label">
                {{ $wishlists->count() }} buku tersimpan
            </p>
        </div>
        @if ($wishlists->count() > 0)
            <a href="{{ route('student.books.index') }}"
                class="flex items-center gap-2 text-sm font-semibold text-evergreen-600 border border-evergreen-200 px-4 py-2 rounded-xl hover:bg-evergreen-50 transition">
                <i class="fas fa-plus text-xs"></i> Tambah Buku
            </a>
        @endif
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div
            class="mb-4 flex items-center gap-2 bg-evergreen-50 border border-evergreen-200 text-evergreen-700 px-4 py-3 rounded-xl text-sm">
            <i class="fas fa-check-circle flex-shrink-0"></i>{{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            <i class="fas fa-exclamation-circle flex-shrink-0"></i>{{ session('error') }}
        </div>
    @endif

    @if ($wishlists->count() > 0)

        {{-- Stats strip --}}
        <div class="grid grid-cols-3 gap-3 mb-6">
            @php
                $availCount = $wishlists->filter(fn($w) => $w->book && $w->book->stock > 0)->count();
                $borrowedCount = $wishlists
                    ->filter(
                        fn($w) => $w->book &&
                            \App\Models\Borrowing::where('user_id', $userId)
                                ->where('book_id', $w->book->id)
                                ->whereIn('status', ['borrowed', 'late'])
                                ->exists(),
                    )
                    ->count();
            @endphp
            <div class="bg-white rounded-2xl border border-gray-100 p-4 text-center shadow-sm">
                <div class="text-2xl font-black text-gray-900" id="stat-total">{{ $wishlists->count() }}</div>
                <div class="text-xs text-gray-400 font-semibold uppercase tracking-wide mt-0.5">Total Simpan</div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-4 text-center shadow-sm">
                <div class="text-2xl font-black text-evergreen-600">{{ $availCount }}</div>
                <div class="text-xs text-gray-400 font-semibold uppercase tracking-wide mt-0.5">Tersedia</div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-4 text-center shadow-sm">
                <div class="text-2xl font-black text-blue-500">{{ $borrowedCount }}</div>
                <div class="text-xs text-gray-400 font-semibold uppercase tracking-wide mt-0.5">Dipinjam</div>
            </div>
        </div>

        {{-- Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-5 mb-8" id="wl-grid">
            @foreach ($wishlists as $i => $wl)
                @php
                    $book = $wl->book;
                    if (!$book) {
                        continue;
                    }
                    $avg = round($book->reviews->avg('rating') ?? 0, 1);
                    $rcnt = $book->reviews->count();
                    $ok = $book->stock > 0;
                    $mine = \App\Models\Borrowing::where('user_id', $userId)
                        ->where('book_id', $book->id)
                        ->whereIn('status', ['borrowed', 'late'])
                        ->exists();
                    $grad = $gradients[$i % count($gradients)];
                @endphp
                <div class="wl-card" id="wl-card-{{ $wl->id }}">
                    <div class="cover-wrap">
                        @if ($book->cover)
                            <img src="{{ asset('storage/' . $book->cover) }}" alt="{{ $book->title }}" loading="lazy">
                        @else
                            <div class="cover-gradient bg-gradient-to-b {{ $grad }} flex flex-col justify-end p-3">
                                <span
                                    class="text-white/90 text-xs font-bold leading-snug line-clamp-4">{{ $book->title }}</span>
                            </div>
                        @endif

                        {{-- Status badge --}}
                        @if ($mine)
                            <span class="cover-badge bg-blue-600/80 text-white">DIPINJAM</span>
                        @elseif($ok)
                            <span class="cover-badge bg-evergreen-600/80 text-white">TERSEDIA</span>
                        @else
                            <span class="cover-badge bg-gray-600/80 text-white">HABIS</span>
                        @endif

                        {{-- Remove from wishlist (red heart) --}}
                        <button class="remove-btn" onclick="removeWL(this,{{ $wl->id }},{{ $book->id }})"
                            title="Hapus dari Wishlist">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>

                    <div class="card-body">
                        <p class="cat-tag">{{ $book->category->name ?? '' }}</p>
                        <a href="{{ route('student.books.show', $book->id) }}">
                            <h3 class="text-sm font-bold text-gray-900 leading-snug line-clamp-2 hover:text-evergreen-700 transition mb-0.5"
                                style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">
                                {{ $book->title }}
                            </h3>
                        </a>
                        <p class="text-xs text-gray-400 truncate mb-2">{{ $book->author }}</p>

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
                            <button disabled class="btn-pinjam mine"><i class="fas fa-check-circle text-xs"></i> Sedang
                                Dipinjam</button>
                        @elseif($ok)
                            <button class="btn-pinjam avail"
                                onclick="openBorrow({{ $book->id }},'{{ addslashes($book->title) }}','{{ addslashes($book->author) }}','{{ addslashes($book->publisher ?? '') }}',{{ $book->stock }},'{{ $book->cover ? asset('storage/' . $book->cover) : '' }}')">
                                <i class="fas fa-bookmark text-xs"></i> Pinjam Buku
                            </button>
                        @else
                            <button disabled class="btn-pinjam out"><i class="fas fa-times text-xs"></i> Stok Habis</button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- ── Empty State ── --}}
        <div class="flex flex-col items-center justify-center py-24 bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mb-5">
                <i class="far fa-heart text-red-400 text-3xl"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Wishlist Kosong</h2>
            <p class="text-sm text-gray-500 text-center max-w-xs mb-6">
                Belum ada buku yang kamu simpan. Temukan buku menarik di katalog dan tap ikon ❤️ untuk menyimpannya!
            </p>
            <a href="{{ route('student.books.index') }}"
                class="flex items-center gap-2 bg-evergreen-600 hover:bg-evergreen-700 text-white font-bold px-6 py-3 rounded-xl transition shadow-sm">
                <i class="fas fa-search text-sm"></i> Jelajahi Katalog Buku
            </a>
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
                    class="w-7 h-7 flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 transition">
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
                            <span
                                class="bg-evergreen-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $maxDays }}
                                Hari</span>
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
                    </div>
                </div>
                <form id="borrow-form" method="POST" action="">
                    @csrf
                    <div class="flex gap-3">
                        <button type="button" onclick="closeBorrow()"
                            class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-xl hover:bg-gray-50 transition">Batal</button>
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
        // Remove from wishlist with animation
function removeWL(btn, wlId, bookId) {
    const card = document.getElementById(`wl-card-${wlId}`);

    fetch(`/student/wishlist/${bookId}`, {
        method: 'DELETE', // 🔥 LANGSUNG DELETE
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(r => r.json())
    .then(d => {
        if (!d.success) return;

        // animasi icon
        const icon = btn.querySelector('i');
        icon.classList.remove('fas');
        icon.classList.add('far');

        btn.classList.add('heart-pop');

        // animasi card
        card.classList.add('fade-out');

        card.addEventListener('animationend', () => {
            card.remove();

            const remaining = document.querySelectorAll('#wl-grid .wl-card').length;

            document.getElementById('wl-count-label').textContent =
                `${remaining} buku tersimpan`;

            document.getElementById('stat-total').textContent = remaining;

            if (remaining === 0) location.reload();
        }, { once: true });

        toast('Dihapus dari Wishlist', 'info');
    });
}

        // Borrow modal
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
    </script>
@endpush
