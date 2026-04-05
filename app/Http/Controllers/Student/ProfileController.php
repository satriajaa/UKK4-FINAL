<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\ClassModel;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show profile page.
     */
    public function index()
    {
        $user = Auth::user();

        $totalBorrowed = Borrowing::where('user_id', $user->id)->count();
        $activeBorrow  = Borrowing::where('user_id', $user->id)->whereIn('status', ['borrowed', 'late'])->count();
        $totalFine     = Borrowing::where('user_id', $user->id)->sum('fine');
        $avgRating     = Review::where('user_id', $user->id)->avg('rating') ?? 0;
        $classes       = ClassModel::where('school_id', $user->school_id)->orderBy('name')->get();

        return view('student.profile', compact(
            'user', 'totalBorrowed', 'activeBorrow', 'totalFine', 'avgRating', 'classes'
        ));
    }

    /**
     * Update profile info (and optional photo).
     * PATCH /student/profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'username'  => ['required', 'string', 'max:50', 'unique:users,username,' . $user->id],
            'email'     => ['required', 'email', 'max:100', 'unique:users,email,' . $user->id],
            'class_id'  => ['nullable', 'exists:classes,id'],
            'photo'     => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp'],
        ], [
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'username.unique'    => 'Username sudah digunakan.',
            'email.unique'       => 'Email sudah digunakan.',
            'photo.image'        => 'File harus berupa gambar.',
            'photo.max'          => 'Ukuran foto maksimal 2MB.',
        ]);

        $data = $request->only(['full_name', 'username', 'email', 'class_id']);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $data['photo'] = $request->file('photo')->store('photos/students', 'public');
        }

        $user->update($data);

        // Log activity
        if (class_exists(\App\Models\ActivityLog::class)) {
            \App\Models\ActivityLog::log('profile_updated', 'Siswa memperbarui profil');
        }

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update password.
     * PATCH /student/profile/password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required'         => 'Password baru wajib diisi.',
            'password.confirmed'        => 'Konfirmasi password tidak cocok.',
            'password.min'              => 'Password minimal 8 karakter.',
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('password_error', 'Password saat ini tidak sesuai.')
                         ->withInput();
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Log activity
        if (class_exists(\App\Models\ActivityLog::class)) {
            \App\Models\ActivityLog::log('password_changed', 'Siswa mengganti password');
        }

        return back()->with('password_success', 'Password berhasil diubah!');
    }
}
