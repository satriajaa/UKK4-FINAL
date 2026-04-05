<?php
// app/Http/Controllers/Student/BookStudentController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\Wishlist;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookStudentController extends Controller
{
    public function index(Request $request)
    {
        $userId   = Auth::id();
        $schoolId = Auth::user()->school_id;

        $query = Book::with(['category', 'reviews'])
            ->where('school_id', $schoolId)
            ->where('is_available', true);

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(fn($q2) =>
                $q2->where('title', 'like', "%{$q}%")
                   ->orWhere('author', 'like', "%{$q}%")
                   ->orWhere('isbn', 'like', "%{$q}%")
            );
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        match ($request->get('sort', 'newest')) {
            'oldest' => $query->oldest(),
            'rating' => $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating'),
            'title'  => $query->orderBy('title'),
            default  => $query->latest(),
        };

        $books         = $query->paginate(12)->withQueryString();
        $categories    = Category::where('school_id', $schoolId)->orderBy('name')->get();
        $wishlistedIds = Wishlist::where('user_id', $userId)->pluck('book_id')->toArray();
        $setting       = Setting::getForSchool($schoolId);

        // IDs buku yang sedang pending request oleh siswa ini
        $pendingBookIds = Borrowing::where('user_id', $userId)
            ->where('status', 'pending')
            ->pluck('book_id')
            ->toArray();

        return view('student.books.index', compact(
            'books', 'categories', 'wishlistedIds', 'setting', 'pendingBookIds'
        ));
    }

    public function show(Book $book)
    {
        $userId   = Auth::id();
        $schoolId = Auth::user()->school_id;

        if ($book->school_id !== $schoolId) abort(403);

        $book->load(['category', 'reviews.user']);

        $isWishlisted  = Wishlist::where('user_id', $userId)->where('book_id', $book->id)->exists();
        $alreadyBorrow = Borrowing::where('user_id', $userId)
            ->where('book_id', $book->id)
            ->whereIn('status', ['pending', 'borrowed', 'late', 'return_requested'])
            ->exists();
        $activeBorrows = Borrowing::where('user_id', $userId)
            ->whereIn('status', ['pending', 'borrowed', 'late', 'return_requested'])
            ->count();
        $relatedBooks  = Book::with('category')
            ->where('school_id', $schoolId)
            ->where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->where('is_available', true)
            ->latest()->limit(5)->get();
        $setting = Setting::getForSchool($schoolId);

        return view('student.books.show', compact(
            'book', 'isWishlisted', 'alreadyBorrow', 'activeBorrows', 'relatedBooks', 'setting'
        ));
    }

    /**
     * Siswa request pinjam → status PENDING, stok BELUM berkurang
     * POST /student/borrow/{book}
     */
    public function borrow(Request $request, Book $book)
    {
        $userId   = Auth::id();
        $schoolId = Auth::user()->school_id;

        if ($book->school_id !== $schoolId) abort(403);

        if ($book->stock <= 0 || !$book->is_available) {
            return back()->with('error', 'Maaf, stok buku ini sudah habis.');
        }

        // Cek sudah ada request/aktif untuk buku ini
        $exists = Borrowing::where('user_id', $userId)
            ->where('book_id', $book->id)
            ->whereIn('status', ['pending', 'borrowed', 'late', 'return_requested'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Kamu sudah mengajukan atau sedang meminjam buku ini.');
        }

        // Cek batas 3 buku (pending + aktif)
        $activeCount = Borrowing::where('user_id', $userId)
            ->whereIn('status', ['pending', 'borrowed', 'late', 'return_requested'])
            ->count();

        if ($activeCount >= 3) {
            return back()->with('error', 'Kamu sudah mencapai batas maksimal peminjaman (3 buku).');
        }

        $setting  = Setting::getForSchool($schoolId);
        $maxDays  = $setting->max_borrow_days ?? 14;
        $today    = Carbon::today();
        $dueDate  = $today->copy()->addDays($maxDays);
        $code     = 'TRX-' . $today->format('Ymd') . '-' . str_pad(
            Borrowing::where('school_id', $schoolId)->whereDate('created_at', today())->count() + 1,
            3, '0', STR_PAD_LEFT
        );

        Borrowing::create([
            'transaction_code' => $code,
            'school_id'        => $schoolId,
            'user_id'          => $userId,
            'book_id'          => $book->id,
            'borrow_date'      => $today,
            'due_date'         => $dueDate,
            'status'           => 'pending',   // ← PENDING, bukan borrowed
        ]);

        // Stok BELUM berkurang — baru berkurang saat admin approve

        if (class_exists(\App\Models\ActivityLog::class)) {
            \App\Models\ActivityLog::log('borrow_request', "Mengajukan peminjaman: {$book->title} [{$code}]");
        }

        return back()->with('success', "Permintaan peminjaman \"$book->title\" berhasil dikirim! Tunggu persetujuan pustakawan.");
    }
}
