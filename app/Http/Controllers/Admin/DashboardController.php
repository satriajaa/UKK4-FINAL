<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $schoolId = Auth::user()->school_id;
        $now      = Carbon::now();

        // ── Stats Cards ──────────────────────────────────────────────
        $totalBooks     = Book::where('school_id', $schoolId)->count();
        $availableBooks = Book::where('school_id', $schoolId)->where('is_available', true)->where('stock', '>', 0)->count();
        $totalMembers   = User::where('school_id', $schoolId)->where('role', 'student')->where('status', 'approved')->count();
        $activeBorrows  = Borrowing::where('school_id', $schoolId)->where('status', 'borrowed')->count();
        $pendingMembers = User::where('school_id', $schoolId)->where('status', 'pending')->count();
        $lateBorrows    = Borrowing::where('school_id', $schoolId)->where('status', 'late')->count();

        // ── Recent Transactions ───────────────────────────────────────
        $recentTransactions = Borrowing::with(['user', 'book'])
            ->where('school_id', $schoolId)
            ->latest()
            ->limit(5)
            ->get();

        // ── Monthly Statistics (current month) ───────────────────────
        $monthlyBorrows  = Borrowing::where('school_id', $schoolId)
            ->whereMonth('borrow_date', $now->month)
            ->whereYear('borrow_date', $now->year)
            ->count();

        $monthlyReturns  = Borrowing::where('school_id', $schoolId)
            ->whereMonth('return_date', $now->month)
            ->whereYear('return_date', $now->year)
            ->where('status', 'returned')
            ->count();

        $monthlyLate     = Borrowing::where('school_id', $schoolId)
            ->whereMonth('due_date', $now->month)
            ->whereYear('due_date', $now->year)
            ->where('status', 'late')
            ->count();

        $monthTotal = $monthlyBorrows + $monthlyReturns;

        // ── Pending Member List (max 5) ───────────────────────────────
        $pendingList = User::with('class')
            ->where('school_id', $schoolId)
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalBooks', 'availableBooks', 'totalMembers', 'activeBorrows',
            'pendingMembers', 'lateBorrows',
            'recentTransactions',
            'monthlyBorrows', 'monthlyReturns', 'monthlyLate', 'monthTotal',
            'pendingList'
        ));
    }
}
