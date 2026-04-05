@extends('layouts.student')

@section('title', 'Profile Saya')

@push('styles')
<style>
    .avatar-ring {
        background: linear-gradient(135deg, #166534, #22c55e, #166534);
        padding: 3px;
        border-radius: 9999px;
    }
    .avatar-inner {
        border-radius: 9999px;
        overflow: hidden;
        background: white;
        padding: 2px;
        border-radius: 9999px;
    }
    .tab-section { display: none; }
    .tab-section.active { display: block; }
    .profile-tab.active {
        background: #166534;
        color: white;
        box-shadow: 0 2px 8px rgba(22,101,52,0.2);
    }

    /* Password strength */
    .strength-bar div { transition: width 0.3s ease, background 0.3s ease; }

    /* Photo hover overlay */
    .avatar-overlay {
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    .avatar-wrap:hover .avatar-overlay { opacity: 1; }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .section-anim { animation: fadeIn 0.25s ease; }

    input:focus, select:focus, textarea:focus {
        outline: none;
        ring: 2px;
    }
</style>
@endpush

@section('content')

@php
    $user          = auth()->user();
    $totalBorrowed = \App\Models\Borrowing::where('user_id', $user->id)->count();
    $activeBorrow  = \App\Models\Borrowing::where('user_id', $user->id)->whereIn('status',['borrowed','late'])->count();
    $totalFine     = \App\Models\Borrowing::where('user_id', $user->id)->sum('fine');
    $avgRating     = \App\Models\Review::where('user_id', $user->id)->avg('rating') ?? 0;
    $classes       = \App\Models\ClassModel::where('school_id', $user->school_id)->orderBy('name')->get();
@endphp

<div class="max-w-2xl mx-auto">

    {{-- ── Profile Card ─────────────────────────────────────── --}}
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden mb-5">

        {{-- Top Green Strip --}}
        <div class="h-24 bg-gradient-to-r from-evergreen-700 via-evergreen-600 to-emerald-500 relative">
            <div class="absolute inset-0 opacity-20" style="background-image: url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\");"></div>
        </div>

        {{-- Avatar + Info --}}
        <div class="flex flex-col items-center -mt-14 pb-6 px-6">

            {{-- Avatar --}}
            <div class="avatar-wrap relative cursor-pointer mb-3" onclick="document.getElementById('photo-input').click()">
                <div class="avatar-ring">
                    <div class="avatar-inner">
                        @if($user->photo)
                        <img src="{{ asset('storage/'.$user->photo) }}"
                             id="avatar-preview"
                             class="w-24 h-24 object-cover rounded-full"
                             alt="{{ $user->full_name }}">
                        @else
                        <div id="avatar-preview"
                             class="w-24 h-24 rounded-full flex items-center justify-center text-2xl font-black text-white"
                             style="background: linear-gradient(135deg, #22c55e, #15803d)">
                            {{ strtoupper(substr($user->full_name ?? 'S', 0, 2)) }}
                        </div>
                        @endif
                    </div>
                </div>
                {{-- Camera overlay --}}
                <div class="avatar-overlay absolute inset-0 flex items-center justify-center">
                    <div class="w-8 h-8 bg-evergreen-600 rounded-full flex items-center justify-center shadow-lg border-2 border-white absolute bottom-1 right-1">
                        <i class="fas fa-camera text-white text-xs"></i>
                    </div>
                </div>
            </div>

            {{-- Hidden file input --}}
            <input type="file" id="photo-input" accept="image/*" class="hidden" onchange="previewPhoto(this)">

            {{-- Name & Info --}}
            <h2 class="text-xl font-black text-gray-900">{{ $user->full_name }}</h2>
            <p class="text-sm text-gray-400 mt-0.5">
                {{ $user->username }} &nbsp;•&nbsp; Kelas {{ $user->class->name ?? '—' }}
            </p>

            {{-- Action Buttons --}}
            <div class="flex gap-3 mt-4">
                <button onclick="switchSection('profile')"
                    id="btn-profile"
                    class="profile-tab active flex items-center gap-2 px-5 py-2 rounded-full text-sm font-bold border border-evergreen-600 text-evergreen-700 transition">
                    <i class="fas fa-pen text-xs"></i> Edit Profil
                </button>
                <button onclick="switchSection('password')"
                    id="btn-password"
                    class="profile-tab flex items-center gap-2 px-5 py-2 rounded-full text-sm font-bold border border-gray-200 text-gray-600 hover:border-evergreen-300 hover:text-evergreen-700 transition">
                    <i class="fas fa-lock text-xs"></i> Ganti Password
                </button>
            </div>
        </div>

        {{-- ── Stats Strip ─────────────────────────────────────── --}}
        <div class="grid grid-cols-4 border-t border-gray-100">
            <div class="flex flex-col items-center py-4 border-r border-gray-100">
                <div class="w-8 h-8 bg-blue-50 rounded-xl flex items-center justify-center mb-2">
                    <i class="fas fa-bookmark text-blue-500 text-sm"></i>
                </div>
                <div class="text-2xl font-black text-gray-900 leading-none">{{ $totalBorrowed }}</div>
                <div class="text-xs text-gray-400 font-semibold uppercase tracking-wide mt-1">Total Pinjam</div>
            </div>

            <div class="flex flex-col items-center py-4 border-r border-gray-100">
                <div class="w-8 h-8 bg-orange-50 rounded-xl flex items-center justify-center mb-2">
                    <i class="fas fa-book-open text-orange-500 text-sm"></i>
                </div>
                <div class="text-2xl font-black text-gray-900 leading-none">{{ $activeBorrow }}</div>
                <div class="text-xs text-gray-400 font-semibold uppercase tracking-wide mt-1">Sedang Pinjam</div>
            </div>

            <div class="flex flex-col items-center py-4 border-r border-gray-100">
                <div class="w-8 h-8 bg-red-50 rounded-xl flex items-center justify-center mb-2">
                    <i class="fas fa-money-bill text-red-400 text-sm"></i>
                </div>
                <div class="text-lg font-black {{ $totalFine > 0 ? 'text-red-600' : 'text-gray-900' }} leading-none">
                    Rp{{ number_format($totalFine, 0, ',', '.') }}
                </div>
                <div class="text-xs text-gray-400 font-semibold uppercase tracking-wide mt-1">Total Denda</div>
            </div>

            <div class="flex flex-col items-center py-4">
                <div class="w-8 h-8 bg-yellow-50 rounded-xl flex items-center justify-center mb-2">
                    <i class="fas fa-star text-yellow-500 text-sm"></i>
                </div>
                <div class="text-2xl font-black text-gray-900 leading-none">
                    {{ number_format($avgRating, 1) }}
                </div>
                <div class="text-xs text-gray-400 font-semibold uppercase tracking-wide mt-1">Rating Siswa</div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════ --}}
    {{-- ── SECTION: Edit Profil ─────────────────────── --}}
    {{-- ══════════════════════════════════════════════ --}}
    <div id="section-profile" class="tab-section active section-anim">
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">

            <h3 class="font-bold text-gray-900 flex items-center gap-2 mb-5">
                <div class="w-7 h-7 bg-evergreen-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user text-evergreen-600 text-xs"></i>
                </div>
                Informasi Pribadi
            </h3>

            <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data" id="profile-form">
                @csrf
                @method('PATCH')

                {{-- Hidden photo field --}}
                <input type="file" name="photo" id="photo-field" class="hidden">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Nama Lengkap --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}"
                               placeholder="Nama lengkap"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-evergreen-500 focus:border-evergreen-500 transition @error('full_name') border-red-400 bg-red-50 @enderror">
                        @error('full_name')
                        <p class="text-xs text-red-500 mt-1"><i class="fas fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Username --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Username</label>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}"
                               placeholder="username"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-evergreen-500 focus:border-evergreen-500 transition @error('username') border-red-400 bg-red-50 @enderror">
                        @error('username')
                        <p class="text-xs text-red-500 mt-1"><i class="fas fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                               placeholder="email@sekolah.sch.id"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-evergreen-500 focus:border-evergreen-500 transition @error('email') border-red-400 bg-red-50 @enderror">
                        @error('email')
                        <p class="text-xs text-red-500 mt-1"><i class="fas fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kelas --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kelas</label>
                        <select name="class_id"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-evergreen-500 focus:border-evergreen-500 transition appearance-none @error('class_id') border-red-400 bg-red-50 @enderror">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classes as $cls)
                            <option value="{{ $cls->id }}" {{ ($user->class_id == $cls->id) ? 'selected' : '' }}>
                                {{ $cls->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('class_id')
                        <p class="text-xs text-red-500 mt-1"><i class="fas fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Read-only info --}}
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1.5">NIS / Student ID</label>
                        <div class="px-4 py-2.5 bg-gray-50 border border-dashed border-gray-200 rounded-xl text-sm text-gray-500">
                            {{ $user->student_id ?? '—' }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1.5">Status Akun</label>
                        <div class="px-4 py-2.5 bg-evergreen-50 border border-evergreen-100 rounded-xl text-sm">
                            <span class="text-evergreen-700 font-bold flex items-center gap-1.5">
                                <span class="w-2 h-2 bg-evergreen-500 rounded-full"></span>
                                {{ ucfirst($user->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Footer Buttons --}}
                <div class="flex items-center justify-between mt-6 pt-5 border-t border-gray-100">
                    <div class="text-xs text-gray-400 flex items-center gap-1.5">
                        <i class="fas fa-circle-info"></i>
                        Terakhir diperbarui<br>
                        pada {{ $user->updated_at?->format('d M Y') ?? '—' }}
                    </div>
                    <div class="flex gap-3">
                        <button type="button" onclick="resetProfileForm()"
                            class="px-5 py-2.5 border border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50 transition">
                            Batalkan
                        </button>
                        <button type="submit"
                            class="bg-evergreen-700 hover:bg-evergreen-800 text-white text-sm font-bold px-6 py-2.5 rounded-xl transition shadow-sm">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════ --}}
    {{-- ── SECTION: Ganti Password ──────────────────── --}}
    {{-- ══════════════════════════════════════════════ --}}
    <div id="section-password" class="tab-section section-anim">
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">

            <h3 class="font-bold text-gray-900 flex items-center gap-2 mb-5">
                <div class="w-7 h-7 bg-evergreen-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-lock text-evergreen-600 text-xs"></i>
                </div>
                Ubah Password
            </h3>

            @if(session('password_error'))
            <div class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                <i class="fas fa-exclamation-circle flex-shrink-0"></i>
                {{ session('password_error') }}
            </div>
            @endif

            <form method="POST" action="{{ route('student.profile.password') }}" id="password-form">
                @csrf
                @method('PATCH')

                {{-- Current Password --}}
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Password Saat Ini</label>
                    <div class="relative">
                        <input type="password" name="current_password" id="current-pwd"
                               placeholder="Masukkan password saat ini"
                               class="w-full px-4 py-2.5 pr-11 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 focus:border-evergreen-500 transition @error('current_password') border-red-400 bg-red-50 @enderror">
                        <button type="button" onclick="togglePwd('current-pwd', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition text-sm">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('current_password')
                    <p class="text-xs text-red-500 mt-1"><i class="fas fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                {{-- New Password --}}
                <div class="mb-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Password Baru</label>
                    <div class="relative">
                        <input type="password" name="password" id="new-pwd"
                               placeholder="Minimal 8 karakter"
                               oninput="checkStrength(this.value)"
                               class="w-full px-4 py-2.5 pr-11 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 focus:border-evergreen-500 transition @error('password') border-red-400 bg-red-50 @enderror">
                        <button type="button" onclick="togglePwd('new-pwd', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition text-sm">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                    <p class="text-xs text-red-500 mt-1"><i class="fas fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Strength Bar --}}
                <div class="mb-4">
                    <div class="flex gap-1 mb-1 strength-bar">
                        <div id="bar1" class="h-1 flex-1 rounded-full bg-gray-200"></div>
                        <div id="bar2" class="h-1 flex-1 rounded-full bg-gray-200"></div>
                        <div id="bar3" class="h-1 flex-1 rounded-full bg-gray-200"></div>
                        <div id="bar4" class="h-1 flex-1 rounded-full bg-gray-200"></div>
                    </div>
                    <div class="text-xs text-gray-400" id="strength-label"></div>
                </div>

                {{-- Confirm Password --}}
                <div class="mb-6">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Konfirmasi Password Baru</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="confirm-pwd"
                               placeholder="Ulangi password baru"
                               oninput="checkMatch()"
                               class="w-full px-4 py-2.5 pr-11 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-evergreen-500 focus:border-evergreen-500 transition">
                        <button type="button" onclick="togglePwd('confirm-pwd', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition text-sm">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div id="match-status" class="text-xs mt-1 hidden"></div>
                </div>

                {{-- Tips --}}
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 mb-6 text-xs text-blue-700 space-y-1">
                    <div class="font-semibold mb-1"><i class="fas fa-shield-halved mr-1"></i> Tips Keamanan Password</div>
                    <div>• Gunakan minimal 8 karakter</div>
                    <div>• Kombinasikan huruf besar, kecil, angka, dan simbol</div>
                    <div>• Jangan gunakan informasi pribadi</div>
                </div>

                {{-- Buttons --}}
                <div class="flex gap-3">
                    <button type="button" onclick="resetPasswordForm()"
                        class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50 transition">
                        Reset
                    </button>
                    <button type="submit"
                        class="flex-1 bg-evergreen-700 hover:bg-evergreen-800 text-white text-sm font-bold py-2.5 px-4 rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                        <i class="fas fa-lock text-xs"></i> Simpan Password
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// ── Tab Switching ────────────────────────────────────────────────────
function switchSection(section) {
    ['profile', 'password'].forEach(s => {
        document.getElementById(`section-${s}`).classList.toggle('active', s === section);
        document.getElementById(`btn-${s}`).classList.toggle('active', s === section);

        if (s !== section) {
            document.getElementById(`btn-${s}`).className =
                'profile-tab flex items-center gap-2 px-5 py-2 rounded-full text-sm font-bold border border-gray-200 text-gray-600 hover:border-evergreen-300 hover:text-evergreen-700 transition';
        } else {
            document.getElementById(`btn-${s}`).className =
                'profile-tab active flex items-center gap-2 px-5 py-2 rounded-full text-sm font-bold border border-evergreen-600 text-white bg-evergreen-700 transition';
        }
    });
}

// ── Photo Preview ────────────────────────────────────────────────────
function previewPhoto(input) {
    if (!input.files || !input.files[0]) return;
    const file   = input.files[0];
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('avatar-preview');
        const photoField = document.getElementById('photo-field');

        // Transfer file to the actual form input
        const dt = new DataTransfer();
        dt.items.add(file);
        photoField.files = dt.files;

        // Update preview
        if (preview.tagName === 'IMG') {
            preview.src = e.target.result;
        } else {
            // Replace div with img
            const img = document.createElement('img');
            img.id        = 'avatar-preview';
            img.src       = e.target.result;
            img.className = 'w-24 h-24 object-cover rounded-full';
            preview.replaceWith(img);
        }
    };
    reader.readAsDataURL(file);
}

// ── Toggle Password Visibility ───────────────────────────────────────
function togglePwd(id, btn) {
    const input = document.getElementById(id);
    const isText = input.type === 'text';
    input.type = isText ? 'password' : 'text';
    btn.innerHTML = `<i class="fas ${isText ? 'fa-eye' : 'fa-eye-slash'}"></i>`;
}

// ── Password Strength ────────────────────────────────────────────────
function checkStrength(val) {
    let score = 0;
    if (val.length >= 8)               score++;
    if (/[A-Z]/.test(val))             score++;
    if (/[0-9]/.test(val))             score++;
    if (/[^A-Za-z0-9]/.test(val))     score++;

    const bars   = [document.getElementById('bar1'), document.getElementById('bar2'), document.getElementById('bar3'), document.getElementById('bar4')];
    const label  = document.getElementById('strength-label');
    const colors = ['bg-red-500', 'bg-orange-400', 'bg-yellow-400', 'bg-evergreen-500'];
    const labels = ['', 'Lemah', 'Sedang', 'Kuat', 'Sangat Kuat'];
    const lColors = ['', 'text-red-500', 'text-orange-400', 'text-yellow-500', 'text-evergreen-600'];

    bars.forEach((bar, i) => {
        bar.className = `h-1 flex-1 rounded-full ${i < score ? colors[score - 1] : 'bg-gray-200'}`;
    });

    label.textContent  = val.length > 0 ? labels[score] : '';
    label.className    = `text-xs font-semibold ${lColors[score] || 'text-gray-400'}`;

    checkMatch();
}

// ── Password Match Check ─────────────────────────────────────────────
function checkMatch() {
    const pwd     = document.getElementById('new-pwd').value;
    const confirm = document.getElementById('confirm-pwd').value;
    const status  = document.getElementById('match-status');

    if (!confirm) { status.classList.add('hidden'); return; }
    status.classList.remove('hidden');
    if (pwd === confirm) {
        status.textContent = '✓ Password cocok';
        status.className   = 'text-xs mt-1 text-evergreen-600 font-semibold';
    } else {
        status.textContent = '✗ Password tidak cocok';
        status.className   = 'text-xs mt-1 text-red-500 font-semibold';
    }
}

// ── Reset Forms ──────────────────────────────────────────────────────
function resetProfileForm() {
    document.getElementById('profile-form').reset();
}
function resetPasswordForm() {
    document.getElementById('password-form').reset();
    ['bar1','bar2','bar3','bar4'].forEach(id => {
        document.getElementById(id).className = 'h-1 flex-1 rounded-full bg-gray-200';
    });
    document.getElementById('strength-label').textContent = '';
    document.getElementById('match-status').classList.add('hidden');
}

// ── Auto-switch to password tab if session error/success for password ──
@if(session('password_success') || session('password_error'))
switchSection('password');
@endif
</script>
@endpush
