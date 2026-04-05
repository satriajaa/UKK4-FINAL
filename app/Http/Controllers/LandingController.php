<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $data = [
            'total_books' => Book::count(),
            'total_students' => User::where('role', 'student')->count(),
            'total_schools' => School::where('status', 'active')->count(),
            'popular_books' => Book::with('category')
                ->where('is_available', true)
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get(),
            'featured_school' => School::where('status', 'active')->first(),
        ];

        return view('landing', $data);
    }

    public function about()
    {
        return view('about');
    }

    public function contact()
    {
        return view('contact');
    }

    public function books()
    {
        $books = Book::with(['category', 'school'])
            ->where('is_available', true)
            ->paginate(12);

        return view('books', compact('books'));
    }
}
