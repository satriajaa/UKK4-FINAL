<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userId   = Auth::id();
        $schoolId = Auth::user()->school_id;

        // Active borrows
        $activeBorrows = Borrowing::with('book.category')
            ->where('user_id', $userId)
            ->whereIn('status', ['borrowed', 'late'])
            ->orderBy('due_date')
            ->get();

        // Borrow history (last 5)
        $borrowHistory = Borrowing::with('book')
            ->where('user_id', $userId)
            ->where('status', 'returned')
            ->latest('return_date')
            ->limit(5)
            ->get();

        // Stats
        $totalBorrowed = Borrowing::where('user_id', $userId)->count();
        $totalReturned = Borrowing::where('user_id', $userId)->where('status', 'returned')->count();
        $totalLate     = Borrowing::where('user_id', $userId)->where('status', 'late')->count();
        $totalFine     = Borrowing::where('user_id', $userId)->sum('fine');
        $wishlistCount = Wishlist::where('user_id', $userId)->count();

        // Recommended / New Books
        $newBooks = Book::with('category')
            ->where('school_id', $schoolId)
            ->where('is_available', true)
            ->where('stock', '>', 0)
            ->latest()
            ->limit(6)
            ->get();

        return view('student.dashboard', compact(
            'activeBorrows', 'borrowHistory',
            'totalBorrowed', 'totalReturned', 'totalLate', 'totalFine',
            'wishlistCount', 'newBooks'
        ));
    }
}
