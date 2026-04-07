@extends('layouts.superadmin')

@section('title', 'Registrasi Sekolah Baru')

@section('topbar-nav')
    {{-- <a href="{{ route('superadmin.schools.index') }}" style="font-size:12px;color:var(--muted);text-decoration:none;padding:4px 12px;border-radius:6px;transition:all 0.15s;" onmouseover="this.style.color='var(--gmid)'" onmouseout="this.style.color='var(--muted)'">
        Kelola Sekolah
    </a> --}}
    <span style="font-size:12px;color:var(--border2);margin:0 4px;">/</span>
    <span style="font-size:12px;color:var(--gmid);font-weight:600;padding:4px 12px;background:rgba(22,101,52,0.07);border-radius:6px;">
        Register New School
    </span>
@endsection

@push('styles')
<style>
    .reg-wrap { max-width: 860px; margin: 0 auto; }

    .reg-page-head {
        margin-bottom: 28px;
        animation: fadeUp 0.3s ease both;
    }
    .reg-page-title {
        font-family: 'Playfair Display', serif;
        font-size: 24px; font-weight: 900;
        color: var(--ink); letter-spacing: -0.5px;
        margin-bottom: 4px;
    }
    .reg-page-sub { font-size: 13px; color: var(--muted); }

    .verified-badge {
        display: inline-flex; align-items: center; gap: 7px;
        background: rgba(22,101,52,0.07);
        border: 1px solid rgba(22,101,52,0.2);
        color: var(--gmid); padding: 7px 14px; border-radius: 100px;
        font-size: 11px; font-weight: 700; letter-spacing: 0.5px;
        text-transform: uppercase;
    }
    .verified-badge i { color: var(--gbright); }

    /* ── FORM CARD ── */
    .form-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 18px;
        animation: fadeUp 0.4s ease both;
    }
    .form-card-head {
        display: flex; align-items: center; gap: 12px;
        padding: 16px 22px;
        border-bottom: 1px solid var(--border);
        background: var(--surface);
    }
    .form-card-icon {
        width: 32px; height: 32px; border-radius: 10px;
        background: rgba(22,101,52,0.1); border: 1px solid var(--border2);
        display: flex; align-items: center; justify-content: center;
        font-size: 14px;
    }
    .form-card-title { font-size: 12px; font-weight: 700; color: var(--ink); letter-spacing: 0.5px; }
    .form-card-body { padding: 22px; }

    /* ── FORM FIELDS ── */
    .form-row { display: grid; gap: 14px; margin-bottom: 14px; }
    .form-row-2 { grid-template-columns: 1fr 1fr; }
    .form-row-3 { grid-template-columns: 1fr 1fr 1fr; }

    .form-group label {
        display: block;
        font-size: 10px; font-weight: 700; letter-spacing: 1.2px;
        text-transform: uppercase; color: var(--muted);
        margin-bottom: 6px;
    }
    .form-group label .req { color: #dc2626; }

    .form-control {
        width: 100%;
        background: var(--surface); border: 1px solid var(--border2);
        border-radius: 10px; padding: 10px 14px;
        font-size: 13px; color: var(--ink);
        font-family: 'DM Sans', sans-serif; outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .form-control:focus {
        border-color: var(--gmid);
        background: white;
        box-shadow: 0 0 0 3px rgba(22,101,52,0.08);
    }
    .form-control::placeholder { color: var(--muted); }
    .form-control.error { border-color: #dc2626; }
    .form-control textarea { resize: vertical; min-height: 80px; }

    textarea.form-control { resize: vertical; min-height: 80px; }

    .form-hint { font-size: 10px; color: var(--muted); margin-top: 4px; }
    .form-error { font-size: 10px; color: #dc2626; margin-top: 4px; font-weight: 600; }

    /* ── VALIDATION INDICATORS ── */
    .input-wrap { position: relative; }
    .input-status {
        position: absolute; right: 12px; top: 50%;
        transform: translateY(-50%);
        font-size: 12px; display: none;
    }
    .input-status.ok { color: var(--gbright); display: block; }
    .input-status.err { color: #dc2626; display: block; }

    /* ── PASSWORD STRENGTH ── */
    .strength-bar { display: flex; gap: 4px; margin-top: 6px; }
    .sb-seg { flex: 1; height: 3px; border-radius: 2px; background: var(--border2); transition: background 0.2s; }
    .pw-match { font-size: 10px; margin-top: 4px; font-weight: 600; }

    /* ── TOGGLE PW ── */
    .pw-wrap { position: relative; }
    .pw-wrap .form-control { padding-right: 40px; }
    .pw-toggle {
        position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
        background: none; border: none; cursor: pointer; color: var(--muted);
        font-size: 13px; transition: color 0.15s; padding: 0;
    }
    .pw-toggle:hover { color: var(--gmid); }

    /* ── INFO NOTE ── */
    .form-note {
        background: rgba(22,101,52,0.05);
        border: 1px solid rgba(22,101,52,0.15);
        border-radius: 12px; padding: 12px 16px;
        display: flex; gap: 10px; align-items: flex-start;
        margin-top: 8px;
    }
    .form-note i { color: var(--gmid); font-size: 13px; margin-top: 1px; flex-shrink: 0; }
    .form-note-text { font-size: 12px; color: var(--muted); line-height: 1.5; }
    .form-note-text strong { color: var(--gmid); }

    /* ── ACTIONS ── */
    .form-actions {
        display: flex; align-items: center; justify-content: flex-end;
        gap: 10px; margin-top: 24px;
        animation: fadeUp 0.4s ease 0.3s both;
    }
    .btn-cancel {
        padding: 10px 22px; border-radius: 10px;
        background: white; border: 1px solid var(--border2);
        color: var(--muted); font-size: 13px; font-weight: 600;
        cursor: pointer; text-decoration: none;
        font-family: 'DM Sans', sans-serif; transition: all 0.15s;
    }
    .btn-cancel:hover { color: var(--ink); border-color: var(--gmid); }
    .btn-save {
        display: inline-flex; align-items: center; gap: 8px;
        background: var(--gmid); color: white;
        border: none; padding: 10px 24px; border-radius: 10px;
        font-size: 13px; font-weight: 700; cursor: pointer;
        font-family: 'DM Sans', sans-serif; transition: all 0.2s;
    }
    .btn-save:hover { background: var(--gdeep); transform: translateY(-1px); box-shadow: 0 6px 18px rgba(13,61,46,0.2); }

    @keyframes fadeUp {
        from { opacity:0; transform:translateY(10px); }
        to   { opacity:1; transform:translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="reg-wrap">

    {{-- ── PAGE HEADER ── --}}
    <div class="reg-page-head" style="display:flex;align-items:flex-start;justify-content:space-between;">
        <div>
            <div class="reg-page-title">Registrasi Sekolah Baru</div>
            <div class="reg-page-sub">Input data sekolah dan akun administrator untuk memberikan akses panel kelola kepada pihak sekolah.</div>
        </div>
        <span class="verified-badge">
            <i class="fas fa-shield-check"></i> Lembaga Terverifikasi
        </span>
    </div>

    <form method="POST" action="{{ route('superadmin.schools.store') }}">
        @csrf

        {{-- ── INFORMASI SEKOLAH ── --}}
        <div class="form-card" style="animation-delay:0.05s;">
            <div class="form-card-head">
                <div class="form-card-icon">🏫</div>
                <div class="form-card-title">INFORMASI SEKOLAH</div>
            </div>
            <div class="form-card-body">

                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Sekolah <span class="req">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') error @enderror"
                               value="{{ old('name') }}"
                               placeholder="Contoh: SMA Negeri 01 Jakarta" required>
                        @error('name') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-row form-row-2">
                    <div class="form-group">
                        <label>NPSN <span class="req">*</span></label>
                        <div class="input-wrap">
                            <input type="text" name="npsn" id="npsn-input"
                                   class="form-control @error('npsn') error @enderror"
                                   value="{{ old('npsn') }}"
                                   placeholder="8 digit nomor pokok"
                                   maxlength="8" oninput="validateNpsn(this)" required>
                            <span class="input-status" id="npsn-status"></span>
                        </div>
                        <div class="form-hint">Nomor Pokok Sekolah Nasional (8 digit)</div>
                        @error('npsn') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>Email Sekolah <span class="req">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') error @enderror"
                               value="{{ old('email') }}"
                               placeholder="admin@sekolah.sch.id" required>
                        @error('email') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Kontak / Telepon</label>
                        <input type="text" name="phone" class="form-control"
                               value="{{ old('phone') }}"
                               placeholder="021-xxxxxx">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Alamat Lengkap <span class="req">*</span></label>
                        <textarea name="address" class="form-control @error('address') error @enderror"
                                  placeholder="Jl. Raya Utama No. 123, Kota..." required>{{ old('address') }}</textarea>
                        @error('address') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

            </div>
        </div>

        {{-- ── INFORMASI ADMIN SEKOLAH ── --}}
        <div class="form-card" style="animation-delay:0.12s;">
            <div class="form-card-head">
                <div class="form-card-icon">👨‍💼</div>
                <div class="form-card-title">INFORMASI ADMIN SEKOLAH</div>
            </div>
            <div class="form-card-body">

                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Lengkap <span class="req">*</span></label>
                        <input type="text" name="admin_name" class="form-control @error('admin_name') error @enderror"
                               value="{{ old('admin_name') }}"
                               placeholder="Masukkan nama penanggung jawab" required>
                        @error('admin_name') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-row form-row-2">
                    <div class="form-group">
                        <label>Username <span class="req">*</span></label>
                        <div class="input-wrap">
                            <input type="text" name="username" id="username-input"
                                   class="form-control @error('username') error @enderror"
                                   value="{{ old('username') }}"
                                   placeholder="admin_sekolah"
                                   oninput="this.value=this.value.replace(/[^a-z0-9_]/gi,'').toLowerCase()" required>
                            <span class="input-status" id="username-status"></span>
                        </div>
                        @error('username') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>Email Pribadi</label>
                        <input type="email" name="admin_email" class="form-control"
                               value="{{ old('admin_email') }}"
                               placeholder="nama@email.com">
                    </div>
                </div>

                <div class="form-row form-row-2">
                    <div class="form-group">
                        <label>Password <span class="req">*</span></label>
                        <div class="pw-wrap">
                            <input type="password" name="password" id="pw-input"
                                   class="form-control @error('password') error @enderror"
                                   placeholder="Min. 8 karakter"
                                   oninput="checkStrength(this.value)" required>
                            <button type="button" class="pw-toggle" onclick="togglePw('pw-input','pw-icon-1')">
                                <i class="fas fa-eye" id="pw-icon-1"></i>
                            </button>
                        </div>
                        <div class="strength-bar">
                            <div class="sb-seg" id="s1"></div>
                            <div class="sb-seg" id="s2"></div>
                            <div class="sb-seg" id="s3"></div>
                            <div class="sb-seg" id="s4"></div>
                        </div>
                        @error('password') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password <span class="req">*</span></label>
                        <div class="pw-wrap">
                            <input type="password" name="password_confirmation" id="pw-confirm"
                                   class="form-control"
                                   placeholder="Ulangi password"
                                   oninput="checkMatch()" required>
                            <button type="button" class="pw-toggle" onclick="togglePw('pw-confirm','pw-icon-2')">
                                <i class="fas fa-eye" id="pw-icon-2"></i>
                            </button>
                        </div>
                        <div class="pw-match" id="pw-match-msg"></div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── INFO NOTE ── --}}
        <div class="form-note">
            <i class="fas fa-info-circle"></i>
            <div class="form-note-text">
                <strong>Penting:</strong> Pastikan NPSN yang dimasukkan valid dan sesuai dengan data Kemendikbud.
                Password administrator akan dikirimkan salinannya melalui email sekolah yang terdaftar setelah proses simpan berhasil.
            </div>
        </div>

        {{-- ── ACTIONS ── --}}
        <div class="form-actions">
            <a href="{{ route('superadmin.dashboard') }}" class="btn-cancel">Batal</a>
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Simpan Sekolah & Admin
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
        if (input.value.length === 8) {
            el.textContent = '✓'; el.className = 'input-status ok';
        } else if (input.value.length > 0) {
            el.textContent = '✗'; el.className = 'input-status err';
        } else {
            el.className = 'input-status';
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
        const colors = ['', '#dc2626', '#f59e0b', '#22c55e', '#166534'];
        ['s1','s2','s3','s4'].forEach((id, i) => {
            document.getElementById(id).style.background = i < score ? colors[score] : 'var(--border2)';
        });
    }

    function checkMatch() {
        const p1 = document.getElementById('pw-input').value;
        const p2 = document.getElementById('pw-confirm').value;
        const el = document.getElementById('pw-match-msg');
        if (!p2) { el.textContent = ''; return; }
        if (p1 === p2) {
            el.textContent = '✓ Password cocok';
            el.style.color = 'var(--gmid)';
        } else {
            el.textContent = '✗ Password tidak cocok';
            el.style.color = '#dc2626';
        }
    }
</script>
@endpush
