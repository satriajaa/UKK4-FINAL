@extends('layouts.student')
@section('title', $book->title)

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

        .stars-fill {
            color: #facc15
        }

        .stars-empty {
            color: #e5e7eb
        }

        .related-card {
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
            border: 1px solid #f0f0f0;
            transition: transform .2s, box-shadow .2s;
            cursor: pointer
        }

        .related-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, .09)
        }

        .related-cover {
            position: relative;
            width: 100%;
            padding-top: 133%;
            overflow: hidden;
            background: #f3f4f6
        }

        /* FIX: img di atas gradient */
        .related-cover img,
.related-cover .cover-gradient {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
}

        .related-cover img {
            object-fit: cover;
            z-index: 1
        }

        .related-cover .cover-gradient {
            z-index: 0
        }

        .wish-top-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
            border: 1.5px solid #e5e7eb;
            color: #6b7280;
            background: #fff;
            transition: all .15s;
            cursor: pointer;
        }

        .wish-top-btn:hover,
        .wish-top-btn.on {
            border-color: #ef4444;
            color: #ef4444;
            background: #fff5f5
        }

        .wish-top-btn.on i {
            color: #ef4444
        }

        #review-modal {
            backdrop-filter: blur(4px)
        }
    </style>
@endpush

@section('content')
    @php
        $userId = auth()->id();
        $schoolId = auth()->user()->school_id;
        $avg = round($book->reviews->avg('rating') ?? 0, 1);
        $rcnt = $book->reviews->count();
        $gradients = [
            'from-teal-600 to-teal-800',
            'from-indigo-500 to-indigo-700',
            'from-emerald-500 to-emerald-700',
            'from-cyan-600 to-cyan-800',
            'from-blue-500 to-blue-700',
            'from-violet-500 to-violet-700',
        ];
        $grad = $gradients[$book->id % count($gradients)];

        // User's own review
$myReview = $book->reviews->where('user_id', $userId)->first();

// Setting sekolah
if (!isset($setting)) {
    $setting = \App\Models\Setting::getForSchool($schoolId);
}
$maxDays = $setting->max_borrow_days ?? 14;
$finePerDay = (int) ($setting->fine_per_day ?? 1000);
$dueDate = now()->addDays($maxDays)->format('d M Y');
    @endphp

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-gray-400 mb-6">
        <a href="{{ route('student.books.index') }}" class="hover:text-evergreen-600 transition font-medium">Koleksi</a>
        <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
        <a href="{{ route('student.books.index') }}" class="hover:text-evergreen-600 transition font-medium">Buku Siswa</a>
        <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
        <span class="text-gray-600 font-semibold truncate max-w-[200px]">{{ $book->title }}</span>
    </nav>

    {{-- ══ MAIN DETAIL ════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="flex flex-col md:flex-row gap-0">

            {{-- ── LEFT: Cover + buttons ── --}}
            <div
                class="md:w-64 flex-shrink-0 bg-gray-50 p-6 flex flex-col items-center gap-4 border-b md:border-b-0 md:border-r border-gray-100">

                {{-- Cover --}}
                <div class="w-full max-w-[180px]">
                    <div class="relative w-full rounded-xl overflow-hidden shadow-lg" style="padding-top:133%">
                        @if ($book->cover)
                            <img src="{{ asset('storage/' . $book->cover) }}"
                                class="absolute inset-0 w-full h-full object-cover" alt="{{ $book->title }}"
                                style="z-index:1">
                        @else
                            <div
                                class="absolute inset-0 bg-gradient-to-b {{ $grad }} flex flex-col items-center justify-end p-4">
                                <i class="fas fa-book text-white/30 text-4xl mb-3"></i>
                                <span
                                    class="text-white text-xs font-bold text-center leading-snug">{{ $book->title }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Action buttons --}}
                @if ($alreadyBorrow)
                    @php
                        $activeTrx = \App\Models\Borrowing::where('user_id', $userId)
                            ->where('book_id', $book->id)
                            ->whereIn('status', ['pending', 'borrowed', 'late', 'return_requested'])
                            ->first();
                    @endphp
                    <div class="w-full max-w-[180px]">
                        @if ($activeTrx?->status === 'pending')
                            <button disabled
                                class="w-full flex items-center justify-center gap-2 text-sm font-bold py-3 rounded-xl cursor-not-allowed"
                                style="background:#fef3c7;color:#92400e">
                                <i class="fas fa-hourglass-half animate-pulse"></i> Menunggu ACC
                            </button>
                            {{-- <p class="text-xs text-amber-600 text-center mt-2 font-medium">
                                Permintaan pinjammu sedang<br>ditinjau oleh pustakawan
                            </p> --}}
                        @elseif($activeTrx?->status === 'return_requested')
                            <button disabled
                                class="w-full flex items-center justify-center gap-2 text-sm font-bold py-3 rounded-xl cursor-not-allowed"
                                style="background:#f3e8ff;color:#7c3aed">
                                <i class="fas fa-clock animate-pulse"></i> Proses Pengembalian
                            </button>
                            <p class="text-xs text-purple-600 text-center mt-2 font-medium">
                                Pengembalian sedang<br>dikonfirmasi pustakawan
                            </p>
                        @else
                            <button disabled
                                class="w-full flex items-center justify-center gap-2 bg-blue-50 text-blue-600 text-sm font-bold py-3 rounded-xl cursor-not-allowed">
                                <i class="fas fa-check-circle"></i> Sedang Dipinjam
                            </button>
                        @endif
                    </div>
                @elseif($book->stock > 0)
                    <div class="w-full max-w-[180px]">
                        <button onclick="openBorrow()"
                            class="w-full flex items-center justify-center gap-2 bg-evergreen-600 hover:bg-evergreen-700 text-white text-sm font-bold py-3 rounded-xl transition shadow-sm">
                            <i class="fas fa-bookmark"></i> PINJAM BUKU
                        </button>
                    </div>
                @else
                    <div class="w-full max-w-[180px]">
                        <button disabled
                            class="w-full flex items-center justify-center gap-2 bg-gray-100 text-gray-400 text-sm font-bold py-3 rounded-xl cursor-not-allowed">
                            <i class="fas fa-times"></i> Stok Habis
                        </button>
                    </div>
                @endif

                <div class="w-full max-w-[180px]">
                    <a href="{{ route('student.books.index') }}"
                        class="w-full flex items-center justify-center gap-2 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-xl hover:bg-gray-50 transition">
                        <i class="fas fa-arrow-left text-xs"></i> Kembali ke Koleksi
                    </a>
                </div>

                {{-- Wishlist + share --}}
                <div class="flex gap-2 w-full max-w-[180px]">
                    <button id="wl-btn" onclick="toggleWL()"
                        class="wish-top-btn flex-1 justify-center {{ $isWishlisted ? 'on' : '' }}">
                        <i class="{{ $isWishlisted ? 'fas' : 'far' }} fa-heart text-xs"></i>
                        <span id="wl-label">{{ $isWishlisted ? 'Disimpan' : 'Simpan' }}</span>
                    </button>
                    <button onclick="copyLink()" class="wish-top-btn px-3" title="Salin tautan">
                        <i class="fas fa-share-alt text-xs"></i>
                    </button>
                </div>
            </div>

            {{-- ── RIGHT: Info ── --}}
            <div class="flex-1 p-6 md:p-8">

                {{-- Top row --}}
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div>
                        {{-- <span
                            class="inline-block text-xs font-bold bg-gray-100 text-gray-500 px-3 py-1 rounded-full mb-2">BUKU
                            SISWA</span> --}}
                        <h1 class="text-2xl md:text-3xl font-black text-gray-900 leading-tight mb-1">{{ $book->title }}
                        </h1>
                        <p class="text-base text-gray-500 font-medium">{{ $book->author }}</p>
                    </div>
                    <div class="flex-shrink-0 text-right">
                        @if ($book->stock > 0)
                            <div class="flex items-center gap-1.5 text-evergreen-600">
                                <i class="fas fa-check-circle text-sm"></i>
                                <span class="text-sm font-bold">Tersedia ({{ $book->stock }} Eks)</span>
                            </div>
                        @else
                            <div class="flex items-center gap-1.5 text-red-500">
                                <i class="fas fa-times-circle text-sm"></i>
                                <span class="text-sm font-bold">Stok Habis</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Rating box --}}
                @if ($rcnt > 0)
                    <div
                        class="inline-flex items-center gap-3 bg-amber-50 border border-amber-100 rounded-xl px-4 py-3 mb-6">
                        <div>
                            <div class="text-3xl font-black text-gray-900 leading-none">{{ number_format($avg, 1) }}</div>
                        </div>
                        <div>
                            <div class="stars-fill text-base">
                                @for ($s = 1; $s <= 5; $s++)
                                    {!! $s <= round($avg)
                                        ? '<i class="fas fa-star stars-fill"></i>'
                                        : '<i class="far fa-star stars-empty" style="color:#d1d5db"></i>' !!}
                                @endfor
                            </div>
                            <div class="text-xs text-gray-400 mt-0.5">Berdasarkan {{ $rcnt }} pembaca</div>
                        </div>
                    </div>
                @endif

                {{-- Meta grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-8 gap-y-4 mb-6 pb-6 border-b border-gray-100">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Penerbit</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $book->publisher ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Tahun Terbit</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $book->publication_year ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">ISBN</p>
                        <p class="text-sm font-semibold text-gray-800 font-mono">{{ $book->isbn ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Kategori</p>
                        <a href="{{ route('student.books.index', ['category' => $book->category_id]) }}"
                            class="text-sm font-semibold text-evergreen-600 hover:underline">{{ $book->category->name ?? '—' }}</a>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Lokasi Rak</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $book->shelf_location ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Bahasa</p>
                        <p class="text-sm font-semibold text-gray-800">Indonesia</p>
                    </div>
                </div>

                {{-- Synopsis --}}
                @if ($book->synopsis)
                    <div>
                        <h2 class="flex items-center gap-2 font-bold text-gray-900 text-base mb-3">
                            <span class="w-1 h-5 bg-evergreen-600 rounded-full inline-block"></span>
                            Sinopsis Buku
                        </h2>
                        <div class="text-sm text-gray-600 leading-relaxed space-y-3">
                            @foreach (explode("\n", $book->synopsis) as $para)
                                @if (trim($para))
                                    <p>{{ trim($para) }}</p>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ══ REVIEWS ════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-star text-yellow-400 text-sm"></i> Ulasan Pembaca
                @if ($rcnt > 0)
                    <span
                        class="bg-gray-100 text-gray-600 text-xs font-bold px-2 py-0.5 rounded-full">{{ $rcnt }}</span>
                @endif
            </h2>
            @if (!$myReview)
                <button onclick="document.getElementById('review-modal').classList.replace('hidden','flex')"
                    class="flex items-center gap-1.5 text-xs font-bold text-evergreen-600 border border-evergreen-200 px-3 py-1.5 rounded-xl hover:bg-evergreen-50 transition">
                    <i class="fas fa-pen text-xs"></i> Tulis Ulasan
                </button>
            @endif
        </div>

        @if ($rcnt > 0)
            <div class="divide-y divide-gray-50">
                @foreach ($book->reviews->take(5) as $rv)
                    <div class="px-6 py-4">
                        <div class="flex items-start gap-3">
                            <div
                                class="w-9 h-9 rounded-full bg-gradient-to-br from-evergreen-400 to-evergreen-700 flex items-center justify-center flex-shrink-0">
                                <span
                                    class="text-white text-xs font-bold">{{ strtoupper(substr($rv->user->full_name ?? '?', 0, 2)) }}</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-bold text-sm text-gray-900">{{ $rv->user->full_name ?? 'Anonim' }}</p>
                                    <span class="text-xs text-gray-300">{{ $rv->created_at?->format('d M Y') }}</span>
                                </div>
                                <div class="flex items-center gap-0.5 my-1">
                                    @for ($s = 1; $s <= 5; $s++)
                                        <i
                                            class="{{ $s <= $rv->rating ? 'fas' : 'far' }} fa-star text-xs {{ $s <= $rv->rating ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                                    @endfor
                                </div>
                                @if ($rv->comment)
                                    <p class="text-sm text-gray-600 leading-relaxed">{{ $rv->comment }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-12 text-center">
                <i class="fas fa-comment-alt text-4xl text-gray-200 mb-3 block"></i>
                <p class="text-gray-500 font-medium text-sm">Belum ada ulasan</p>
                <p class="text-xs text-gray-400 mt-1">Jadilah yang pertama memberikan ulasan!</p>
            </div>
        @endif
    </div>

    {{-- ══ RELATED BOOKS ══════════════════════════════════════════ --}}
    @if ($relatedBooks->count() > 0)
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-layer-group text-evergreen-500 text-sm"></i> Buku Terkait
                </h2>
                <a href="{{ route('student.books.index', ['category' => $book->category_id]) }}"
                    class="text-sm font-semibold text-evergreen-600 hover:underline">Lihat Semua →</a>
            </div>
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-4">
                @foreach ($relatedBooks as $i => $rb)
                    @php
                        $rgrad = [
                            'from-teal-600 to-teal-800',
                            'from-indigo-500 to-indigo-700',
                            'from-emerald-500 to-emerald-700',
                            'from-cyan-600 to-cyan-800',
                            'from-violet-500 to-violet-700',
                        ][$i % 5];
                        $rbWL = \App\Models\Wishlist::where('user_id', $userId)->where('book_id', $rb->id)->exists();
                    @endphp
                    <div class="related-card group"
                        onclick="window.location='{{ route('student.books.show', $rb->id) }}'">
                        <div class="related-cover">
                            @if ($rb->cover)
                                <img src="{{ asset('storage/' . $rb->cover) }}" alt="{{ $rb->title }}"
                                    loading="lazy">
                            @else
                                <div class="cover-gradient bg-gradient-to-b {{ $rgrad }} flex items-end p-3">
                                    <span
                                        class="text-white/90 text-xs font-semibold leading-snug line-clamp-3">{{ $rb->title }}</span>
                                </div>
                            @endif
                            {{-- wishlist star --}}
                            <button onclick="event.stopPropagation();toggleRelatedWL(this,{{ $rb->id }})"
                                class="absolute top-2 right-2 w-7 h-7 rounded-full bg-white/90 flex items-center justify-center border border-white/50 shadow-sm {{ $rbWL ? '' : 'opacity-0 group-hover:opacity-100' }} transition"
                                style="z-index:10">
                                <i class="{{ $rbWL ? 'fas text-yellow-400' : 'far text-gray-400' }} fa-star text-xs"></i>
                            </button>
                        </div>
                        <div class="p-2.5">
                            <p
                                class="text-xs font-bold text-gray-800 leading-snug line-clamp-2 group-hover:text-evergreen-700 transition">
                                {{ $rb->title }}</p>
                            <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $rb->author }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ══ Borrow Modal ════════════════════════════════════════════ --}}
    <div id="borrow-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4"
        style="backdrop-filter:blur(4px)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 bg-evergreen-600 rounded-lg flex items-center justify-center"><i
                            class="fas fa-book-open text-white text-xs"></i></div>
                    <span class="font-bold text-sm text-gray-900">RUANG BACA</span>
                </div>
                <button onclick="closeBorrow()"
                    class="w-7 h-7 flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 transition"><i
                        class="fas fa-times text-sm"></i></button>
            </div>
            <div class="px-6 py-5">
                <h2 class="text-lg font-bold text-gray-900 mb-0.5">Konfirmasi Peminjaman</h2>
                <p class="text-sm text-gray-400 mb-5">Pastikan data sudah benar sebelum konfirmasi.</p>

                <div class="bg-gray-50 rounded-xl p-4 mb-4 flex gap-3">
                    <div class="w-14 rounded-xl overflow-hidden flex-shrink-0" style="height:72px">
                        @if ($book->cover)
                            <img src="{{ asset('storage/' . $book->cover) }}" class="w-full h-full object-cover"
                                alt="">
                        @else
                            <div
                                class="w-full h-full bg-gradient-to-b {{ $grad }} flex items-center justify-center">
                                <i class="fas fa-book text-white text-xl"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-evergreen-600 mb-0.5">TERSEDIA • {{ $book->stock }} EKS</p>
                        <p class="font-bold text-gray-900 text-sm line-clamp-2">{{ $book->title }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $book->author }}</p>
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
                {{-- Tambahkan ini SETELAH blok denda (bg-amber-50), SEBELUM form/button --}}
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 mb-5 flex gap-2">
                    <i class="fas fa-info-circle text-blue-500 text-sm mt-0.5 flex-shrink-0"></i>
                    <div class="text-xs text-blue-700 space-y-0.5">
                        <p class="font-bold">Permintaan akan diproses oleh pustakawan</p>
                        <p>Setelah konfirmasi, pengajuanmu akan <strong>menunggu persetujuan admin</strong> sebelum buku
                            bisa diambil.</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('student.borrow', $book->id) }}">
                    @csrf
                    <div class="flex gap-3">
                        <button type="button" onclick="closeBorrow()"
                            class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-xl hover:bg-gray-50 transition">Batal</button>

                        <button type="submit"
                            class="flex-1 bg-evergreen-600 hover:bg-evergreen-700 text-white text-sm font-bold py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                            <i class="fas fa-paper-plane text-xs"></i> Ajukan Permintaan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ══ Review Modal ════════════════════════════════════════════ --}}
    <div id="review-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4"
        style="backdrop-filter:blur(4px)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">Tulis Ulasan</h3>
                <button onclick="document.getElementById('review-modal').classList.replace('flex','hidden')"
                    class="text-gray-400 hover:text-gray-600 transition"><i class="fas fa-times"></i></button>
            </div>
            <form method="POST" action="{{ route('student.books.review', $book->id) }}" class="px-6 py-5">
    @csrf
    <p class="text-sm text-gray-500 mb-4">Berikan penilaian untuk <strong>{{ $book->title }}</strong></p>

    {{-- Star picker --}}
    <div class="flex justify-center gap-2 mb-4" id="star-picker">
        @for ($s = 1; $s <= 5; $s++)
            <button type="button" data-star="{{ $s }}" onclick="setRating({{ $s }})"
                class="text-2xl text-gray-200 hover:text-yellow-400 transition">★</button>
        @endfor
    </div>

    <input type="hidden" name="rating" id="rating-val" value="" required>

    <textarea name="comment" rows="3" placeholder="Ceritakan pendapatmu tentang buku ini..."
        class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-evergreen-500 resize-none mb-4"></textarea>

    <button type="submit"
        class="w-full bg-evergreen-600 hover:bg-evergreen-700 text-white text-sm font-bold py-2.5 rounded-xl transition">
        Kirim Ulasan
    </button>
</form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Borrow modal
        function openBorrow() {
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
        document.getElementById('review-modal').addEventListener('click', e => {
            if (e.target === document.getElementById('review-modal'))
                document.getElementById('review-modal').classList.replace('flex', 'hidden')
        });

        // Wishlist toggle (main) — FIX: update label langsung tanpa reload
        const WL_ID = {{ $book->id }};

        function toggleWL() {
            const btn = document.getElementById('wl-btn');
            const on = btn.classList.contains('on');
            const icon = btn.querySelector('i');
            const label = document.getElementById('wl-label');
            fetch(`/student/wishlist/${WL_ID}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    _method: on ? 'DELETE' : 'POST'
                })
            }).then(r => r.json()).then(d => {
                if (!d.success) return;
                if (d.in_wishlist) {
                    btn.classList.add('on');
                    icon.classList.replace('far', 'fas');
                    label.textContent = 'Disimpan';
                } else {
                    btn.classList.remove('on');
                    icon.classList.replace('fas', 'far');
                    label.textContent = 'Simpan';
                }
            });
        }

        // Wishlist toggle (related books)
        function toggleRelatedWL(btn, id) {
            const isStar = btn.querySelector('i').classList.contains('fas');
            fetch(`/student/wishlist/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    _method: isStar ? 'DELETE' : 'POST'
                })
            }).then(r => r.json()).then(d => {
                if (!d.success) return;
                const icon = btn.querySelector('i');
                if (d.action === 'added') {
                    icon.classList.replace('far', 'fas');
                    icon.classList.replace('text-gray-400', 'text-yellow-400');
                    toast('❤️ Ditambahkan ke Wishlist!');
                } else {
                    icon.classList.replace('fas', 'far');
                    icon.classList.replace('text-yellow-400', 'text-gray-400');
                    toast('Dihapus dari Wishlist', 'info');
                }
            });
        }

        // Star rating
        function setRating(n) {
            document.getElementById('rating-val').value = n;
            document.querySelectorAll('#star-picker button').forEach((b, i) => {
                b.style.color = i < n ? '#facc15' : '#e5e7eb';
            });
        }

        // Copy link
        function copyLink() {
            navigator.clipboard.writeText(window.location.href).then(() => toast('Tautan disalin!', 'info'));
        }

        // Toast
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
