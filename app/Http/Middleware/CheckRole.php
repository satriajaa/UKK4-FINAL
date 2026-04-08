<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            // Belum login - redirect ke halaman login
            return redirect()->route('login')
                           ->with('error', 'Silakan login terlebih dahulu!');
        }

        // Ambil role user yang sedang login
        $userRole = Auth::user()->role;

        // Cek apakah role user sesuai dengan yang diizinkan
        if (in_array($userRole, $roles)) {
            // Role sesuai - lanjutkan request
            return $next($request);
        } else {
            // Role tidak sesuai - redirect dengan error
            if ($userRole === 'school_admin') {
                return redirect()->route('admin.dashboard')
                               ->with('error', 'Anda tidak memiliki akses ke halaman ini!');
            } else {
                return redirect()->route('user.dashboard')
                               ->with('error', 'Anda tidak memiliki akses ke halaman ini!');
            }
        }
    }
}
