<?php
// app/Http/Controllers/Admin/TransactionController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Book;
use App\Models\User;
use App\Models\Setting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        // ── Hitung badge pending untuk tab ──
        $pendingBorrowCount  = Borrowing::where('school_id', $schoolId)->where('status', 'pending')->count();
        $pendingReturnCount  = Borrowing::where('school_id', $schoolId)->where('status', 'return_requested')->count();

        // ── Query utama (tab "Semua Transaksi") ──
        $query = Borrowing::with(['user.class', 'book'])
            ->where('school_id', $schoolId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('borrow_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('borrow_date', '<=', $request->date_to);
        }
        if ($request->filled('class_id')) {
            $query->whereHas('user', fn($q) => $q->where('class_id', $request->class_id));
        }

        $transactions = $query->latest()->paginate(10)->withQueryString();

        // ── Pending requests (tab 1) ──
        $pendingBorrows = Borrowing::with(['user.class', 'book'])
            ->where('school_id', $schoolId)
            ->where('status', 'pending')
            ->latest()
            ->get();

        // ── Return requests (tab 2) ──
        $returnRequests = Borrowing::with(['user.class', 'book'])
            ->where('school_id', $schoolId)
            ->where('status', 'return_requested')
            ->latest('return_requested_at')
            ->get();

        // ── Stats ──
        $totalTransactions = Borrowing::where('school_id', $schoolId)->count();
        $totalFine         = Borrowing::where('school_id', $schoolId)->sum('fine');
        $unpaidFine        = Borrowing::where('school_id', $schoolId)->where('status', 'late')->count();

        $setting = Setting::getForSchool($schoolId);
        $finePerDay = $setting->fine_per_day ?? 1000;

        return view('admin.transactions', compact(
            'transactions', 'pendingBorrows', 'returnRequests',
            'pendingBorrowCount', 'pendingReturnCount',
            'totalTransactions', 'totalFine', 'unpaidFine',
'finePerDay'
        ));
    }

    // ── Admin: Approve permintaan pinjam ──────────────────────────
    public function approveBorrow(Borrowing $borrowing)
    {
        $schoolId = Auth::user()->school_id;
        if ($borrowing->school_id !== $schoolId) abort(403);
        if ($borrowing->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah diproses.');
        }

        // Cek stok sekali lagi saat approve
        if ($borrowing->book->stock <= 0) {
            return back()->with('error', 'Stok buku habis, tidak bisa disetujui.');
        }

        $borrowing->update([
            'status'      => 'borrowed',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Kurangi stok SAAT approve
        $borrowing->book->decrement('stock');
        if ($borrowing->book->stock <= 0) {
            $borrowing->book->update(['is_available' => false]);
        }

        ActivityLog::log('approve_borrow', "Menyetujui peminjaman: {$borrowing->book->title} oleh {$borrowing->user->full_name}", $schoolId);

        return back()->with('success', "Peminjaman \"{$borrowing->book->title}\" oleh {$borrowing->user->full_name} berhasil disetujui.");
    }

    // ── Admin: Reject permintaan pinjam ──────────────────────────
    public function rejectBorrow(Request $request, Borrowing $borrowing)
    {
        $schoolId = Auth::user()->school_id;
        if ($borrowing->school_id !== $schoolId) abort(403);
        if ($borrowing->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah diproses.');
        }

        $request->validate(['reason' => 'nullable|string|max:255']);

        $borrowing->update([
            'status'           => 'rejected',
            'approved_by'      => Auth::id(),
            'approved_at'      => now(),
            'rejection_reason' => $request->reason ?? 'Ditolak oleh pustakawan.',
        ]);

        // Stok tidak berubah karena memang belum dikurangi saat pending

        ActivityLog::log('reject_borrow', "Menolak peminjaman: {$borrowing->book->title} oleh {$borrowing->user->full_name}", $schoolId);

        return back()->with('success', "Permintaan peminjaman berhasil ditolak.");
    }

    // ── Admin: Approve pengembalian ───────────────────────────────
    public function approveReturn(Borrowing $borrowing)
    {
        $schoolId = Auth::user()->school_id;
        if ($borrowing->school_id !== $schoolId) abort(403);
        if ($borrowing->status !== 'return_requested') {
            return back()->with('error', 'Tidak ada permintaan pengembalian untuk transaksi ini.');
        }

        $setting = Setting::getForSchool($schoolId);
        $fine    = $borrowing->calculateFine($setting->fine_per_day ?? 1000);

        $borrowing->update([
            'status'      => 'returned',
            'return_date' => Carbon::today(),
            'fine'        => $fine,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $borrowing->book->increment('stock');
        if (!$borrowing->book->is_available) {
            $borrowing->book->update(['is_available' => true]);
        }

        ActivityLog::log('approve_return', "Menyetujui pengembalian: {$borrowing->book->title} oleh {$borrowing->user->full_name}", $schoolId);

        return back()->with('success', "Pengembalian \"{$borrowing->book->title}\" berhasil dikonfirmasi." . ($fine > 0 ? " Denda: Rp " . number_format($fine, 0, ',', '.') : ''));
    }

    // ── Admin: Store (manual input dari admin) ────────────────────
    public function store(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $request->validate([
            'user_id'     => 'required|exists:users,id',
            'book_id'     => 'required|exists:books,id',
            'borrow_date' => 'required|date',
            'due_date'    => 'required|date|after:borrow_date',
        ]);

        $book = Book::where('id', $request->book_id)->where('school_id', $schoolId)->firstOrFail();
        $user = User::where('id', $request->user_id)->where('school_id', $schoolId)->firstOrFail();

        if ($book->stock <= 0) {
            return back()->with('error', "Stok buku \"{$book->title}\" habis.");
        }

        $activeCount = Borrowing::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'borrowed', 'late', 'return_requested'])
            ->count();

        if ($activeCount >= 3) {
            return back()->with('error', "Siswa {$user->full_name} sudah mencapai batas 3 buku.");
        }

        $today = Carbon::today();
        $code  = 'TRX-' . $today->format('Ymd') . '-' . str_pad(
            Borrowing::where('school_id', $schoolId)->whereDate('created_at', today())->count() + 1,
            3, '0', STR_PAD_LEFT
        );

        // Admin input langsung → langsung BORROWED (tidak perlu approval lagi)
        Borrowing::create([
            'transaction_code' => $code,
            'school_id'        => $schoolId,
            'user_id'          => $user->id,
            'book_id'          => $book->id,
            'borrow_date'      => $request->borrow_date,
            'due_date'         => $request->due_date,
            'status'           => 'borrowed',
            'approved_by'      => Auth::id(),
            'approved_at'      => now(),
            'notes'            => $request->notes,
        ]);

        $book->decrement('stock');
        if ($book->stock <= 0) $book->update(['is_available' => false]);

        ActivityLog::log('manual_borrow', "Input manual peminjaman: {$book->title} oleh {$user->full_name}", $schoolId);

        return redirect()->route('admin.transactions.index')
            ->with('success', "Peminjaman \"{$book->title}\" berhasil dicatat.");
    }

    // ── Detail (AJAX) ─────────────────────────────────────────────
    public function show(Borrowing $borrowing)
    {
        $borrowing->load(['user.class', 'book.category', 'approver']);
        return response()->json($borrowing);
    }
}
