<?php
// app/Http/Controllers/Student/HistoryController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $activeBorrows = Borrowing::with('book.category')
            ->where('user_id', $userId)
            ->whereIn('status', ['pending', 'borrowed', 'late', 'return_requested'])
            ->orderByRaw("FIELD(status, 'late', 'return_requested', 'borrowed', 'pending')")
            ->orderBy('due_date')
            ->get()
            ->map(function ($trx) {
                // FIX: pakai startOfDay() untuk perbandingan yang akurat
                if ($trx->status === 'borrowed' && now()->startOfDay()->greaterThan($trx->due_date->startOfDay())) {
                    $trx->update(['status' => 'late']);
                    $trx->status = 'late'; // update in-memory juga
                }
                return $trx;
            });

        $returnedBorrows = Borrowing::with('book.category')
            ->where('user_id', $userId)
            ->whereIn('status', ['returned', 'rejected'])
            ->latest('updated_at')
            ->paginate(5);

        $totalBorrowed = Borrowing::where('user_id', $userId)->count();
        $totalActive   = $activeBorrows->whereIn('status', ['borrowed', 'late'])->count();
        $totalLate     = $activeBorrows->where('status', 'late')->count();
        $totalFine     = Borrowing::where('user_id', $userId)->sum('fine');
        $totalPending = $activeBorrows->where('status', 'pending')->count();
        return view('student.history', compact(
            'activeBorrows',
            'returnedBorrows',
            'totalBorrowed',
            'totalActive',
            'totalLate',
            'totalFine',
            'totalPending'
        ));
    }

    /**
     * Siswa request kembalikan → status RETURN_REQUESTED
     * PATCH /student/return/{borrowing}
     */
    public function requestReturn(Request $request, Borrowing $borrowing)
    {
        if ($borrowing->user_id !== Auth::id()) abort(403);

        if (!in_array($borrowing->status, ['borrowed', 'late'])) {
            return back()->with('error', 'Status peminjaman tidak valid untuk dikembalikan.');
        }

        $borrowing->update([
            'status'              => 'return_requested',
            'return_requested_at' => now(),
        ]);

        return back()->with('success', 'Permintaan pengembalian berhasil dikirim! Tunggu konfirmasi pustakawan.');
    }
}
