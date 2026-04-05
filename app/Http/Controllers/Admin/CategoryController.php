<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'code'        => 'nullable|string|max:5',
            'description' => 'nullable|string',
        ]);

        $schoolId = Auth::user()->school_id;

        $category = Category::create([
            'school_id'   => $schoolId,
            'name'        => $request->name,
            'code'        => strtoupper($request->code),
            'description' => $request->description,
        ]);

        ActivityLog::log('add_category', "Kategori ditambahkan: {$category->name}", $schoolId);

        return back()->with('success', "Kategori \"{$category->name}\" berhasil ditambahkan.")
        ->with('last_tab', $request->active_tab);
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'code'        => 'nullable|string|max:5',
            'description' => 'nullable|string',
        ]);

        $category->update([
            'name'        => $request->name,
            'code'        => strtoupper($request->code),
            'description' => $request->description,
        ]);

        ActivityLog::log('update_category', "Kategori diupdate: {$category->name}", $category->school_id);

        return back()->with('success', "Kategori berhasil diperbarui.")
        ->with('last_tab', $request->active_tab);
    }

    public function destroy(Category $category)
    {
        // Cek apakah kategori masih digunakan oleh buku
        if ($category->books()->count() > 0) {
            return back()->with('error', "Kategori tidak bisa dihapus karena masih memiliki buku.");
        }

        $name = $category->name;
        $category->delete();

        ActivityLog::log('delete_category', "Kategori dihapus: {$name}", Auth::user()->school_id);

        return back()->with('success', "Kategori \"{$name}\" berhasil dihapus.")
        ;
    }
}
