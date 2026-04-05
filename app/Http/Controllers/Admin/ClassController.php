<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    const LEVEL_TEMPLATE = '__level_template__';
    const MAJOR_TEMPLATE = '__major_template__';

    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        // Kelas nyata saja — exclude semua placeholder template
        $query = ClassModel::withCount('users')
            ->where('school_id', $schoolId)
            ->whereNotIn('name', [self::LEVEL_TEMPLATE, self::MAJOR_TEMPLATE]);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('major', 'like', '%' . $request->search . '%')
                  ->orWhere('level', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        if ($request->filled('major')) {
            $query->where('major', $request->major);
        }

        $perPage = $request->get('per_page', 10);
        $classes = $query->orderBy('level')->orderBy('name')
                         ->paginate($perPage)->withQueryString();

        // Level unik (dari semua baris termasuk template level)
        $availableLevels = ClassModel::where('school_id', $schoolId)
            ->whereNotNull('level')
            ->distinct()
            ->pluck('level')
            ->sort()
            ->values()
            ->toArray();

        // Jurusan unik (dari semua baris termasuk major template, exclude level template)
        $availableMajors = ClassModel::where('school_id', $schoolId)
            ->where('name', '!=', self::LEVEL_TEMPLATE)
            ->whereNotNull('major')
            ->distinct()
            ->pluck('major')
            ->sort()
            ->values()
            ->toArray();

        // Stats per level — hitung hanya dari kelas nyata
        $levelStats = ClassModel::where('school_id', $schoolId)
            ->whereNotNull('level')
            ->withCount('users')
            ->get()
            ->groupBy('level')
            ->map(fn($group) => [
                'classes_count'  => $group->whereNotIn('name', [self::LEVEL_TEMPLATE, self::MAJOR_TEMPLATE])->count(),
                'students_count' => $group->whereNotIn('name', [self::LEVEL_TEMPLATE, self::MAJOR_TEMPLATE])->sum('users_count'),
            ]);

        // Stats per jurusan — hitung hanya dari kelas nyata
        $majorStats = ClassModel::where('school_id', $schoolId)
            ->whereNotIn('name', [self::LEVEL_TEMPLATE, self::MAJOR_TEMPLATE])
            ->whereNotNull('major')
            ->withCount('users')
            ->get()
            ->groupBy('major')
            ->map(fn($group) => [
                'classes_count'  => $group->count(),
                'students_count' => $group->sum('users_count'),
            ]);

        return view('admin.classes', compact(
            'classes', 'availableLevels', 'availableMajors', 'levelStats', 'majorStats'
        ));
    }

    // ══ KELAS ══════════════════════════════════════════════════

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'level' => 'nullable|string|max:50',
            'major' => 'nullable|string|max:100',
        ]);

        ClassModel::create([
            'school_id' => auth()->user()->school_id,
            'name'      => $request->name,
            'level'     => $request->level ?: null,
            'major'     => $request->major ?: null,
        ]);

        return redirect()->route('admin.classes.index')
            ->with('success', "Kelas '{$request->name}' berhasil ditambahkan.")
            ->with('last_tab', 'tab-classes');
    }

    public function update(Request $request, ClassModel $class)
    {
        abort_if($class->school_id !== auth()->user()->school_id, 403);

        $request->validate([
            'name'  => 'required|string|max:100',
            'level' => 'nullable|string|max:50',
            'major' => 'nullable|string|max:100',
        ]);

        $class->update([
            'name'  => $request->name,
            'level' => $request->level ?: null,
            'major' => $request->major ?: null,
        ]);

        return redirect()->route('admin.classes.index')
            ->with('success', "Kelas '{$class->name}' berhasil diperbarui.")
            ->with('last_tab', 'tab-classes');
    }

    public function destroy(ClassModel $class)
    {
        abort_if($class->school_id !== auth()->user()->school_id, 403);

        $name = $class->name;
        $class->delete();

        return redirect()->route('admin.classes.index')
            ->with('success', "Kelas '{$name}' berhasil dihapus.")
            ->with('last_tab', 'tab-classes');
    }

    // ══ JURUSAN ════════════════════════════════════════════════

    public function storeMajor(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'old_major' => 'nullable|string|max:100',
        ]);

        $schoolId = auth()->user()->school_id;
        $newName  = trim($request->name);

        // Edit / rename jurusan
        if ($request->filled('old_major')) {
            $oldName = $request->old_major;
            ClassModel::where('school_id', $schoolId)
                ->where('major', $oldName)
                ->update(['major' => $newName]);

            return redirect()->route('admin.classes.index')
                ->with('success', "Jurusan '{$oldName}' diubah menjadi '{$newName}'.")
                ->with('last_tab', 'tab-majors');
        }

        // Cek duplikat
        if (ClassModel::where('school_id', $schoolId)->where('major', $newName)->exists()) {
            return redirect()->route('admin.classes.index')
                ->with('error', "Jurusan '{$newName}' sudah ada.")
                ->with('last_tab', 'tab-majors');
        }

        // Simpan placeholder jurusan ke DB ← FIX UTAMA
        ClassModel::create([
            'school_id' => $schoolId,
            'name'      => self::MAJOR_TEMPLATE,
            'level'     => null,
            'major'     => $newName,
        ]);

        return redirect()->route('admin.classes.index')
            ->with('success', "Jurusan '{$newName}' berhasil ditambahkan.")
            ->with('last_tab', 'tab-majors');
    }

    public function destroyMajor(Request $request)
    {
        $request->validate(['major' => 'required|string']);
        $schoolId = auth()->user()->school_id;

        // Hapus placeholder
        ClassModel::where('school_id', $schoolId)
            ->where('name', self::MAJOR_TEMPLATE)
            ->where('major', $request->major)
            ->delete();

        // Set null pada kelas nyata yang pakai jurusan ini
        ClassModel::where('school_id', $schoolId)
            ->where('major', $request->major)
            ->update(['major' => null]);

        return redirect()->route('admin.classes.index')
            ->with('success', "Jurusan '{$request->major}' berhasil dihapus.")
            ->with('last_tab', 'tab-majors');
    }

    // ══ LEVEL ══════════════════════════════════════════════════

    public function storeLevel(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:50',
            'old_level' => 'nullable|string|max:50',
        ]);

        $schoolId = auth()->user()->school_id;
        $newName  = trim($request->name);

        // Edit / rename level
        if ($request->filled('old_level')) {
            $oldName = $request->old_level;
            ClassModel::where('school_id', $schoolId)
                ->where('level', $oldName)
                ->update(['level' => $newName]);

            return redirect()->route('admin.classes.index')
                ->with('success', "Level '{$oldName}' diubah menjadi '{$newName}'.")
                ->with('last_tab', 'tab-levels');
        }

        // Cek duplikat
        if (ClassModel::where('school_id', $schoolId)->where('level', $newName)->exists()) {
            return redirect()->route('admin.classes.index')
                ->with('error', "Level '{$newName}' sudah ada.")
                ->with('last_tab', 'tab-levels');
        }

        // Simpan placeholder level
        ClassModel::create([
            'school_id' => $schoolId,
            'name'      => self::LEVEL_TEMPLATE,
            'level'     => $newName,
            'major'     => null,
        ]);

        return redirect()->route('admin.classes.index')
            ->with('success', "Level '{$newName}' berhasil ditambahkan.")
            ->with('last_tab', 'tab-levels');
    }

    public function destroyLevel(Request $request)
    {
        $request->validate(['level' => 'required|string']);
        $schoolId = auth()->user()->school_id;

        $realCount = ClassModel::where('school_id', $schoolId)
            ->where('level', $request->level)
            ->whereNotIn('name', [self::LEVEL_TEMPLATE, self::MAJOR_TEMPLATE])
            ->count();

        if ($realCount > 0) {
            return redirect()->route('admin.classes.index')
                ->with('error', "Level '{$request->level}' tidak bisa dihapus, masih digunakan {$realCount} kelas.")
                ->with('last_tab', 'tab-levels');
        }

        ClassModel::where('school_id', $schoolId)
            ->where('level', $request->level)
            ->where('name', self::LEVEL_TEMPLATE)
            ->delete();

        return redirect()->route('admin.classes.index')
            ->with('success', "Level '{$request->level}' berhasil dihapus.")
            ->with('last_tab', 'tab-levels');
    }
}
