<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Username atau email wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginField => $request->login,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->remember)) {
            $user = Auth::user();

            // Check user status
            if ($user->status === 'pending') {
                Auth::logout();
                return back()->withErrors([
                    'login' => 'Akun Anda masih menunggu persetujuan admin.',
                ])->withInput();
            }

            if ($user->status === 'rejected') {
                Auth::logout();
                return back()->withErrors([
                    'login' => 'Akun Anda ditolak oleh admin.',
                ])->withInput();
            }

            if ($user->status === 'inactive') {
                Auth::logout();
                return back()->withErrors([
                    'login' => 'Akun Anda tidak aktif. Hubungi admin.',
                ])->withInput();
            }

            // Log activity
            ActivityLog::log(
                action: 'login',
                description: "User {$user->full_name} melakukan login",
                schoolId: $user->school_id,
                userId: $user->id
            );

            // Redirect based on role
            return $this->redirectBasedOnRole($user);
        }

        return back()->withErrors([
            'login' => 'Username/email atau password salah.',
        ])->withInput();
    }

    protected function redirectBasedOnRole($user)
    {
        switch ($user->role) {
            case 'super_admin':
                return redirect()->route('superadmin.dashboard');
            case 'school_admin':
                return redirect()->route('admin.dashboard');
            case 'student':
                return redirect()->route('student.dashboard');
            default:
                return redirect()->route('landing');
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        // Log activity
        if ($user) {
            ActivityLog::log(
                action: 'logout',
                description: "User {$user->full_name} melakukan logout",
                schoolId: $user->school_id,
                userId: $user->id
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }
}
