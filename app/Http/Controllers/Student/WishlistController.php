<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $userId    = Auth::id();
        $wishlists = Wishlist::with(['book.category', 'book.reviews'])
            ->where('user_id', $userId)->latest()->get();
        return view('student.wishlist', compact('wishlists'));
    }

    public function toggle(Book $book)
    {
        $userId = Auth::id();

        $existing = Wishlist::where('user_id', $userId)
            ->where('book_id', $book->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return response()->json([
                'success' => true,
                'in_wishlist' => false
            ]);
        }

        Wishlist::create([
            'user_id' => $userId,
            'book_id' => $book->id,
            'school_id' => Auth::user()->school_id
        ]);

        return response()->json([
            'success' => true,
            'in_wishlist' => true
        ]);
    }

    public function destroy(Book $book)
    {
        Wishlist::where('user_id', Auth::id())
            ->where('book_id', $book->id)
            ->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
