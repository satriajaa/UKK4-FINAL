<?php
// app/Models/Borrowing.php

namespace App\Models;

use App\Models\Book;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code',
        'school_id',
        'user_id',
        'book_id',
        'borrow_date',
        'due_date',
        'return_date',
        'return_requested_at',
        'status',
        'fine',
        'notes',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'borrow_date'         => 'date',
        'due_date'            => 'date',
        'return_date'         => 'date',
        'approved_at'         => 'datetime',
        'return_requested_at' => 'datetime',
        'fine'                => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────────────
    public function school()
    {
        return $this->belongsTo(School::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ── Scopes ─────────────────────────────────────────────────────
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    public function scopeBorrowed($query)
    {
        return $query->where('status', 'borrowed');
    }
    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }
    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }
    public function scopeReturnRequested($query)
    {
        return $query->where('status', 'return_requested');
    }

    // ── Helpers ────────────────────────────────────────────────────
    public function isLate(): bool
    {
        if (in_array($this->status, ['returned', 'pending', 'rejected'])) return false;
        return Carbon::today()->greaterThan($this->due_date);
    }

    // app/Models/Borrowing.php

    public function calculateFine($finePerDay = 1000): float
    {
        if (!$this->isLate()) return 0;
        $returnDate = $this->return_date ?? Carbon::today();
        // FIX: abs() biar selalu positif, urutan diffInDays tidak penting lagi
        return abs($returnDate->diffInDays($this->due_date)) * $finePerDay;
    }

    public function getDaysLate(): int
    {
        if (!$this->isLate()) return 0;
        $returnDate = $this->return_date ?? Carbon::today();
        // FIX: sama, pakai abs()
        return (int) abs($returnDate->diffInDays($this->due_date));
    }
}
