<?php

use App\Http\Controllers\Admin\BookController         as AdminBook;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\DashboardController    as AdminDashboard;
use App\Http\Controllers\Admin\MemberController       as AdminMember;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TransactionController  as AdminTransaction;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Student\BookStudentController;
use App\Http\Controllers\Student\DashboardController  as StudentDashboard;
use App\Http\Controllers\Student\HistoryController;
use App\Http\Controllers\Student\ProfileController;
use App\Http\Controllers\Student\WishlistController;
use App\Http\Controllers\Superadmin\DashboardController;
use App\Http\Controllers\Superadmin\SchoolController;
use App\Http\Controllers\Superadmin\SuperadminLoginController;
use Illuminate\Support\Facades\Route;

// ── Landing ───────────────────────────────────────────────────────────
Route::get('/',        [LandingController::class, 'index'])->name('landing');
Route::get('/about',   [LandingController::class, 'about'])->name('about');
Route::get('/contact', [LandingController::class, 'contact'])->name('contact');
Route::get('/books',   [LandingController::class, 'books'])->name('books.public');

// ── Auth (Guest Only) ─────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',   [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login',  [LoginController::class, 'login'])->name('login.post');
    Route::get('/register',  [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
    Route::post('/get-classes-by-school', [RegisterController::class, 'getClassesBySchool'])->name('classes.by.school');
    Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('password.request');

    Route::prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/login',  [SuperadminLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [SuperadminLoginController::class, 'login'])->name('login.post');
    });
});

// ── Authenticated ──────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // ──────── SUPERADMIN ──────────────────────────────────────────────
    Route::middleware('role:super_admin')->prefix('superadmin')->name('superadmin.')->group(function () {
       Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/schools/create', [SchoolController::class, 'create'])->name('schools.create');
    Route::post('/schools', [SchoolController::class, 'store'])->name('schools.store');
    Route::get('/schools/{school}/edit', [SchoolController::class, 'edit'])->name('schools.edit');
    Route::put('/schools/{school}', [SchoolController::class, 'update'])->name('schools.update');
    Route::delete('/schools/{school}', [SchoolController::class, 'destroy'])->name('schools.destroy');
    Route::get('/schools/{school}', [SchoolController::class, 'show'])->name('schools.show');

    });

    // ──────── SCHOOL ADMIN ────────────────────────────────────────────
    Route::middleware('role:school_admin')->prefix('admin')->name('admin.')->group(function () {

        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        // Books
        Route::get('/books',           [AdminBook::class, 'index'])->name('books.index');
        Route::post('/books',          [AdminBook::class, 'store'])->name('books.store');
        Route::put('/books/{book}',    [AdminBook::class, 'update'])->name('books.update');
        Route::delete('/books/{book}', [AdminBook::class, 'destroy'])->name('books.destroy');

        // Categories
        Route::get('/categories',               fn() => redirect()->route('admin.books.index'))->name('categories.index');
        Route::post('/categories',              [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}',    [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // Members
        // Members - letakkan rute yang lebih spesifik di ATAS
        Route::get('/members',                   [AdminMember::class, 'index'])->name('members.index');
        Route::post('/members',                  [AdminMember::class, 'store'])->name('members.store');
        Route::post('/members/bulk-action',      [AdminMember::class, 'bulkAction'])->name('members.bulk');
        Route::patch('/members/{user}/approve',  [AdminMember::class, 'approve'])->name('members.approve');
        Route::patch('/members/{user}/reject',   [AdminMember::class, 'reject'])->name('members.reject');
        Route::patch('/members/{user}',          [AdminMember::class, 'update'])->name('members.update');
        Route::delete('/members/{user}',         [AdminMember::class, 'destroy'])->name('members.destroy');

        // Transactions
        Route::get('/transactions',                      [AdminTransaction::class, 'index'])->name('transactions.index');
        Route::post('/transactions',                     [AdminTransaction::class, 'store'])->name('transactions.store');
        Route::get('/transactions/{borrowing}',          [AdminTransaction::class, 'show'])->name('transactions.show');
        Route::patch('/transactions/{borrowing}/return', [AdminTransaction::class, 'approveReturn'])->name('transactions.return');
        Route::post('/transactions/{borrowing}/approve-borrow',  [AdminTransaction::class, 'approveBorrow'])->name('transactions.approve-borrow');
        Route::post('/transactions/{borrowing}/reject-borrow',   [AdminTransaction::class, 'rejectBorrow'])->name('transactions.reject-borrow');
        Route::post('/transactions/{borrowing}/approve-return',  [AdminTransaction::class, 'approveReturn'])->name('transactions.approve-return');

        // Reports & Settings
        // Settings
        Route::get('/settings',             [SettingController::class, 'index'])->name('settings');
        Route::put('/settings/app',         [SettingController::class, 'updateApp'])->name('settings.app');
        Route::patch('/settings/profile',   [SettingController::class, 'updateProfile'])->name('settings.profile');
        Route::patch('/settings/password',  [SettingController::class, 'updatePassword'])->name('settings.password');

        // BARU: route khusus tambah & hapus staf admin
        Route::post('/settings/staff',          [SettingController::class, 'storeStaff'])->name('settings.staff.store');
        Route::delete('/settings/staff/{user}', [SettingController::class, 'destroyStaff'])->name('settings.staff.destroy');

        Route::get('/classes',            [ClassController::class, 'index'])->name('classes.index');
        Route::post('/classes',           [ClassController::class, 'store'])->name('classes.store');
        Route::put('/classes/{class}',    [ClassController::class, 'update'])->name('classes.update');
        Route::delete('/classes/{class}', [ClassController::class, 'destroy'])->name('classes.destroy');

        // Jurusan
        Route::post('/majors',            [ClassController::class, 'storeMajor'])->name('majors.store');
        Route::delete('/majors',          [ClassController::class, 'destroyMajor'])->name('majors.destroy');

        // Level
        Route::post('/levels',            [ClassController::class, 'storeLevel'])->name('levels.store');
        Route::delete('/levels',          [ClassController::class, 'destroyLevel'])->name('levels.destroy');
    });

    // ──────── STUDENT ─────────────────────────────────────────────────
    Route::middleware('role:student')->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentDashboard::class, 'index'])->name('dashboard');
        // Route::get('/books',     fn() => view('student.books.index'))->name('books.index');
        // Route::get('/books/{id}', fn($id) => view('student.books.show', compact('id')))->name('books.show');
        Route::get('/history',                  [HistoryController::class, 'index'])->name('history');
        Route::patch('/return/{borrowing}', [HistoryController::class, 'requestReturn'])->name('return.request');
        Route::get('/profile',            [ProfileController::class, 'index'])->name('profile');
        Route::patch('/profile',          [ProfileController::class, 'update'])->name('profile.update');
        Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        // Dalam group student:
        Route::get('/wishlist',           [WishlistController::class, 'index'])->name('wishlist');
        Route::post('/wishlist/{book}',   [WishlistController::class, 'toggle'])->name('wishlist.toggle');
        Route::delete('/wishlist/{book}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
        // Route::post('/borrow/{book}',      fn() => back())->name('borrow');
        Route::get('/books',          [BookStudentController::class, 'index'])->name('books.index');
        Route::get('/books/{book}',   [BookStudentController::class, 'show'])->name('books.show');
        Route::post('/borrow/{book}', [BookStudentController::class, 'borrow'])->name('borrow');
        Route::post('/books/{book}/review', [BookStudentController::class, 'storeReview'])->name('books.review');
        // Route::post('/return/{borrowing}', fn() => back())->name('return');
    });
});
