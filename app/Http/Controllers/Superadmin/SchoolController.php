<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SchoolController extends Controller
{
    /**
     * Show registration form.
     */
    public function create()
    {
        return view('superadmin.school');
    }

    /**
     * Store new school + admin account.
     */
    public function store(Request $request)
    {
        $request->validate([
            // School
            'name'           => 'required|string|max:255',
            'npsn'           => 'required|digits:8|unique:schools,npsn',
            'email'          => 'required|email|max:255',
            'phone'          => 'nullable|string|max:30',
            'address'        => 'required|string',
            // Admin
            'admin_name'     => 'required|string|max:255',
            'username'       => 'required|string|max:50|unique:users,username|alpha_dash',
            'admin_email'    => 'nullable|email|max:255',
            'password'       => ['required', 'confirmed', Password::min(8)],
        ], [
            'npsn.digits'    => 'NPSN harus tepat 8 digit angka.',
            'npsn.unique'    => 'NPSN ini sudah terdaftar di sistem.',
            'username.unique' => 'Username sudah digunakan.',
        ]);

        // 1. Create school
        $school = School::create([
            'name'    => $request->name,
            'npsn'    => $request->npsn,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'address' => $request->address,
            'status'  => 'active',
        ]);

        // 2. Create admin account attached to school
        User::create([
            'school_id' => $school->id,
            'full_name' => $request->admin_name,
            'username'  => $request->username,
            'email'     => $request->admin_email ?? $request->email,
            'password'  => Hash::make($request->password),
            'role'      => 'school_admin',
            'status'    => 'approved',
        ]);

        return redirect()->route('superadmin.dashboard')
            ->with('success', "Sekolah \"{$school->name}\" dan akun admin berhasil ditambahkan!");
    }

    /**
     * Show edit form.
     */
    public function edit(School $school)
    {
        $school->load(['users' => fn($q) => $q->where('role', 'school_admin')]);
        return view('superadmin.schools.edit', compact('school'));
    }

    /**
     * Update school data.
     */
    public function update(Request $request, School $school)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'npsn'    => "required|digits:8|unique:schools,npsn,{$school->id}",
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:30',
            'address' => 'required|string',
            'status'  => 'required|in:active,inactive',
        ]);

        $school->update($request->only(['name','npsn','email','phone','address','status']));

        return redirect()->route('superadmin.dashboard')
            ->with('success', "Data sekolah \"{$school->name}\" berhasil diperbarui.");
    }

    /**
     * Delete school and all related users.
     */
    public function destroy(School $school)
    {
        $name = $school->name;
        // Users will be cascade-deleted via FK constraint (or do it manually)
        $school->users()->delete();
        $school->delete();

        return redirect()->route('superadmin.dashboard')
            ->with('success', "Sekolah \"{$name}\" berhasil dihapus dari sistem.");
    }
}
