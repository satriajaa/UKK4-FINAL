<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        // ── Global Stats ─────────────────────────────────────────────
        $totalSchools    = School::count();
        $activeSchools   = School::where('status', 'active')->count();
        $inactiveSchools = $totalSchools - $activeSchools;

        $totalBooks      = Book::count();
        $totalStudents   = User::where('role', 'student')->where('status', 'approved')->count();
        $totalAdmins     = User::where('role', 'school_admin')->count();

        $pendingMembers  = User::where('status', 'pending')->count();
        $activeBorrows   = Borrowing::whereIn('status', ['borrowed', 'late'])->count();
        $lateBorrows     = Borrowing::where('status', 'late')->count();

        $totalFineCollected = Borrowing::where('status', 'returned')->sum('fine');

        // ── Monthly Trend (last 6 months) ────────────────────────────
        $monthlyTrend = collect(range(5, 0))->map(function ($monthsAgo) {
            $date = Carbon::now()->subMonths($monthsAgo);
            return [
                'month'   => $date->format('M'),
                'borrows' => Borrowing::whereYear('borrow_date', $date->year)
                                      ->whereMonth('borrow_date', $date->month)
                                      ->count(),
                'returns' => Borrowing::whereYear('return_date', $date->year)
                                      ->whereMonth('return_date', $date->month)
                                      ->where('status', 'returned')
                                      ->count(),
            ];
        });

        // ── Schools with stats + admin list ─────────────────────────
        $schools = School::withCount([
            'books',
            'users as students_count' => fn($q) => $q->where('role', 'student')->where('status', 'approved'),
            'users as admins_count'   => fn($q) => $q->where('role', 'school_admin'),
            'borrowings as active_borrows_count' => fn($q) => $q->whereIn('status', ['borrowed', 'late']),
        ])
        ->withSum('borrowings as total_fine', 'fine')
        ->with(['users' => fn($q) => $q->where('role', 'school_admin')->select('id','school_id','full_name','username','email')])
        ->latest()
        ->get()
        ->each(function ($school) {
            $school->admins = $school->users;
        });

        // ── Recent Activity Log ──────────────────────────────────────
        $recentActivities = ActivityLog::with(['user', 'school'])
            ->latest('created_at')
            ->limit(8)
            ->get();

        // ── Top Schools by borrows this month ────────────────────────
        $topSchools = School::withCount([
            'borrowings as month_borrows' => fn($q) => $q
                ->whereMonth('borrow_date', $now->month)
                ->whereYear('borrow_date', $now->year),
        ])
        ->orderByDesc('month_borrows')
        ->limit(5)
        ->get();

        // ── New registrations this month ─────────────────────────────
        $newStudentsThisMonth = User::where('role', 'student')
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        $newBooksThisMonth = Book::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        return view('superadmin.dashboard', compact(
            'totalSchools', 'activeSchools', 'inactiveSchools',
            'totalBooks', 'totalStudents', 'totalAdmins',
            'pendingMembers', 'activeBorrows', 'lateBorrows',
            'totalFineCollected',
            'monthlyTrend',
            'schools',
            'recentActivities',
            'topSchools',
            'newStudentsThisMonth', 'newBooksThisMonth',
        ));
    }
}
