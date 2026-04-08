<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $query = User::with('class')
            ->where('school_id', $schoolId)
            ->where('role', 'student');

        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('class_id')) $query->where('class_id', $request->class_id);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('full_name',  'like', "%$s%")
                ->orWhere('username', 'like', "%$s%")
                ->orWhere('student_id','like',"%$s%")
            );
        }

        $members = $query->latest()->paginate(15)->withQueryString();

        $counts = [
            'all'      => User::where('school_id',$schoolId)->where('role','student')->count(),
            'approved' => User::where('school_id',$schoolId)->where('role','student')->where('status','approved')->count(),
            'pending'  => User::where('school_id',$schoolId)->where('role','student')->where('status','pending')->count(),
            'rejected' => User::where('school_id',$schoolId)->where('role','student')->where('status','rejected')->count(),
        ];

        return view('admin.members', compact('members', 'counts'));
    }

    // Admin tambah anggota manual (langsung aktif)
    public function store(Request $request)
    {
        $request->validate([
            'full_name'  => 'required|string|max:100',
            'username'   => 'required|string|max:50|unique:users,username',
            'email'      => 'nullable|email|max:100|unique:users,email',
            'student_id' => 'nullable|string|max:20',
            'class_id'   => 'required|exists:classes,id',
            'password'   => 'required|string|min:8',
        ]);

        $schoolId = Auth::user()->school_id;

        $user = User::create([
            'school_id'   => $schoolId,
            'class_id'    => $request->class_id,
            'full_name'   => $request->full_name,
            'username'    => $request->username,
            'email'       => $request->email,
            'student_id'  => $request->student_id,
            'password'    => Hash::make($request->password),
            'role'        => 'student',
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        ActivityLog::log('add_member', "Anggota ditambahkan: {$user->full_name}", $schoolId);

        return back()->with('success', "Anggota {$user->full_name} berhasil ditambahkan.");
    }

    // Edit data anggota
    public function update(Request $request, User $user)
    {
        $request->validate([
            'full_name'  => 'required|string|max:100',
            'username'   => 'required|string|max:50|unique:users,username,'.$user->id,
            'email'      => 'nullable|email|max:100|unique:users,email,'.$user->id,
            'student_id' => 'nullable|string|max:20',
            'class_id'   => 'required|exists:classes,id',
            'status'     => 'required|in:approved,pending,rejected',
        ]);

        $wasApproved = $user->status !== 'approved' && $request->status === 'approved';

        $user->update([
            'full_name'   => $request->full_name,
            'username'    => $request->username,
            'email'       => $request->email,
            'student_id'  => $request->student_id,
            'class_id'    => $request->class_id,
            'status'      => $request->status,
            'approved_by' => $wasApproved ? Auth::id() : $user->approved_by,
            'approved_at' => $wasApproved ? now() : $user->approved_at,
        ]);

        ActivityLog::log('update_member', "Anggota diupdate: {$user->full_name}", Auth::user()->school_id);

        return back()->with('success', "Data {$user->full_name} berhasil diperbarui.");
    }

    public function approve(User $user)
    {
        $user->update(['status' => 'approved', 'approved_by' => Auth::id(), 'approved_at' => now()]);
        ActivityLog::log('approve_member', "Anggota disetujui: {$user->full_name}", Auth::user()->school_id);
        return back()->with('success', "Akun {$user->full_name} berhasil disetujui.");
    }

    public function reject(User $user)
    {
        $user->update(['status' => 'rejected']);
        ActivityLog::log('reject_member', "Anggota ditolak: {$user->full_name}", Auth::user()->school_id);
        return back()->with('success', "Akun {$user->full_name} ditolak.");
    }

    public function destroy(User $user)
    {
        $name = $user->full_name;
        $user->delete();
        ActivityLog::log('delete_member', "Anggota dihapus: {$name}", Auth::user()->school_id);
        return back()->with('success', "Anggota {$name} berhasil dihapus.");
    }


}
