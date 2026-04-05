<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $schoolId    = Auth::user()->school_id;
        $setting     = Setting::getForSchool($schoolId);
        $school      = Auth::user()->school;
        $staffAdmins = User::where('school_id', $schoolId)
            ->where('role', 'school_admin')
            ->where('id', '!=', Auth::id())
            ->get();

        return view('admin.settings', compact('setting', 'school', 'staffAdmins'));
    }

    public function updateApp(Request $request)
    {
        $request->validate([
            'address'         => 'nullable|string|max:255',
            'phone'           => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:100',
            'max_borrow_days' => 'required|integer|min:1|max:90',
            'fine_per_day'    => 'required|numeric|min:0',
        ]);

        $schoolId = Auth::user()->school_id;

        Auth::user()->school->update([
            'address' => $request->address,
            'phone'   => $request->phone,
            'email'   => $request->email,
        ]);

        Setting::getForSchool($schoolId)->update([
            'max_borrow_days' => $request->max_borrow_days,
            'fine_per_day'    => $request->fine_per_day,
        ]);

        ActivityLog::log('update_settings', 'Pengaturan aplikasi diperbarui', $schoolId);

        return back()->with('success', 'Pengaturan berhasil disimpan.')->with('active_tab', 'app');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'full_name' => 'required|string|max:100',
            'username'  => 'required|string|max:50|unique:users,username,' . $user->id,
            'email'     => 'nullable|email|max:100|unique:users,email,' . $user->id,
            'photo'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($user->photo) Storage::disk('public')->delete($user->photo);
            $user->photo = $request->file('photo')->store('photos', 'public');
        }

        $user->update([
            'full_name' => $request->full_name,
            'username'  => $request->username,
            'email'     => $request->email,
            'photo'     => $user->photo,
        ]);

        ActivityLog::log('update_profile', 'Profil diperbarui', $user->school_id);

        return back()->with('success', 'Profil berhasil diperbarui.')->with('active_tab', 'profile');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/',
        ], [
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, dan angka.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Password lama tidak sesuai.'])
                ->with('active_tab', 'password');
        }

        $user->update(['password' => Hash::make($request->password)]);

        ActivityLog::log('change_password', 'Password diubah', $user->school_id);

        return back()->with('success', 'Password berhasil diperbarui.')->with('active_tab', 'password');
    }

    /**
     * Tambah staf admin baru (TERPISAH dari updateProfile)
     */
    public function storeStaff(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:100',
            'username'  => 'required|string|max:50|unique:users,username',
            'email'     => 'nullable|email|max:100|unique:users,email',
            'password'  => 'required|string|min:8',
        ]);

        $schoolId = Auth::user()->school_id;

        $staff = User::create([
            'school_id'   => $schoolId,
            'full_name'   => $request->full_name,
            'username'    => $request->username,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'role'        => 'school_admin',
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        ActivityLog::log('add_staff', "Staf admin ditambahkan: {$staff->full_name}", $schoolId);

        return back()
            ->with('success', "Staf admin \"{$staff->full_name}\" berhasil ditambahkan.")
            ->with('active_tab', 'profile');
    }

    /**
     * Hapus staf admin
     */
    public function destroyStaff(User $user)
    {
        if ($user->school_id !== Auth::user()->school_id || $user->id === Auth::id()) {
            return back()->with('error', 'Tidak dapat menghapus akun ini.');
        }

        $name = $user->full_name;
        $user->delete();

        ActivityLog::log('delete_staff', "Staf admin dihapus: {$name}", Auth::user()->school_id);

        return back()
            ->with('success', "Staf admin \"{$name}\" berhasil dihapus.")
            ->with('active_tab', 'profile');
    }
}
