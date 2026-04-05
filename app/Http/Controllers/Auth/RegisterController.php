<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\School;
use App\Models\ClassModel;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {
        $schools = School::where('status', 'active')->get();
        $classes = ClassModel::all(); // Akan di-filter berdasarkan sekolah via AJAX

        return view('auth.register', compact('schools', 'classes'));
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'school_id' => 'required|exists:schools,id',
            'full_name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'nullable|email|max:100|unique:users,email',
            'class_id' => 'required|exists:classes,id',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[a-z]/',      // must contain lowercase
                'regex:/[A-Z]/',      // must contain uppercase
                'regex:/[0-9]/',      // must contain digit
            ],
            'terms' => 'required|accepted',
        ], [
            'school_id.required' => 'Sekolah wajib dipilih',
            'school_id.exists' => 'Sekolah tidak ditemukan',
            'full_name.required' => 'Nama lengkap wajib diisi',
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan, pilih yang lain',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'class_id.required' => 'Kelas wajib dipilih',
            'class_id.exists' => 'Kelas tidak ditemukan',
            'password.required' => 'Kata sandi wajib diisi',
            'password.min' => 'Kata sandi minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok',
            'password.regex' => 'Kata sandi harus mengandung huruf besar, huruf kecil, dan angka',
            'terms.required' => 'Anda harus menyetujui syarat dan ketentuan',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // HARDCODED: Role always 'student', status always 'pending'
            $user = User::create([
                'school_id' => $request->school_id,
                'class_id' => $request->class_id,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'full_name' => $request->full_name,
                'email' => $request->email,
                'role' => 'student', // HARDCODED - Cannot be changed by user
                'status' => 'pending', // Waiting for admin approval
                'student_id' => null, // Will be set by admin
            ]);

            // Log activity
            ActivityLog::log(
                action: 'register',
                description: "User baru mendaftar: {$user->full_name} ({$user->username})",
                schoolId: $user->school_id,
                userId: $user->id
            );

            // Redirect to login with success message
            return redirect()->route('login')->with('success',
                'Pendaftaran berhasil! Akun Anda sedang menunggu persetujuan dari admin sekolah. Anda akan menerima notifikasi setelah akun disetujui.'
            );

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.'])
                ->withInput();
        }
    }

    /**
     * Get classes by school (AJAX)
     */
    public function getClassesBySchool(Request $request)
    {
        $classes = ClassModel::where('school_id', $request->school_id)->get();
        return response()->json($classes);
    }
}
