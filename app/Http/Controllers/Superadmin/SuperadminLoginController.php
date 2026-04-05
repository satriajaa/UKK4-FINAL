<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperadminLoginController extends Controller
{
    /**
     * Show superadmin login form
     */
    public function showLoginForm()
    {
        // Check if already logged in as superadmin
        if (Auth::check() && Auth::user()->role === 'super_admin') {
            return redirect()->route('superadmin.dashboard');
        }

        return view('superadmin.login');
    }

    /**
     * Handle superadmin login
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi',
            'password.required' => 'Kata sandi wajib diisi',
        ]);

        // Attempt login with username only (superadmin doesn't have email usually)
        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
            'role' => 'super_admin', // IMPORTANT: Only allow super_admin role
        ];

        if (Auth::attempt($credentials, $request->remember)) {
            $user = Auth::user();

            // Double check role (security layer)
            if ($user->role !== 'super_admin') {
                Auth::logout();
                return back()->withErrors([
                    'username' => 'Akses ditolak. Anda bukan superadmin.',
                ])->withInput();
            }

            // Regenerate session to prevent fixation attacks
            $request->session()->regenerate();

            // Set additional session flag for superadmin
            session(['is_superadmin' => true]);

            // Log activity
            ActivityLog::log(
                action: 'superadmin_login',
                description: "Superadmin {$user->full_name} berhasil login",
                schoolId: null, // Superadmin tidak terikat ke sekolah
                userId: $user->id
            );

            return redirect()->route('superadmin.dashboard')->with('success',
                "Selamat datang kembali, {$user->full_name}!"
            );
        }

        // Login failed
        return back()->withErrors([
            'username' => 'Username atau kata sandi salah.',
        ])->withInput();
    }

    /**
     * Handle superadmin logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Log activity before logout
        if ($user) {
            ActivityLog::log(
                action: 'superadmin_logout',
                description: "Superadmin {$user->full_name} melakukan logout",
                schoolId: null,
                userId: $user->id
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('superadmin.login')->with('success',
            'Anda telah berhasil logout.'
        );
    }
}
