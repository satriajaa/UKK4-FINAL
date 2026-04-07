@extends('layouts.admin')

@section('title', 'Kelola Buku')
@section('page-title', 'Manajemen Inventaris Buku')
@section('page-subtitle', 'Kelola data judul, kategori, dan ketersediaan stok buku perpustakaan.')

@section('breadcrumb')
    <span class="text-gray-700 font-medium">Kelola Buku</span>
@endsection

@section('content')

    {{-- ── Tabs ───────────────────────────────────────────────── --}}
    <div class="flex gap-1 mb-5 border-b border-gray-200">
        <button onclick="switchTab('tab-books', this)"
            class="tab-btn px-5 py-2.5 text-sm font-bold border-b-2 border-evergreen-600 text-evergreen-600 -mb-px transition">
            Daftar Buku
        </button>
        <button onclick="switchTab('tab-categories', this)"
            class="tab-btn px-5 py-2.5 text-sm font-bold border-b-2 border-transparent text-gray-400 -mb-px transition hover:text-gray-700">
            Daftar Kategori
        </button>
    </div>

    {{-- ════════════ TAB: BUKU ════════════ --}}
    <div id="tab-books">
        {{-- Search + Filter + Add --}}
        <div class="flex flex-col sm:flex-row gap-3 mb-5">
            <form method="GET" action="{{ route('admin.books.index') }}" class="flex gap-3 flex-1">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari judul, penulis, atau ISBN..."
                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 focus:border-transparent outline-none">
                </div>
                <select name="category_id" onchange="this.form.submit()"
                    class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-600 focus:ring-2 focus:ring-evergreen-500 outline-none cursor-pointer">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </form>
            <button onclick="document.getElementById('modal-add-book').classList.remove('hidden')"
                class="flex items-center gap-2 bg-evergreen-600 hover:bg-evergreen-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition whitespace-nowrap">
                <i class="fas fa-plus"></i> Tambah Buku
            </button>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-5 py-3 text-left w-12">No</th>
                            <th class="px-5 py-3 text-left w-16">Sampul</th>
                            <th class="px-5 py-3 text-left">Judul Buku</th>
                            <th class="px-5 py-3 text-left">Penulis</th>
                            <th class="px-5 py-3 text-left">Kategori</th>
                            <th class="px-5 py-3 text-left">Stok</th>
                            <th class="px-5 py-3 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($books as $i => $book)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-5 py-3.5 font-bold text-gray-400 text-xs">
                                    {{ str_pad($books->firstItem() + $i, 2, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($book->cover)
                                        <img src="{{ asset('storage/' . $book->cover) }}" alt="{{ $book->title }}"
                                            class="w-9 h-12 object-cover rounded-lg shadow-sm">
                                    @else
                                        <div
                                            class="w-9 h-12 bg-gradient-to-br from-evergreen-200 to-evergreen-400 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-book text-white text-xs"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="font-semibold text-gray-900">{{ $book->title }}</div>
                                    <div class="text-xs text-gray-400">ISBN: {{ $book->isbn ?? '—' }}</div>
                                </td>
                                <td class="px-5 py-3.5 text-gray-600">{{ $book->author }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="bg-gray-100 text-gray-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                        {{ $book->category->name ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="font-semibold text-gray-900">{{ $book->stock }} Unit</div>
                                    @if ($book->stock > 0)
                                        <span
                                            class="text-xs font-bold text-evergreen-600 bg-evergreen-50 px-2 py-0.5 rounded-full">TERSEDIA</span>
                                    @else
                                        <span
                                            class="text-xs font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-full">HABIS</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button onclick='openEditModal(@json($book))'
                                            class="w-8 h-8 flex items-center justify-center bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white rounded-lg transition text-sm"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="{{ route('admin.books.destroy', $book) }}"
                                            onsubmit="return confirm('Hapus buku \'{{ $book->title }}\'?')">
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
                                <td colspan="7" class="px-5 py-16 text-center text-gray-400">
                                    <i class="fas fa-books text-4xl mb-3 block text-gray-200"></i>
                                    <div class="font-medium">Belum ada buku</div>
                                    <p class="text-sm mt-1">Tambahkan buku pertama perpustakaan Anda</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($books->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between">
                    <span class="text-sm text-gray-500">
                        Menampilkan <strong>{{ $books->firstItem() }}–{{ $books->lastItem() }}</strong> dari
                        <strong>{{ $books->total() }}</strong> buku
                    </span>
                    {{ $books->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- ════════════ TAB: KATEGORI ════════════ --}}
    <div id="tab-categories" class="hidden">
        <div class="flex justify-between gap-3 mb-5">
            <div class="relative flex-1 max-w-xs">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" placeholder="Cari kategori..."
                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
            </div>
            <button onclick="document.getElementById('modal-add-cat').classList.remove('hidden')"
                class="flex items-center gap-2 bg-evergreen-600 hover:bg-evergreen-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition whitespace-nowrap">
                <i class="fas fa-plus"></i> Tambah Kategori
            </button>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-5 py-3 text-left w-12">No</th>
                        <th class="px-5 py-3 text-left w-20">Kode</th>
                        <th class="px-5 py-3 text-left">Nama Kategori</th>
                        <th class="px-5 py-3 text-left">Jumlah Buku</th>
                        <th class="px-5 py-3 text-left">Deskripsi</th>
                        <th class="px-5 py-3 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($categories as $i => $cat)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-3.5 text-xs font-bold text-gray-400">
                                {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-5 py-3.5">
                                <span
                                    class="bg-evergreen-100 text-evergreen-700 text-xs font-bold px-2.5 py-1 rounded-full uppercase">
                                    {{ $cat->code ?? strtoupper(substr($cat->name, 0, 3)) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-gray-900">{{ $cat->name }}</td>
                            <td class="px-5 py-3.5 text-gray-600">
                                <strong>{{ $cat->books_count ?? $cat->books->count() }}</strong> buku
                            </td>
                            <td class="px-5 py-3.5 text-gray-500 text-xs">{{ $cat->description ?? '—' }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button onclick='openEditCategoryModal(@json($cat))'
                                        class="w-8 h-8 flex items-center justify-center bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white rounded-lg transition text-sm">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST"
                                        onsubmit="return confirm('Hapus kategori ini?')">
                                        @csrf @method('DELETE')
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
                            <td colspan="6" class="py-16 text-center text-gray-400">Belum ada kategori</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ════════════ MODAL: TAMBAH BUKU ════════════ --}}
    {{-- <div id="modal-add-book" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">Tambah Buku Baru</h3>
                <button onclick="document.getElementById('modal-add-book').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.books.store') }}" enctype="multipart/form-data"
                class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Judul Buku <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="title" required placeholder="Masukkan judul buku"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Penulis</label>
                        <input type="text" name="author" placeholder="Nama penulis"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Penerbit</label>
                        <input type="text" name="publisher" placeholder="Nama penerbit"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">ISBN</label>
                        <input type="text" name="isbn" placeholder="978-xxx-xxx-xxx-x"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tahun Terbit</label>
                        <input type="number" name="publication_year" placeholder="{{ date('Y') }}" min="1900"
                            max="{{ date('Y') }}"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kategori <span
                                class="text-red-500">*</span></label>
                        <select name="category_id" required
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Stok <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="stock" required min="0" placeholder="0"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Lokasi Rak</label>
                    <input type="text" name="shelf_location" placeholder="Contoh: A-12"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Sampul Buku</label>
                    <input type="file" name="cover" accept="image/*"
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-evergreen-50 file:text-evergreen-700 file:font-semibold hover:file:bg-evergreen-100">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Sinopsis</label>
                    <textarea name="synopsis" rows="3" placeholder="Deskripsi singkat buku..."
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none resize-none"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-add-book').classList.add('hidden')"
                        class="flex-1 py-2.5 border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition text-sm">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-[2] py-2.5 bg-evergreen-600 hover:bg-evergreen-700 text-white font-bold rounded-xl transition text-sm">
                        Simpan Buku
                    </button>
                </div>
            </form>
        </div>
    </div> --}}
    {{-- ════════════ MODAL: TAMBAH BUKU ════════════ --}}
    <div id="modal-add-book" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white z-10">
                <h3 class="text-lg font-bold text-gray-900">Tambah Buku Baru</h3>
                <button onclick="document.getElementById('modal-add-book').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.books.store') }}" enctype="multipart/form-data"
                class="px-6 py-5 space-y-4">
                @csrf

                {{-- SAMPUL BUKU - DI PALING ATAS DENGAN PREVIEW --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Sampul Buku</label>
                    <div class="flex items-start gap-4">
                        {{-- Preview Image --}}
                        <div class="flex-shrink-0">
                            <div id="cover-preview-container"
                                class="w-24 h-32 bg-gray-100 rounded-xl border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden">
                                <img id="cover-preview" src="#" alt="Preview"
                                    class="hidden w-full h-full object-cover">
                                <i id="cover-preview-icon" class="fas fa-image text-3xl text-gray-400"></i>
                            </div>
                        </div>

                        {{-- Upload Button --}}
                        <div class="flex-1">
                            <div class="relative">
                                <input type="file" name="cover" id="cover-input" accept="image/*"
                                    onchange="previewCover(this)"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                <button type="button" onclick="document.getElementById('cover-input').click()"
                                    class="w-full px-4 py-2.5 bg-evergreen-50 hover:bg-evergreen-100 text-evergreen-700 font-semibold rounded-xl transition text-sm border-2 border-dashed border-evergreen-200">
                                    <i class="fas fa-upload mr-2"></i>Pilih Gambar
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Format: JPG, PNG, GIF. Maksimal 2MB</p>
                            <button type="button" onclick="removeCover()"
                                class="text-xs text-red-600 hover:text-red-800 mt-1 hidden" id="remove-cover-btn">
                                <i class="fas fa-trash mr-1"></i>Hapus gambar
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Judul Buku --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Judul Buku <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="title" required placeholder="Masukkan judul buku"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                </div>

                {{-- Penulis & Penerbit --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Penulis</label>
                        <input type="text" name="author" placeholder="Nama penulis"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Penerbit</label>
                        <input type="text" name="publisher" placeholder="Nama penerbit"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    </div>
                </div>

                {{-- ISBN & Tahun Terbit --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">ISBN</label>
                        <input type="text" name="isbn" placeholder="978-xxx-xxx-xxx-x"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tahun Terbit</label>
                        <input type="number" name="publication_year" placeholder="{{ date('Y') }}" min="1900"
                            max="{{ date('Y') }}"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    </div>
                </div>

                {{-- Kategori --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kategori <span
                            class="text-red-500">*</span></label>
                    <select name="category_id" required
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Stok & Lokasi Rak --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Stok <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="stock" required min="0" placeholder="0"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Lokasi Rak</label>
                        <input type="text" name="shelf_location" placeholder="Contoh: A-12"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    </div>
                </div>

                {{-- Sinopsis --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Sinopsis</label>
                    <textarea name="synopsis" rows="3" placeholder="Deskripsi singkat buku..."
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none resize-none"></textarea>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-add-book').classList.add('hidden')"
                        class="flex-1 py-2.5 border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition text-sm">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-[2] py-2.5 bg-evergreen-600 hover:bg-evergreen-700 text-white font-bold rounded-xl transition text-sm">
                        Simpan Buku
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- ════════════ MODAL: EDIT BUKU (FIXED) ════════════ --}}
    <div id="modal-edit-book" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white z-10">
                <h3 class="text-lg font-bold text-gray-900">Edit Data Buku</h3>
                <button onclick="document.getElementById('modal-edit-book').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="form-edit-book" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-4">
                @csrf
                @method('PUT')

                {{-- SAMPUL BUKU - DENGAN PREVIEW (FIXED) --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Sampul Buku</label>
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <div id="edit-cover-preview-container"
                                class="w-24 h-32 bg-gray-100 rounded-xl border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden">
                                {{-- img akan diisi via JS --}}
                                <img id="edit-cover-preview" src="#" alt="Preview"
                                    class="hidden w-full h-full object-cover">
                                <i id="edit-cover-preview-icon" class="fas fa-image text-3xl text-gray-400"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="relative">
                                <input type="file" name="cover" id="edit-cover-input" accept="image/*"
                                    onchange="previewEditCover(this)"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                <button type="button" onclick="document.getElementById('edit-cover-input').click()"
                                    class="w-full px-4 py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold rounded-xl transition text-sm border-2 border-dashed border-blue-200">
                                    <i class="fas fa-upload mr-2"></i>Ganti Sampul
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Kosongkan jika tidak ingin mengubah sampul</p>
                            <button type="button" onclick="removeEditCover()"
                                class="text-xs text-red-600 hover:text-red-800 mt-1 hidden" id="remove-edit-cover-btn">
                                <i class="fas fa-trash mr-1"></i>Hapus preview
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Judul --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Judul Buku <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="title" id="edit-title" required
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                </div>

                {{-- Penulis & Penerbit --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Penulis</label>
                        <input type="text" name="author" id="edit-author"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Penerbit</label>
                        <input type="text" name="publisher" id="edit-publisher"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    </div>
                </div>

                {{-- ISBN & Tahun Terbit --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">ISBN</label>
                        <input type="text" name="isbn" id="edit-isbn"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tahun Terbit</label>
                        <input type="number" name="publication_year" id="edit-publication-year"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    </div>
                </div>

                {{-- Kategori --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
    <select name="category_id" id="edit-category-id" required
        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
        <option value="">Pilih Kategori</option>
        @foreach ($categories as $cat)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
        @endforeach
    </select>
</div>

                {{-- Stok & Lokasi Rak --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Stok <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="stock" id="edit-stock" required min="0"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Lokasi Rak</label>
                        <input type="text" name="shelf_location" id="edit-shelf-location"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    </div>
                </div>

                {{-- Sinopsis --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Sinopsis</label>
                    <textarea name="synopsis" id="edit-synopsis" rows="3"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none resize-none"></textarea>
                </div>

                {{-- Tombol --}}
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-edit-book').classList.add('hidden')"
                        class="flex-1 py-2.5 border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 text-sm">Batal</button>
                    <button type="submit"
                        class="flex-[2] py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-sm">Update
                        Data</button>
                </div>
            </form>
        </div>
    </div>


    {{-- ════════════ MODAL: TAMBAH KATEGORI ════════════ --}}
    <div id="modal-add-cat" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">Tambah Kategori Baru</h3>
                <button onclick="document.getElementById('modal-add-cat').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.categories.store') }}" class="px-6 py-5 space-y-4">
                @csrf
                <input type="hidden" name="active_tab" value="tab-categories">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Kategori <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="Masukkan nama kategori"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kode</label>
                    <input type="text" name="code" placeholder="Contoh: TEK" maxlength="5"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none uppercase">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi</label>
                    <textarea name="description" rows="3" placeholder="Deskripsi kategori..."
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none resize-none"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-add-cat').classList.add('hidden')"
                        class="flex-1 py-2.5 border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition text-sm">Batal</button>
                    <button type="submit"
                        class="flex-[2] py-2.5 bg-evergreen-600 hover:bg-evergreen-700 text-white font-bold rounded-xl transition text-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // ── Restore active tab dari session ────────────────────────
            @if (session('last_tab'))
                const activeTabId = "{{ session('last_tab') }}";
                const tabButton = document.querySelector(`button[onclick*='${activeTabId}']`);
                if (tabButton) switchTab(activeTabId, tabButton);
            @endif

            // ── Close modals on backdrop click ─────────────────────────
            ['modal-add-book', 'modal-add-cat', 'modal-edit-book'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.addEventListener('click', function(e) {
                    if (e.target === this) this.classList.add('hidden');
                });
            });
        });

        // ── Tab Switching ───────────────────────────────────────────
        function switchTab(id, btn) {
            ['tab-books', 'tab-categories'].forEach(t => document.getElementById(t).classList.add('hidden'));
            document.getElementById(id).classList.remove('hidden');
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('border-evergreen-600', 'text-evergreen-600');
                b.classList.add('border-transparent', 'text-gray-400');
            });
            btn.classList.add('border-evergreen-600', 'text-evergreen-600');
            btn.classList.remove('border-transparent', 'text-gray-400');
        }

        // ── Preview Sampul: Modal TAMBAH ────────────────────────────
        function previewCover(input) {
            const preview = document.getElementById('cover-preview');
            const icon = document.getElementById('cover-preview-icon');
            const removeBtn = document.getElementById('remove-cover-btn');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    icon.classList.add('hidden');
                    removeBtn.classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeCover() {
            document.getElementById('cover-input').value = '';
            document.getElementById('cover-preview').src = '#';
            document.getElementById('cover-preview').classList.add('hidden');
            document.getElementById('cover-preview-icon').classList.remove('hidden');
            document.getElementById('remove-cover-btn').classList.add('hidden');
        }

        // ── Preview Sampul: Modal EDIT ──────────────────────────────
        function previewEditCover(input) {
            const preview = document.getElementById('edit-cover-preview');
            const icon = document.getElementById('edit-cover-preview-icon');
            const removeBtn = document.getElementById('remove-edit-cover-btn');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    icon.classList.add('hidden');
                    removeBtn.classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeEditCover() {
            document.getElementById('edit-cover-input').value = '';
            const preview = document.getElementById('edit-cover-preview');
            preview.src = '#';
            preview.classList.add('hidden');
            document.getElementById('edit-cover-preview-icon').classList.remove('hidden');
            document.getElementById('remove-edit-cover-btn').classList.add('hidden');
        }

        // ── Buka Modal Edit Buku ────────────────────────────────────
        function openEditModal(book) {
            const modal = document.getElementById('modal-edit-book');
            const form = document.getElementById('form-edit-book');

            // Set action URL
            form.action = `/admin/books/${book.id}`;

            // Isi field teks
            document.getElementById('edit-title').value = book.title ?? '';
            document.getElementById('edit-author').value = book.author ?? '';
            document.getElementById('edit-publisher').value = book.publisher ?? '';
            document.getElementById('edit-isbn').value = book.isbn ?? '';
            document.getElementById('edit-publication-year').value = book.publication_year ?? '';
            document.getElementById('edit-stock').value = book.stock ?? 0;
            document.getElementById('edit-shelf-location').value = book.shelf_location ?? '';
            document.getElementById('edit-synopsis').value = book.synopsis ?? '';

            // ── PREVIEW SAMPUL yang sudah ada ──────────────────────
            const preview = document.getElementById('edit-cover-preview');
            const icon = document.getElementById('edit-cover-preview-icon');
            const removeBtn = document.getElementById('remove-edit-cover-btn');
            // Reset dulu
            preview.classList.add('hidden');
            icon.classList.remove('hidden');
            removeBtn.classList.add('hidden');
            document.getElementById('edit-cover-input').value = '';

            if (book.cover) {
                // book.cover = path relatif, mis: "covers/1/abc.jpg"
                preview.src = `/storage/${book.cover}`;
                preview.classList.remove('hidden');
                icon.classList.add('hidden');
            }

            document.getElementById('edit-category-id').value = book.category_id ?? '';
            // // ── CHECKBOX KATEGORI: centang sesuai category_id buku ─
            // document.querySelectorAll('.edit-category-checkbox').forEach(cb => {
            //     // Centang checkbox yang sesuai dengan category_id buku saat ini
            //     cb.checked = (parseInt(cb.value) === parseInt(book.category_id));
            // });

            modal.classList.remove('hidden');
        }

        // ── Buka Modal Edit Kategori ────────────────────────────────
        function openEditCategoryModal(cat) {
            const modal = document.getElementById('modal-add-cat');
            const form = modal.querySelector('form');
            modal.querySelector('h3').innerText = 'Edit Kategori';

            form.action = `/admin/categories/${cat.id}`;

            let methodInput = form.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                form.appendChild(methodInput);
            } else {
                methodInput.value = 'PUT';
            }

            form.querySelector('input[name="name"]').value = cat.name ?? '';
            form.querySelector('input[name="code"]').value = cat.code ?? '';
            form.querySelector('textarea[name="description"]').value = cat.description ?? '';

            modal.classList.remove('hidden');
        }
    </script>
@endpush
