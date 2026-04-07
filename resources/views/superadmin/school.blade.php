@extends('layouts.superadmin')

@section('title', 'Registrasi Sekolah Baru')

@section('topbar-nav')
    <span class="text-xs text-gray-300 mx-1">/</span>
    <span class="text-xs text-evergreen-700 font-bold px-3 py-1.5 bg-evergreen-50 rounded-lg">
        Register New School
    </span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto pb-8">

    {{-- ── PAGE HEADER ── --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight mb-1 flex items-center gap-2">
                <i class="fas fa-plus-circle text-evergreen-600"></i> Registrasi Sekolah Baru
            </h1>
            <p class="text-sm text-gray-500">Input data sekolah dan akun administrator untuk memberikan akses panel kelola kepada pihak sekolah.</p>
        </div>
        <span class="inline-flex items-center gap-1.5 bg-evergreen-50 border border-evergreen-200 text-evergreen-700 px-3 py-1.5 rounded-full text-[10px] font-bold tracking-wider uppercase flex-shrink-0">
            <i class="fas fa-shield-check text-evergreen-500"></i> Lembaga Terverifikasi
        </span>
    </div>

    <form method="POST" action="{{ route('superadmin.schools.store') }}">
        @csrf

        {{-- ── INFORMASI SEKOLAH ── --}}
        <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden mb-6 shadow-sm">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <div class="w-8 h-8 rounded-lg bg-evergreen-50 border border-evergreen-100 flex items-center justify-center">
                    <i class="fas fa-school text-evergreen-600 text-sm"></i>
                </div>
                <h2 class="text-xs font-bold text-gray-900 tracking-wider">INFORMASI SEKOLAH</h2>
            </div>

            <div class="p-6 space-y-4">
                {{-- Baris 1: Nama Sekolah --}}
                <div>
                    <label class="flex items-center gap-1.5 text-[10px] font-bold tracking-[1.2px] uppercase text-gray-500 mb-1.5">
                        <i class="fas fa-building text-gray-400"></i> Nama Sekolah <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: SMA Negeri 01 Jakarta" required
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-evergreen-500/20 focus:border-evergreen-500 transition focus:bg-white @error('name') border-red-500 @enderror">
                    @error('name') <p class="text-[10px] text-red-500 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Baris 2: NPSN & Email --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="flex items-center gap-1.5 text-[10px] font-bold tracking-[1.2px] uppercase text-gray-500 mb-1.5">
                            <i class="fas fa-id-badge text-gray-400"></i> NPSN <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="npsn" id="npsn-input" value="{{ old('npsn') }}" placeholder="8 digit nomor pokok" maxlength="8" oninput="validateNpsn(this)" required
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-evergreen-500/20 focus:border-evergreen-500 transition focus:bg-white @error('npsn') border-red-500 @enderror">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm hidden" id="npsn-status"></span>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">Nomor Pokok Sekolah Nasional (8 digit)</p>
                        @error('npsn') <p class="text-[10px] text-red-500 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="flex items-center gap-1.5 text-[10px] font-bold tracking-[1.2px] uppercase text-gray-500 mb-1.5">
                            <i class="fas fa-envelope text-gray-400"></i> Email Sekolah <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@sekolah.sch.id" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-evergreen-500/20 focus:border-evergreen-500 transition focus:bg-white @error('email') border-red-500 @enderror">
                        @error('email') <p class="text-[10px] text-red-500 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Baris 3: Kontak --}}
                <div>
                    <label class="flex items-center gap-1.5 text-[10px] font-bold tracking-[1.2px] uppercase text-gray-500 mb-1.5">
                        <i class="fas fa-phone-alt text-gray-400"></i> Kontak / Telepon
                    </label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="021-xxxxxx"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-evergreen-500/20 focus:border-evergreen-500 transition focus:bg-white">
                </div>

                {{-- Baris 4: Alamat --}}
                <div>
                    <label class="flex items-center gap-1.5 text-[10px] font-bold tracking-[1.2px] uppercase text-gray-500 mb-1.5">
                        <i class="fas fa-map-marker-alt text-gray-400"></i> Alamat Lengkap <span class="text-red-500">*</span>
                    </label>
                    <textarea name="address" rows="3" placeholder="Jl. Raya Utama No. 123, Kota..." required
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-evergreen-500/20 focus:border-evergreen-500 transition focus:bg-white resize-y min-h-[80px] @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                    @error('address') <p class="text-[10px] text-red-500 font-bold mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- ── INFORMASI ADMIN SEKOLAH ── --}}
        <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden mb-6 shadow-sm">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <div class="w-8 h-8 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center">
                    <i class="fas fa-user-tie text-blue-600 text-sm"></i>
                </div>
                <h2 class="text-xs font-bold text-gray-900 tracking-wider">INFORMASI ADMIN SEKOLAH</h2>
            </div>

            <div class="p-6 space-y-4">
                {{-- Baris 1: Nama Admin --}}
                <div>
                    <label class="flex items-center gap-1.5 text-[10px] font-bold tracking-[1.2px] uppercase text-gray-500 mb-1.5">
                        <i class="fas fa-user text-gray-400"></i> Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="admin_name" value="{{ old('admin_name') }}" placeholder="Masukkan nama penanggung jawab" required
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-evergreen-500/20 focus:border-evergreen-500 transition focus:bg-white @error('admin_name') border-red-500 @enderror">
                    @error('admin_name') <p class="text-[10px] text-red-500 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Baris 2: Username & Email Admin --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="flex items-center gap-1.5 text-[10px] font-bold tracking-[1.2px] uppercase text-gray-500 mb-1.5">
                            <i class="fas fa-at text-gray-400"></i> Username <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="username" value="{{ old('username') }}" placeholder="admin_sekolah" required oninput="this.value=this.value.replace(/[^a-z0-9_]/gi,'').toLowerCase()"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-evergreen-500/20 focus:border-evergreen-500 transition focus:bg-white @error('username') border-red-500 @enderror">
                        @error('username') <p class="text-[10px] text-red-500 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="flex items-center gap-1.5 text-[10px] font-bold tracking-[1.2px] uppercase text-gray-500 mb-1.5">
                            <i class="fas fa-envelope-open text-gray-400"></i> Email Pribadi
                        </label>
                        <input type="email" name="admin_email" value="{{ old('admin_email') }}" placeholder="nama@email.com"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-evergreen-500/20 focus:border-evergreen-500 transition focus:bg-white">
                    </div>
                </div>

                {{-- Baris 3: Password & Confirm Password --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="flex items-center gap-1.5 text-[10px] font-bold tracking-[1.2px] uppercase text-gray-500 mb-1.5">
                            <i class="fas fa-lock text-gray-400"></i> Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="password" id="pw-input" placeholder="Min. 8 karakter" required oninput="checkStrength(this.value)"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-4 pr-10 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-evergreen-500/20 focus:border-evergreen-500 transition focus:bg-white @error('password') border-red-500 @enderror">
                            <button type="button" onclick="togglePw('pw-input','pw-icon-1')" tabindex="-1"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition focus:outline-none">
                                <i class="fas fa-eye" id="pw-icon-1"></i>
                            </button>
                        </div>
                        <div class="flex gap-1 mt-2">
                            <div class="flex-1 h-1 rounded-full bg-gray-200 transition-colors" id="s1"></div>
                            <div class="flex-1 h-1 rounded-full bg-gray-200 transition-colors" id="s2"></div>
                            <div class="flex-1 h-1 rounded-full bg-gray-200 transition-colors" id="s3"></div>
                            <div class="flex-1 h-1 rounded-full bg-gray-200 transition-colors" id="s4"></div>
                        </div>
                        @error('password') <p class="text-[10px] text-red-500 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="flex items-center gap-1.5 text-[10px] font-bold tracking-[1.2px] uppercase text-gray-500 mb-1.5">
                            <i class="fas fa-key text-gray-400"></i> Konfirmasi Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="pw-confirm" placeholder="Ulangi password" required oninput="checkMatch()"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-4 pr-10 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-evergreen-500/20 focus:border-evergreen-500 transition focus:bg-white">
                            <button type="button" onclick="togglePw('pw-confirm','pw-icon-2')" tabindex="-1"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition focus:outline-none">
                                <i class="fas fa-eye" id="pw-icon-2"></i>
                            </button>
                        </div>
                        <p class="text-[10px] font-bold mt-1" id="pw-match-msg"></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── INFO NOTE ── --}}
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex gap-3 items-start mb-8">
            <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
            <p class="text-xs text-blue-700 leading-relaxed">
                <strong class="font-bold">Penting:</strong> Pastikan NPSN yang dimasukkan valid dan sesuai dengan data Kemendikbud. Password administrator akan dikirimkan salinannya melalui email sekolah yang terdaftar setelah proses simpan berhasil.
            </p>
        </div>

        {{-- ── ACTIONS ── --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('superadmin.dashboard') }}"
               class="px-5 py-2.5 bg-white border border-gray-200 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-50 hover:text-gray-900 transition flex items-center gap-2">
                <i class="fas fa-arrow-left text-xs"></i> Batal
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 bg-evergreen-600 hover:bg-evergreen-700 text-white text-sm font-bold px-6 py-2.5 rounded-xl transition shadow-sm hover:shadow-md">
                <i class="fas fa-save text-xs"></i> Simpan Sekolah & Admin
            </button>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
    function validateNpsn(input) {
        input.value = input.value.replace(/\D/g, '');
        const el = document.getElementById('npsn-status');
        el.classList.remove('hidden');

        if (input.value.length === 8) {
            el.textContent = '✓';
            el.className = 'absolute right-4 top-1/2 -translate-y-1/2 text-sm text-evergreen-500 font-bold block';
        } else if (input.value.length > 0) {
            el.textContent = '✗';
            el.className = 'absolute right-4 top-1/2 -translate-y-1/2 text-sm text-red-500 font-bold block';
        } else {
            el.classList.add('hidden');
        }
    }

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

    function checkStrength(val) {
        let score = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^a-zA-Z0-9]/.test(val)) score++;

        // Warna Tailwind: gray-200, red-500, amber-500, green-500, evergreen-700
        const colors = ['#e5e7eb', '#ef4444', '#f59e0b', '#22c55e', '#15803d'];

        ['s1','s2','s3','s4'].forEach((id, i) => {
            document.getElementById(id).style.backgroundColor = i < score ? colors[score] : '#e5e7eb';
        });
    }

    function checkMatch() {
        const p1 = document.getElementById('pw-input').value;
        const p2 = document.getElementById('pw-confirm').value;
        const el = document.getElementById('pw-match-msg');

        if (!p2) {
            el.textContent = '';
            return;
        }
        if (p1 === p2) {
            el.textContent = '✓ Password cocok';
            el.className = 'text-[10px] font-bold mt-1 text-evergreen-600';
        } else {
            el.textContent = '✗ Password tidak cocok';
            el.className = 'text-[10px] font-bold mt-1 text-red-500';
        }
    }
</script>
@endpush
