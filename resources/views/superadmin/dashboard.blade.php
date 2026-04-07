@extends('layouts.superadmin')

@section('title', 'Dashboard')

@push('styles')
<style>
    /* ── PAGE HEADER ── */
    .sa-page-header {
        margin-bottom: 26px;
        animation: fadeUp 0.35s ease both;
    }
    .sa-page-title {
        font-family: 'Playfair Display', serif;
        font-size: 26px; font-weight: 900;
        letter-spacing: -0.8px; color: var(--ink);
        display: flex; align-items: center; gap: 10px;
        line-height: 1.1;
    }
    .sa-page-subtitle {
        font-size: 13px; color: var(--muted); margin-top: 4px;
    }
    .sa-page-subtitle span {
        color: var(--gmid); font-weight: 600;
    }

    /* ── STAT CARDS ── */
    .sa-stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 24px;
    }
    .sa-stat-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 18px 20px;
        position: relative; overflow: hidden;
        transition: border-color .2s, transform .15s, box-shadow .2s;
        animation: fadeUp 0.4s ease both;
    }
    .sa-stat-card:hover {
        border-color: var(--border2);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(22,101,52,0.08);
    }
    .sa-stat-card::before {
        content: '';
        position: absolute; top: -16px; right: -16px;
        width: 64px; height: 64px; border-radius: 50%;
        background: var(--gbright); opacity: 0.06;
    }
    .sa-stat-label {
        font-size: 10px; font-weight: 700; letter-spacing: 1.2px;
        text-transform: uppercase; color: var(--muted); margin-bottom: 8px;
    }
    .sa-stat-val {
        font-family: 'Playfair Display', serif;
        font-size: 32px; font-weight: 900;
        color: var(--gmid); line-height: 1;
        letter-spacing: -1px;
    }
    .sa-stat-sub {
        font-size: 11px; color: var(--muted); margin-top: 6px;
        display: flex; align-items: center; gap: 4px;
    }
    .sa-stat-icon {
        position: absolute; top: 16px; right: 18px;
        font-size: 20px; color: var(--gmid); opacity: 0.12;
    }
    .stat-red .sa-stat-sub { color: #dc2626; }
    .stat-amber .sa-stat-sub { color: #b45309; }

    /* ── SECTION HEADING ── */
    .sa-section-head {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 16px; animation: fadeUp 0.4s ease 0.1s both;
    }
    .sa-section-title {
        font-family: 'Playfair Display', serif;
        font-size: 16px; font-weight: 900;
        color: var(--ink); letter-spacing: -0.3px;
        display: flex; align-items: center; gap: 8px;
    }
    .sa-section-count {
        font-size: 10px; font-weight: 700;
        background: var(--surface); border: 1px solid var(--border2);
        color: var(--muted); padding: 2px 8px; border-radius: 8px;
    }

    /* ── FILTER BAR ── */
    .sa-filter-bar {
        display: flex; gap: 8px; margin-bottom: 16px;
        animation: fadeUp 0.4s ease 0.15s both;
    }
    .sa-filter-input {
        flex: 1; background: white;
        border: 1px solid var(--border2);
        border-radius: 10px; padding: 8px 14px;
        font-size: 12px; color: var(--ink);
        font-family: 'DM Sans', sans-serif; outline: none;
        transition: border-color 0.15s;
    }
    .sa-filter-input:focus { border-color: var(--gmid); }
    .sa-filter-input::placeholder { color: var(--muted); }
    .sa-filter-select {
        background: white; border: 1px solid var(--border2);
        border-radius: 10px; padding: 8px 12px;
        font-size: 12px; color: var(--ink);
        font-family: 'DM Sans', sans-serif; outline: none;
        cursor: pointer; transition: border-color 0.15s;
    }
    .sa-filter-select:focus { border-color: var(--gmid); }

    /* ── SCHOOL GRID ── */
    .school-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        animation: fadeUp 0.4s ease 0.2s both;
    }

    /* ── SCHOOL CARD ── */
    .school-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 18px;
        padding: 20px;
        cursor: pointer;
        transition: all 0.2s;
        position: relative; overflow: hidden;
    }
    .school-card::after {
        content: '';
        position: absolute; top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--gmid), var(--gbright));
        opacity: 0; transition: opacity 0.2s;
    }
    .school-card:hover {
        border-color: var(--border2);
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(22,101,52,0.08);
    }
    .school-card:hover::after { opacity: 1; }

    .sc-head { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 14px; }
    .sc-avatar {
        width: 40px; height: 40px; border-radius: 12px;
        background: rgba(22,101,52,0.07);
        border: 1px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; flex-shrink: 0;
    }
    .sc-name { font-size: 13px; font-weight: 700; color: var(--ink); line-height: 1.3; }
    .sc-npsn { font-size: 10px; color: var(--muted); margin-top: 2px; }
    .sc-badge {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 9px; font-weight: 700; padding: 2px 7px;
        border-radius: 5px; letter-spacing: 0.5px; margin-top: 4px;
    }
    .sc-badge-active { background: rgba(34,197,94,0.1); color: #166534; border: 1px solid rgba(34,197,94,0.2); }
    .sc-badge-inactive { background: rgba(220,38,38,0.07); color: #991b1b; border: 1px solid rgba(220,38,38,0.15); }

    .sc-stats {
        display: grid; grid-template-columns: repeat(3, 1fr);
        gap: 8px; margin-bottom: 14px;
    }
    .sc-stat {
        background: var(--surface); border-radius: 10px;
        padding: 9px 6px; text-align: center;
    }
    .sc-stat-val { font-size: 15px; font-weight: 700; color: var(--gmid); display: block; line-height: 1; }
    .sc-stat-lbl { font-size: 9px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; margin-top: 3px; }

    .sc-footer {
        display: flex; align-items: center; justify-content: space-between;
        padding-top: 12px; border-top: 1px solid var(--border);
    }
    .sc-meta { font-size: 11px; color: var(--muted); }
    .sc-meta strong { color: var(--gmid); font-weight: 600; }

    .btn-detail {
        font-size: 11px; font-weight: 600; padding: 6px 14px;
        border-radius: 8px; color: var(--gmid);
        background: rgba(22,101,52,0.07);
        border: 1px solid var(--border2);
        cursor: pointer; transition: all 0.15s;
        font-family: 'DM Sans', sans-serif;
    }
    .btn-detail:hover { background: rgba(22,101,52,0.14); }

    .btn-add {
        display: inline-flex; align-items: center; gap: 8px;
        background: var(--gmid); color: white;
        border: none; padding: 9px 18px; border-radius: 100px;
        font-size: 13px; font-weight: 600; cursor: pointer;
        font-family: 'DM Sans', sans-serif; text-decoration: none;
        transition: all 0.2s;
    }
    .btn-add:hover { background: var(--gdeep); transform: translateY(-1px); box-shadow: 0 6px 18px rgba(13,61,46,0.2); }

    /* ── DETAIL PANEL ── */
    .detail-panel {
        background: white;
        border: 1px solid var(--border);
        border-radius: 20px;
        overflow: hidden;
        margin-top: 20px;
        animation: fadeUp 0.3s ease both;
        display: none;
    }
    .detail-panel.open { display: block; }

    .detail-header {
        background: var(--gdeep);
        padding: 20px 24px;
        display: flex; align-items: center; justify-content: space-between;
    }
    .detail-header-left { display: flex; align-items: center; gap: 14px; }
    .detail-school-icon {
        width: 44px; height: 44px; background: rgba(255,255,255,0.1);
        border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;
    }
    .detail-school-name {
        font-family: 'Playfair Display', serif;
        font-size: 17px; font-weight: 900; color: white; line-height: 1.2;
    }
    .detail-school-npsn { font-size: 11px; color: rgba(255,255,255,0.45); margin-top: 2px; }
    .btn-close-detail {
        display: flex; align-items: center; gap: 6px;
        background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);
        color: rgba(255,255,255,0.7); padding: 7px 14px; border-radius: 8px;
        font-size: 12px; font-weight: 600; cursor: pointer;
        font-family: 'DM Sans', sans-serif; transition: all 0.15s;
    }
    .btn-close-detail:hover { background: rgba(255,255,255,0.15); color: white; }

    .detail-body { padding: 24px; }
    .detail-body-grid {
        display: grid; grid-template-columns: 1fr 1.4fr 1fr;
        gap: 24px;
    }

    .detail-section-label {
        font-size: 9px; font-weight: 700; letter-spacing: 2px;
        text-transform: uppercase; color: var(--gmid);
        margin-bottom: 12px; display: flex; align-items: center; gap: 8px;
    }
    .detail-section-label::after { content:''; flex:1; height:1px; background:var(--border); }

    .info-field { margin-bottom: 12px; }
    .info-lbl { font-size: 9px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: var(--muted); margin-bottom: 2px; }
    .info-val { font-size: 13px; font-weight: 600; color: var(--ink); line-height: 1.4; }

    .detail-stats-grid {
        display: grid; grid-template-columns: 1fr 1fr;
        gap: 8px;
    }
    .detail-stat {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: 12px; padding: 12px; text-align: center;
    }
    .detail-stat.highlight { background: rgba(22,101,52,0.06); border-color: rgba(22,101,52,0.18); }
    .detail-stat-val {
        font-family: 'Playfair Display', serif;
        font-size: 24px; font-weight: 900; color: var(--gmid); display: block; line-height: 1;
    }
    .detail-stat-lbl { font-size: 9px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px; }

    .admin-row {
        display: flex; align-items: center; gap: 10px;
        background: var(--surface); border: 1px solid var(--border);
        border-radius: 10px; padding: 10px 12px; margin-bottom: 8px;
    }
    .admin-num {
        width: 22px; height: 22px; border-radius: 50%;
        background: rgba(22,101,52,0.1); border: 1px solid var(--border2);
        display: flex; align-items: center; justify-content: center;
        font-size: 10px; font-weight: 700; color: var(--gmid); flex-shrink: 0;
    }
    .admin-name { font-size: 12px; font-weight: 600; color: var(--ink); }
    .admin-info { font-size: 10px; color: var(--muted); }

    .detail-actions {
        display: flex; gap: 8px; justify-content: flex-end;
        padding-top: 16px; margin-top: 16px;
        border-top: 1px solid var(--border);
    }
    .btn-edit-school {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(22,101,52,0.08); border: 1px solid var(--border2);
        color: var(--gmid); padding: 8px 18px; border-radius: 10px;
        font-size: 12px; font-weight: 600; cursor: pointer;
        font-family: 'DM Sans', sans-serif; transition: all 0.15s;
    }
    .btn-edit-school:hover { background: rgba(22,101,52,0.16); }
    .btn-delete-school {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(220,38,38,0.08); border: 1px solid rgba(220,38,38,0.2);
        color: #991b1b; padding: 8px 18px; border-radius: 10px;
        font-size: 12px; font-weight: 600; cursor: pointer;
        font-family: 'DM Sans', sans-serif; transition: all 0.15s;
        text-decoration: none;
    }
    .btn-delete-school:hover { background: rgba(220,38,38,0.15); }

    @keyframes fadeUp {
        from { opacity:0; transform:translateY(10px); }
        to   { opacity:1; transform:translateY(0); }
    }
</style>
@endpush

@section('content')

{{-- ── PAGE HEADER ── --}}
<div class="sa-page-header" style="display:flex;align-items:flex-start;justify-content:space-between;">
    <div>
        <div class="sa-page-title">
            <i class="fas fa-th-large" style="font-size:20px;color:var(--gmid);"></i>
            Dashboard Superadmin
        </div>
        <div class="sa-page-subtitle">
            Selamat datang, Superadmin! Mengelola seluruh sistem perpustakaan sekolah &mdash;
            <span>{{ now()->format('d M Y, H:i') }}</span>
        </div>
    </div>
    <a href="{{ route('superadmin.schools.create') }}" class="btn-add">
        <i class="fas fa-plus" style="font-size:11px;"></i> Tambah Sekolah Baru
    </a>
</div>

{{-- ── STAT CARDS ── --}}
<div class="sa-stat-grid">
    <div class="sa-stat-card" style="animation-delay:0.04s;">
        <div class="sa-stat-label">Total Sekolah</div>
        <div class="sa-stat-val">{{ $totalSchools }}</div>
        <div class="sa-stat-sub">
            <span style="color:var(--gmid);">{{ $activeSchools }} aktif</span>
            @if($inactiveSchools > 0)
                &nbsp;·&nbsp;<span style="color:#dc2626;">{{ $inactiveSchools }} nonaktif</span>
            @endif
        </div>
        <i class="fas fa-school sa-stat-icon"></i>
    </div>
    <div class="sa-stat-card" style="animation-delay:0.08s;">
        <div class="sa-stat-label">Total Buku</div>
        <div class="sa-stat-val">{{ number_format($totalBooks) }}</div>
        <div class="sa-stat-sub">+{{ $newBooksThisMonth }} buku bulan ini</div>
        <i class="fas fa-book sa-stat-icon"></i>
    </div>
    <div class="sa-stat-card stat-amber" style="animation-delay:0.12s;">
        <div class="sa-stat-label">Total Siswa</div>
        <div class="sa-stat-val">{{ number_format($totalStudents) }}</div>
        <div class="sa-stat-sub">
            @if($pendingMembers > 0)
                <i class="fas fa-clock" style="font-size:10px;"></i> {{ $pendingMembers }} menunggu verifikasi
            @else
                +{{ $newStudentsThisMonth }} bulan ini
            @endif
        </div>
        <i class="fas fa-users sa-stat-icon"></i>
    </div>
    <div class="sa-stat-card stat-red" style="animation-delay:0.16s;">
        <div class="sa-stat-label">Peminjaman Aktif</div>
        <div class="sa-stat-val">{{ $activeBorrows }}</div>
        <div class="sa-stat-sub">
            @if($lateBorrows > 0)
                <i class="fas fa-exclamation-triangle" style="font-size:10px;"></i> {{ $lateBorrows }} terlambat
            @else
                Semua tepat waktu
            @endif
        </div>
        <i class="fas fa-exchange-alt sa-stat-icon"></i>
    </div>
</div>

{{-- ── MANAGE SCHOOLS ── --}}
<div class="sa-section-head">
    <div class="sa-section-title">
        🏫 Manage Schools
        <span class="sa-section-count">{{ $schools->count() }} sekolah</span>
    </div>
</div>

<div class="sa-filter-bar">
    <input class="sa-filter-input" type="text" id="schoolSearch" placeholder="🔍  Cari nama sekolah atau NPSN..." oninput="filterSchools()">
    <select class="sa-filter-select" id="statusFilter" onchange="filterSchools()">
        <option value="">Semua Status</option>
        <option value="active">Aktif</option>
        <option value="inactive">Nonaktif</option>
    </select>
</div>

{{-- ── SCHOOL GRID ── --}}
<div class="school-grid" id="schoolGrid">
    @forelse($schools as $school)
    <div class="school-card"
         data-id="{{ $school->id }}"
         data-name="{{ strtolower($school->name) }}"
         data-npsn="{{ $school->npsn }}"
         data-status="{{ $school->status }}">
        <div class="sc-head">
            <div class="sc-avatar">🏫</div>
            <div style="flex:1;min-width:0;">
                <div class="sc-name">{{ $school->name }}</div>
                <div class="sc-npsn">ID: NPSN {{ $school->npsn }}</div>
                <span class="sc-badge {{ $school->status === 'active' ? 'sc-badge-active' : 'sc-badge-inactive' }}">
                    ● {{ strtoupper($school->status === 'active' ? 'ACTIVE' : 'NONAKTIF') }}
                </span>
            </div>
        </div>

        <div class="sc-stats">
            <div class="sc-stat">
                <span class="sc-stat-val">{{ number_format($school->books_count) }}</span>
                <div class="sc-stat-lbl">Buku</div>
            </div>
            <div class="sc-stat">
                <span class="sc-stat-val">{{ number_format($school->students_count) }}</span>
                <div class="sc-stat-lbl">Siswa</div>
            </div>
            <div class="sc-stat">
                <span class="sc-stat-val">{{ $school->admins_count }}</span>
                <div class="sc-stat-lbl">Admin</div>
            </div>
        </div>

        <div class="sc-footer">
            <div class="sc-meta">
                Denda: <strong>Rp {{ number_format($school->total_fine ?? 0) }}</strong>
                &nbsp;·&nbsp; Trx: <strong>{{ $school->active_borrows_count }}</strong>
            </div>
            <button class="btn-detail" onclick="openDetail({{ $school->id }}, event)">Detail →</button>
        </div>
    </div>
    @empty
    <div style="grid-column:1/-1;text-align:center;padding:48px;color:var(--muted);font-size:14px;">
        <i class="fas fa-school" style="font-size:32px;opacity:0.2;display:block;margin-bottom:12px;"></i>
        Belum ada sekolah terdaftar.
    </div>
    @endforelse
</div>

{{-- ── DETAIL PANEL (EXPANDED VIEW) ── --}}
<div class="detail-panel" id="detailPanel">
    <div class="detail-header">
        <div class="detail-header-left">
            <div class="detail-school-icon">🏫</div>
            <div>
                <div class="detail-school-name" id="dp-name">—</div>
                <div class="detail-school-npsn" id="dp-npsn">—</div>
            </div>
        </div>
        <button class="btn-close-detail" onclick="closeDetail()">
            <i class="fas fa-times"></i> Tutup Detail
        </button>
    </div>

    <div class="detail-body">
        <div class="detail-body-grid">
            {{-- Column 1: Info Sekolah --}}
            <div>
                <div class="detail-section-label">School Information</div>
                <div class="info-field">
                    <div class="info-lbl">School Name</div>
                    <div class="info-val" id="dp-full-name">—</div>
                </div>
                <div class="info-field">
                    <div class="info-lbl">NPSN</div>
                    <div class="info-val" id="dp-npsn-val">—</div>
                </div>
                <div class="info-field">
                    <div class="info-lbl">Address</div>
                    <div class="info-val" id="dp-addr">—</div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="info-field">
                        <div class="info-lbl">Contact</div>
                        <div class="info-val" id="dp-contact">—</div>
                    </div>
                    <div class="info-field">
                        <div class="info-lbl">Email</div>
                        <div class="info-val" id="dp-email" style="font-size:11px;">—</div>
                    </div>
                </div>
            </div>

            {{-- Column 2: Detailed Stats --}}
            <div>
                <div class="detail-section-label">Detailed Stats</div>
                <div class="detail-stats-grid">
                    <div class="detail-stat highlight">
                        <span class="detail-stat-val" id="dp-books">—</span>
                        <div class="detail-stat-lbl">Total Buku</div>
                    </div>
                    <div class="detail-stat">
                        <span class="detail-stat-val" id="dp-students">—</span>
                        <div class="detail-stat-lbl">Siswa Aktif</div>
                    </div>
                    <div class="detail-stat">
                        <span class="detail-stat-val" id="dp-admins">—</span>
                        <div class="detail-stat-lbl">Admin</div>
                    </div>
                    <div class="detail-stat">
                        <span class="detail-stat-val" id="dp-trx">—</span>
                        <div class="detail-stat-lbl">Transaksi</div>
                    </div>
                    <div class="detail-stat" style="grid-column:1/-1;background:rgba(22,101,52,0.04);">
                        <span class="detail-stat-val" id="dp-fine" style="font-size:20px;">—</span>
                        <div class="detail-stat-lbl">Total Denda</div>
                    </div>
                </div>
            </div>

            {{-- Column 3: Assigned Admins --}}
            <div>
                <div class="detail-section-label">Assigned Admins</div>
                <div id="dp-admin-list">
                    <div style="text-align:center;padding:24px;color:var(--muted);font-size:12px;">
                        <i class="fas fa-users" style="font-size:20px;opacity:0.2;display:block;margin-bottom:8px;"></i>
                        Belum ada admin
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-actions">
            <a href="#" id="dp-edit-link" class="btn-edit-school">
                <i class="fas fa-edit"></i> Edit Sekolah
            </a>
            <a href="#" id="dp-delete-link" class="btn-delete-school"
               onclick="return confirm('Yakin ingin menghapus sekolah ini?')">
                <i class="fas fa-trash"></i> Hapus Sekolah
            </a>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ── School data injected from controller ──
    const schoolsData = @json($schools->keyBy('id'));

    function filterSchools() {
        const q = document.getElementById('schoolSearch').value.toLowerCase();
        const st = document.getElementById('statusFilter').value;
        document.querySelectorAll('#schoolGrid .school-card').forEach(card => {
            const nameMatch = card.dataset.name.includes(q) || card.dataset.npsn.includes(q);
            const statusMatch = !st || card.dataset.status === st;
            card.style.display = (nameMatch && statusMatch) ? '' : 'none';
        });
    }

    function openDetail(id, event) {
        const s = schoolsData[id];
        if (!s) return;

        document.getElementById('dp-name').textContent = s.name;
        document.getElementById('dp-npsn').textContent = 'NPSN: ' + s.npsn;
        document.getElementById('dp-full-name').textContent = s.name;
        document.getElementById('dp-npsn-val').textContent = s.npsn;
        document.getElementById('dp-addr').textContent = s.address || '—';
        document.getElementById('dp-contact').textContent = s.phone || '—';
        document.getElementById('dp-email').textContent = s.email || '—';
        document.getElementById('dp-books').textContent = (s.books_count || 0).toLocaleString('id-ID');
        document.getElementById('dp-students').textContent = (s.students_count || 0).toLocaleString('id-ID');
        document.getElementById('dp-admins').textContent = s.admins_count || 0;
        document.getElementById('dp-trx').textContent = s.active_borrows_count || 0;
        document.getElementById('dp-fine').textContent = 'Rp ' + (s.total_fine || 0).toLocaleString('id-ID');

        // Edit/delete links
        document.getElementById('dp-edit-link').href = `/superadmin/schools/${id}/edit`;
        document.getElementById('dp-delete-link').href = `/superadmin/schools/${id}`;
        document.getElementById('dp-delete-link').setAttribute('data-id', id);

        // Admin list - placeholder (needs separate API call in real impl)
        const adminListEl = document.getElementById('dp-admin-list');
        if (s.admins && s.admins.length > 0) {
            adminListEl.innerHTML = s.admins.map((a, i) => `
                <div class="admin-row">
                    <div class="admin-num">${i + 1}</div>
                    <div>
                        <div class="admin-name">${a.full_name}</div>
                        <div class="admin-info">@${a.username} · ${a.email || '—'}</div>
                    </div>
                </div>
            `).join('');
        } else {
            adminListEl.innerHTML = `
                <div style="text-align:center;padding:20px;color:var(--muted);font-size:12px;">
                    <i class="fas fa-users" style="font-size:18px;opacity:0.2;display:block;margin-bottom:6px;"></i>
                    Data admin belum tersedia
                </div>`;
        }

        const panel = document.getElementById('detailPanel');
        panel.classList.add('open');
        setTimeout(() => panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 50);
    }

    function closeDetail() {
        document.getElementById('detailPanel').classList.remove('open');
    }

    // Delete with form submit
    document.getElementById('dp-delete-link').addEventListener('click', function(e) {
        e.preventDefault();
        if (!confirm('Yakin ingin menghapus sekolah ini? Aksi ini tidak dapat dibatalkan.')) return;
        const id = this.dataset.id;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/superadmin/schools/${id}`;
        form.innerHTML = `@csrf @method('DELETE')`.replace('@csrf', '<input type="hidden" name="_token" value="{{ csrf_token() }}">').replace("@method('DELETE')", '<input type="hidden" name="_method" value="DELETE">');
        document.body.appendChild(form);
        form.submit();
    });
</script>
@endpush
