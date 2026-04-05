@extends('layouts.admin')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')
@section('page-subtitle', 'Kelola profil, informasi perpustakaan, dan keamanan akun.')

@section('breadcrumb')
    <span class="text-gray-700 font-medium">Pengaturan</span>
@endsection

@section('content')
    @php $activeTab = session('active_tab', 'profile'); @endphp

    {{-- ── Tab Navigation ───────────────────────────────────── --}}
    <div class="flex gap-1 mb-6 border-b border-gray-200">
        @foreach ([['id' => 'profile', 'icon' => 'fa-user-circle', 'label' => 'Profil Admin'], ['id' => 'app', 'icon' => 'fa-cog', 'label' => 'Pengaturan Aplikasi'], ['id' => 'password', 'icon' => 'fa-lock', 'label' => 'Ganti Password']] as $tab)
            <button onclick="switchTab('{{ $tab['id'] }}', this)" id="tab-btn-{{ $tab['id'] }}"
                class="tab-btn flex items-center gap-2 px-5 py-3 text-sm font-semibold border-b-2 -mb-px transition
               {{ $activeTab === $tab['id']
                   ? 'border-evergreen-600 text-evergreen-600'
                   : 'border-transparent text-gray-400 hover:text-gray-700' }}">
                <i class="fas {{ $tab['icon'] }} text-sm"></i>
                <span>{{ $tab['label'] }}</span>
            </button>
        @endforeach
    </div>

    {{-- ══════════════════════════════════════
     TAB 1 — PROFIL ADMIN
══════════════════════════════════════ --}}
    <div id="tab-profile" class="{{ $activeTab !== 'profile' ? 'hidden' : '' }} space-y-5 max-w-3xl">

        {{-- Foto Profil --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="font-bold text-gray-900 flex items-center gap-2 mb-5 text-sm">
                <i class="fas fa-camera text-evergreen-500"></i> Foto Profil
            </h3>
            <div class="flex items-start gap-5">
                {{-- Avatar --}}
                <div class="flex-shrink-0">
                    @if (auth()->user()->photo)
                        <img src="{{ asset('storage/' . auth()->user()->photo) }}" id="photo-preview"
                            class="w-20 h-20 rounded-full object-cover ring-4 ring-evergreen-100 shadow">
                    @else
                        <div id="photo-preview"
                            class="w-20 h-20 rounded-full ring-4 ring-evergreen-100 shadow flex items-center justify-center text-xl font-bold text-white"
                            style="background: linear-gradient(135deg,#22c55e,#15803d)">
                            {{ strtoupper(substr(auth()->user()->full_name, 0, 2)) }}
                        </div>
                    @endif
                </div>

                {{-- Upload Zone --}}
                <form method="POST" action="{{ route('admin.settings.profile') }}" enctype="multipart/form-data"
                    class="flex-1">
                    @csrf @method('PATCH')
                    {{-- keep other profile fields unchanged --}}
                    <input type="hidden" name="full_name" value="{{ auth()->user()->full_name }}">
                    <input type="hidden" name="username" value="{{ auth()->user()->username }}">
                    <input type="hidden" name="email" value="{{ auth()->user()->email }}">

                    <div id="drop-zone"
                        class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-evergreen-400 hover:bg-evergreen-50/30 transition cursor-pointer"
                        onclick="document.getElementById('photo-input').click()"
                        ondragover="event.preventDefault();this.classList.add('border-evergreen-400','bg-evergreen-50')"
                        ondragleave="this.classList.remove('border-evergreen-400','bg-evergreen-50')"
                        ondrop="handleDrop(event)">
                        <i class="fas fa-cloud-upload-alt text-3xl text-evergreen-400 mb-2 block"></i>
                        <p class="text-sm font-semibold text-gray-700">Upload Foto Baru</p>
                        <p class="text-xs text-gray-400 mt-1">Seret dan lepas foto Anda ke sini, atau klik untuk memilih
                            file dari komputer (PNG, JPG, max. 2MB)</p>
                        <button type="button"
                            class="mt-3 px-5 py-2 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-gray-700 transition">
                            Pilih File
                        </button>
                        <input type="file" id="photo-input" name="photo" accept="image/*" class="hidden"
                            onchange="previewPhoto(this)">
                    </div>

                    <div id="photo-actions" class="hidden mt-3 flex gap-2">
                        <button type="submit"
                            class="px-5 py-2 bg-evergreen-600 hover:bg-evergreen-700 text-white text-sm font-bold rounded-xl transition">
                            <i class="fas fa-save mr-1"></i> Simpan Foto
                        </button>
                        <button type="button" onclick="cancelPhoto()"
                            class="px-5 py-2 border border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50 transition">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Informasi Pribadi --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="font-bold text-gray-900 flex items-center gap-2 mb-5 text-sm">
                <i class="fas fa-user text-evergreen-500"></i> Informasi Pribadi
            </h3>
            <form method="POST" action="{{ route('admin.settings.profile') }}" enctype="multipart/form-data">
                @csrf @method('PATCH')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Nama
                            Lengkap</label>
                        <input type="text" name="full_name" value="{{ old('full_name', auth()->user()->full_name) }}"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none
                                  @error('full_name') border-red-400 @enderror">
                        @error('full_name')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label
                            class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Username</label>
                        <div class="relative">
                            <span
                                class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">@</span>
                            <input type="text" name="username" value="{{ old('username', auth()->user()->username) }}"
                                class="w-full pl-8 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none
                                      @error('username') border-red-400 @enderror">
                        </div>
                        @error('username')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Alamat
                        Email</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none
                                  @error('email') border-red-400 @enderror">
                    </div>
                    @error('email')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- <div class="mb-5">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Bio Singkat</label>
                <textarea name="bio" rows="3"
                          placeholder="Tuliskan sedikit tentang peran Anda..."
                          class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none resize-none">{{ old('bio') }}</textarea>
            </div> --}}

                <div class="flex items-center justify-end gap-3">
                    <button type="reset"
                        class="px-5 py-2.5 border border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50 transition">
                        Batalkan
                    </button>
                    <button type="submit"
                        class="flex items-center gap-2 px-6 py-2.5 bg-evergreen-600 hover:bg-evergreen-700 text-white text-sm font-bold rounded-xl transition">
                        <i class="fas fa-save"></i> Simpan Profil
                    </button>
                </div>
            </form>
        </div>

        {{-- Kelola Admin Sekolah --}}
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900 flex items-center gap-2 text-sm">
                    <i class="fas fa-users-cog text-evergreen-500"></i> Kelola Admin Sekolah
                </h3>
                <button onclick="document.getElementById('modal-add-staff').classList.remove('hidden')"
                    class="flex items-center gap-1.5 bg-evergreen-600 hover:bg-evergreen-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition">
                    <i class="fas fa-plus"></i> Tambah Staf Admin
                </button>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        <th class="px-6 py-3 text-left">Nama</th>
                        <th class="px-6 py-3 text-left">Username</th>
                        <th class="px-6 py-3 text-left hidden sm:table-cell">Email</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($staffAdmins as $staff)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                                        style="background:hsl({{ crc32($staff->full_name) % 360 }},55%,52%)">
                                        {{ strtoupper(substr($staff->full_name, 0, 2)) }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $staff->full_name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 font-mono text-xs text-gray-500">{{ $staff->username }}</td>
                            <td class="px-6 py-3.5 text-xs text-gray-500 hidden sm:table-cell">{{ $staff->email ?? '—' }}
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-1.5">
                                    {{-- Tombol hapus — sekarang pakai route settings.staff.destroy --}}
                                    <form method="POST" action="{{ route('admin.settings.staff.destroy', $staff) }}"
                                        onsubmit="return confirm('Hapus admin {{ addslashes($staff->full_name) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="w-7 h-7 flex items-center justify-center bg-red-50 hover:bg-red-600 text-red-600 hover:text-white rounded-lg transition text-xs">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-400">
                                <i class="fas fa-users text-2xl mb-2 block text-gray-200"></i>
                                Belum ada staf admin lain
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Hapus Akun --}}
        <div class="bg-white rounded-2xl border border-red-100 p-6 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-gray-900 text-sm mb-0.5">Hapus Akun</h3>
                <p class="text-xs text-gray-500">Sekali Anda menghapus akun, aksi ini tidak dapat dibatalkan.</p>
            </div>
            <button
                onclick="if(confirm('Yakin ingin menonaktifkan akun ini? Hubungi Superadmin untuk tindakan lanjut.')) return false"
                class="px-5 py-2.5 border border-red-200 text-red-600 text-sm font-semibold rounded-xl hover:bg-red-50 transition whitespace-nowrap">
                Nonaktifkan Akun
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════
     TAB 2 — PENGATURAN APLIKASI
══════════════════════════════════════ --}}
    <div id="tab-app" class="{{ $activeTab !== 'app' ? 'hidden' : '' }} space-y-5 max-w-3xl">

        <div class="mb-2">
            <h2 class="text-xl font-bold text-gray-900">Pengaturan Aplikasi</h2>
            <p class="text-sm text-gray-500 mt-0.5">Kelola informasi perpustakaan dan kebijakan sistem di sini.</p>
        </div>

        <form method="POST" action="{{ route('admin.settings.app') }}" class="space-y-5">
            @csrf @method('PUT')

            {{-- Informasi Perpustakaan --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h3 class="font-bold text-gray-900 flex items-center gap-2 mb-5 text-sm">
                    <i class="fas fa-info-circle text-evergreen-500"></i> Informasi Perpustakaan
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    {{-- <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Nama Perpustakaan</label>
                    <input type="text" name="library_name"
                           value="{{ old('library_name', 'RUANG BACA '.($school->name ?? '')) }}"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                </div> --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Nama
                            Sekolah</label>
                        <input type="text" value="{{ $school->name ?? '' }}" disabled
                            class="w-full px-4 py-2.5 bg-gray-100 border border-gray-200 rounded-xl text-sm text-gray-400 cursor-not-allowed outline-none">
                        <p class="text-xs text-gray-400 mt-1">Hanya dapat diubah oleh Superadmin.</p>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Alamat
                        Lengkap</label>
                    <textarea name="address" rows="2"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none resize-none">{{ old('address', $school->address ?? '') }}</textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Nomor
                            Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone', $school->phone ?? '') }}"
                            placeholder="(021) 555-0192"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Email
                            Perpustakaan</label>
                        <input type="email" name="email" value="{{ old('email', $school->email ?? '') }}"
                            placeholder="info@ruangbaca.sch.id"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    </div>
                </div>
            </div>

            {{-- Pengaturan Sistem --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h3 class="font-bold text-gray-900 flex items-center gap-2 mb-5 text-sm">
                    <i class="fas fa-sliders-h text-evergreen-500"></i> Pengaturan Sistem
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Maksimal
                            Peminjaman (Hari)</label>
                        <div class="relative">
                            <input type="number" name="max_borrow_days" min="1" max="90"
                                value="{{ old('max_borrow_days', $setting->max_borrow_days ?? 14) }}"
                                class="w-full px-4 py-2.5 pr-14 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none
                                      @error('max_borrow_days') border-red-400 @enderror">
                            <span
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-semibold">Hari</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Batas waktu peminjaman buku per transaksi.</p>
                        @error('max_borrow_days')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Denda
                            Keterlambatan (Rp/Hari)</label>
                        <div class="relative">
                            <span
                                class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm font-semibold">Rp</span>
                            <input type="number" name="fine_per_day" min="0"
                                value="{{ old('fine_per_day', (int) ($setting->fine_per_day ?? 1000)) }}"
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none
                                      @error('fine_per_day') border-red-400 @enderror">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Denda yang dikenakan per hari per buku.</p>
                        @error('fine_per_day')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.settings') }}"
                    class="px-5 py-2.5 border border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50 transition">
                    Batalkan
                </a>
                <button type="submit"
                    class="flex items-center gap-2 px-6 py-2.5 bg-evergreen-600 hover:bg-evergreen-700 text-white text-sm font-bold rounded-xl transition">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- ══════════════════════════════════════
     TAB 3 — GANTI PASSWORD
══════════════════════════════════════ --}}
    <div id="tab-password" class="{{ $activeTab !== 'password' ? 'hidden' : '' }}">

        <div class="mb-5">
            <h2 class="text-xl font-bold text-gray-900">Ganti Password</h2>
            <p class="text-sm text-gray-500 mt-0.5">Pastikan akun Anda tetap aman dengan memperbarui kata sandi secara
                berkala menggunakan kombinasi yang kuat.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Form --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <form method="POST" action="{{ route('admin.settings.password') }}">
                        @csrf @method('PATCH')
                        <div class="space-y-5">

                            {{-- Password Lama --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password Lama</label>
                                <div class="relative">
                                    <i
                                        class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="password" name="current_password" id="pw-current"
                                        placeholder="Masukkan password saat ini"
                                        class="w-full pl-10 pr-12 py-3 bg-gray-50 border rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none
                                              @error('current_password') border-red-400 @else border-gray-200 @enderror">
                                    <button type="button" onclick="togglePw('pw-current','ico-cur')"
                                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                                        <i class="fas fa-eye text-sm" id="ico-cur"></i>
                                    </button>
                                </div>
                                @error('current_password')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Password Baru --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password Baru</label>
                                <div class="relative">
                                    <i
                                        class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="password" name="password" id="pw-new"
                                        placeholder="Masukkan password baru" oninput="checkStrength(this.value)"
                                        class="w-full pl-10 pr-12 py-3 bg-gray-50 border rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none
                                              @error('password') border-red-400 @else border-gray-200 @enderror">
                                    <button type="button" onclick="togglePw('pw-new','ico-new')"
                                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                                        <i class="fas fa-eye text-sm" id="ico-new"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Konfirmasi Password --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Konfirmasi Password
                                    Baru</label>
                                <div class="relative">
                                    <i
                                        class="fas fa-shield-alt absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="password" name="password_confirmation" id="pw-confirm"
                                        placeholder="Ulangi password baru"
                                        class="w-full pl-10 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                                    <button type="button" onclick="togglePw('pw-confirm','ico-conf')"
                                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                                        <i class="fas fa-eye text-sm" id="ico-conf"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit"
                                class="w-full flex items-center justify-center gap-2 py-3 bg-evergreen-600 hover:bg-evergreen-700 text-white text-sm font-bold rounded-xl transition">
                                <i class="fas fa-save"></i> Perbarui Password
                            </button>
                        </div>
                    </form>

                    <p class="mt-6 text-center text-xs text-gray-400">
                        &copy; {{ date('Y') }} RUANG BACA Library Management System. All rights reserved.
                    </p>
                </div>
            </div>

            {{-- Sidebar Kanan --}}
            <div class="space-y-4">

                {{-- Persyaratan --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <h4 class="font-bold text-gray-900 text-sm flex items-center gap-2 mb-4">
                        <span class="w-7 h-7 bg-evergreen-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-shield-alt text-evergreen-600 text-xs"></i>
                        </span>
                        Persyaratan Password
                    </h4>
                    <div class="space-y-2.5">
                        @foreach ([['id' => 'req-length', 'label' => 'Minimal 8 karakter'], ['id' => 'req-case', 'label' => 'Menggunakan huruf besar & kecil'], ['id' => 'req-number', 'label' => 'Minimal menyertakan satu angka'], ['id' => 'req-symbol', 'label' => 'Menyertakan simbol unik (!@#$%^*)']] as $req)
                            <div class="flex items-center gap-2.5" id="{{ $req['id'] }}">
                                <div
                                    class="w-4 h-4 rounded-full border-2 border-gray-200 flex items-center justify-center flex-shrink-0 req-dot transition-all">
                                    <i class="fas fa-check text-white hidden req-icon" style="font-size:8px"></i>
                                </div>
                                <span class="text-sm text-gray-500 req-label">{{ $req['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Pusat Bantuan --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 bg-evergreen-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-info-circle text-evergreen-600 text-sm"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 text-sm">Pusat Bantuan</h4>
                    </div>
                    <p class="text-sm text-gray-500 mb-3">Lupa password lama atau mengalami kendala teknis? Hubungi tim
                        support sistem.</p>
                    <a href="#"
                        class="text-sm font-semibold text-evergreen-600 hover:underline flex items-center gap-1">
                        Hubungi IT Support <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════
     MODAL: TAMBAH STAF ADMIN
══════════════════════════════════════ --}}
    <div id="modal-add-staff" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">Tambah Staf Admin</h3>
                <button onclick="document.getElementById('modal-add-staff').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            {{-- ACTION DIUBAH ke route settings.staff.store (bukan members.store) --}}
            <form method="POST" action="{{ route('admin.settings.staff.store') }}" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="full_name" required placeholder="Nama lengkap admin"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Username <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="username" required placeholder="username_admin"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password <span
                                class="text-red-500">*</span></label>
                        <input type="password" name="password" required placeholder="Min. 8 karakter"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" placeholder="email@sekolah.sch.id"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 outline-none">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-add-staff').classList.add('hidden')"
                        class="flex-1 py-2.5 border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition text-sm">Batal</button>
                    <button type="submit"
                        class="flex-[2] py-2.5 bg-evergreen-600 hover:bg-evergreen-700 text-white font-bold rounded-xl transition text-sm">Tambah
                        Admin</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // ── Tab switching ──────────────────────────────────────────────────────
        const tabIds = ['profile', 'app', 'password'];

        function switchTab(id, btn) {
            tabIds.forEach(t => {
                document.getElementById('tab-' + t).classList.add('hidden');
                const b = document.getElementById('tab-btn-' + t);
                b.classList.remove('border-evergreen-600', 'text-evergreen-600');
                b.classList.add('border-transparent', 'text-gray-400');
            });
            document.getElementById('tab-' + id).classList.remove('hidden');
            btn.classList.remove('border-transparent', 'text-gray-400');
            btn.classList.add('border-evergreen-600', 'text-evergreen-600');
        }

        // ── Password toggle ────────────────────────────────────────────────────
        function togglePw(fieldId, iconId) {
            const f = document.getElementById(fieldId);
            const i = document.getElementById(iconId);
            if (f.type === 'password') {
                f.type = 'text';
                i.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                f.type = 'password';
                i.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // ── Password strength checker ──────────────────────────────────────────
        function checkStrength(val) {
            const rules = {
                'req-length': val.length >= 8,
                'req-case': /[a-z]/.test(val) && /[A-Z]/.test(val),
                'req-number': /[0-9]/.test(val),
                'req-symbol': /[!@#$%^&*]/.test(val),
            };

            Object.entries(rules).forEach(([id, pass]) => {
                const row = document.getElementById(id);
                const dot = row.querySelector('.req-dot');
                const icon = row.querySelector('.req-icon');
                const lbl = row.querySelector('.req-label');

                if (pass) {
                    dot.classList.replace('border-gray-200', 'border-evergreen-500');
                    dot.classList.add('bg-evergreen-500');
                    icon.classList.remove('hidden');
                    lbl.classList.replace('text-gray-500', 'text-evergreen-700');
                } else {
                    dot.classList.replace('border-evergreen-500', 'border-gray-200');
                    dot.classList.remove('bg-evergreen-500');
                    icon.classList.add('hidden');
                    lbl.classList.replace('text-evergreen-700', 'text-gray-500');
                }
            });
        }

        // ── Photo preview ──────────────────────────────────────────────────────
        function previewPhoto(input) {
            if (!input.files || !input.files[0]) return;
            const reader = new FileReader();
            reader.onload = e => {
                const preview = document.getElementById('photo-preview');
                // Replace div placeholder with img tag if needed
                if (preview.tagName === 'DIV') {
                    const img = document.createElement('img');
                    img.id = 'photo-preview';
                    img.className = 'w-20 h-20 rounded-full object-cover ring-4 ring-evergreen-100 shadow';
                    preview.replaceWith(img);
                    img.src = e.target.result;
                } else {
                    preview.src = e.target.result;
                }
                document.getElementById('photo-actions').classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }

        function handleDrop(e) {
            e.preventDefault();
            e.currentTarget.classList.remove('border-evergreen-400', 'bg-evergreen-50');
            const input = document.getElementById('photo-input');
            const dt = e.dataTransfer;
            if (dt.files.length) {
                // Assign files
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(dt.files[0]);
                input.files = dataTransfer.files;
                previewPhoto(input);
            }
        }

        function cancelPhoto() {
            document.getElementById('photo-input').value = '';
            document.getElementById('photo-actions').classList.add('hidden');
        }

        // ── Close modals on backdrop ───────────────────────────────────────────
        document.querySelectorAll('[id^="modal-"]').forEach(m => {
            m.addEventListener('click', e => {
                if (e.target === m) m.classList.add('hidden');
            });
        });
    </script>
@endpush
