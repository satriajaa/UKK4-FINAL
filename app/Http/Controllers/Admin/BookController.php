<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $query = Book::with('category')
            ->where('school_id', $schoolId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('availability')) {
            if ($request->availability === 'available') {
                $query->where('stock', '>', 0);
            } else {
                $query->where('stock', 0);
            }
        }

        $books      = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::where('school_id', $schoolId)->withCount('books')->get();

        return view('admin.books', compact('books', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'author'           => 'required|string|max:100',
            'publisher'        => 'nullable|string|max:100',
            'publication_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'isbn'             => 'nullable|string|max:20|unique:books,isbn',
            // DIUBAH: dari 'category_id' => 'required|exists:...'
            // menjadi 'categories' array, ambil yang pertama sebagai category_id
            'category_id' => 'required|exists:categories,id',
            'categories.*'     => 'exists:categories,id',
            'stock'            => 'required|integer|min:0',
            'shelf_location'   => 'nullable|string|max:50',
            'synopsis'         => 'nullable|string',
            'cover'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $schoolId = Auth::user()->school_id;

        $coverPath = null;
        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store("covers/{$schoolId}", 'public');
        }

        // Ambil category_id pertama dari array yang dipilih
        // $categoryId = $request->categories[0];

        $book = Book::create([
            'school_id'        => $schoolId,
            'category_id'      => $request->category_id,
            'title'            => $request->title,
            'author'           => $request->author,
            'publisher'        => $request->publisher,
            'publication_year' => $request->publication_year,
            'isbn'             => $request->isbn,
            'stock'            => $request->stock,
            'shelf_location'   => $request->shelf_location,
            'synopsis'         => $request->synopsis,
            'cover'            => $coverPath,
            'is_available'     => $request->stock > 0,
        ]);

        ActivityLog::log('add_book', "Buku ditambahkan: {$book->title}", $schoolId);

        return redirect()->route('admin.books.index')
            ->with('success', "Buku \"{$book->title}\" berhasil ditambahkan.");
    }

    public function update(Request $request, Book $book)
    {
        if ($book->school_id !== Auth::user()->school_id) {
            return abort(403, 'Anda tidak memiliki akses ke buku ini.');
        }

        $request->validate([
            'title'            => 'required|string|max:255',
            'author'           => 'required|string|max:100',
            'publisher'        => 'nullable|string|max:100',
            'publication_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'isbn'             => 'nullable|string|max:20|unique:books,isbn,' . $book->id,
            // DIUBAH: sama seperti store, pakai array categories
            'category_id' => 'required|exists:categories,id',
            'categories.*'     => 'exists:categories,id',
            'stock'            => 'required|integer|min:0',
            'shelf_location'   => 'nullable|string|max:50',
            'synopsis'         => 'nullable|string',
            'cover'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('cover')) {
            if ($book->cover) Storage::disk('public')->delete($book->cover);
            $book->cover = $request->file('cover')->store("covers/{$book->school_id}", 'public');
        }

        // Ambil category_id pertama dari array
        // $categoryId = $request->categories[0];

        $book->update([
            'category_id'      => $request->category_id,
            'title'            => $request->title,
            'author'           => $request->author,
            'publisher'        => $request->publisher,
            'publication_year' => $request->publication_year,
            'isbn'             => $request->isbn,
            'stock'            => $request->stock,
            'shelf_location'   => $request->shelf_location,
            'synopsis'         => $request->synopsis,
            'is_available'     => $request->stock > 0,
            'cover'            => $book->cover,
        ]);

        ActivityLog::log('update_book', "Buku diupdate: {$book->title}", $book->school_id);

        return redirect()->route('admin.books.index')
            ->with('success', "Buku \"{$book->title}\" berhasil diperbarui.");
    }

    public function destroy(Book $book)
    {
        $title = $book->title;
        if ($book->cover) Storage::disk('public')->delete($book->cover);
        $book->delete();

        ActivityLog::log('delete_book', "Buku dihapus: {$title}", Auth::user()->school_id);

        return redirect()->route('admin.books.index')
            ->with('success', "Buku \"{$title}\" berhasil dihapus.");
    }
}
