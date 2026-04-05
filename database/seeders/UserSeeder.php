<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\School;
use App\Models\ClassModel; // Pastikan nama model Kelas sesuai (ClassModel atau Classroom)

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Buat Data Sekolah Dummy
        $school = School::firstOrCreate(
            ['npsn' => '12345678'], // Cek unique key
            [
                'name' => 'SMK Negeri 1 Digital',
                'address' => 'Jl. Teknologi No. 1, Jakarta',
                'phone' => '021-1234567',
                'email' => 'info@smkn1digital.sch.id',
                'status' => 'active'
            ]
        );

        // 2. Buat Data Kelas Dummy untuk Sekolah tersebut
        $class = ClassModel::firstOrCreate(
            ['name' => 'XII RPL 1', 'school_id' => $school->id],
            ['major' => 'Rekayasa Perangkat Lunak'] // Sesuaikan dengan kolom di tabel classes
        );

        // ---------------------------------------------------------
        // 3. SEED USER: SUPERADMIN
        // ---------------------------------------------------------
        User::firstOrCreate(
            ['username' => 'superadmin'],
            [
                'full_name' => 'Super Administrator',
                'email' => 'superadmin@ruangbaca.com',
                'password' => Hash::make('password'), // Default password: password
                'role' => 'super_admin',
                'status' => 'approved',
                'school_id' => null, // Superadmin tidak terikat sekolah
                'class_id' => null,
            ]
        );

        // ---------------------------------------------------------
        // 4. SEED USER: ADMIN SEKOLAH
        // ---------------------------------------------------------
        User::firstOrCreate(
            ['username' => 'admin_smk'],
            [
                'full_name' => 'Admin SMK 1',
                'email' => 'admin@smkn1digital.sch.id',
                'password' => Hash::make('password'),
                'role' => 'school_admin',
                'status' => 'approved',
                'school_id' => $school->id,
                'class_id' => null,
            ]
        );

        // ---------------------------------------------------------
        // 5. SEED USER: SISWA (Status approved)
        // ---------------------------------------------------------
        User::firstOrCreate(
            ['username' => 'siswa01'],
            [
                'full_name' => 'Raffael Aditya',
                'email' => 'raffael@siswa.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'status' => 'approved', // Langsung aktif agar bisa login
                'school_id' => $school->id,
                'class_id' => $class->id,
                'student_id' => 'NISN-001',
            ]
        );

        // ---------------------------------------------------------
        // 6. SEED USER: SISWA (Status Pending - untuk tes validasi login)
        // ---------------------------------------------------------
        User::firstOrCreate(
            ['username' => 'siswa_pending'],
            [
                'full_name' => 'Siswa Baru Daftar',
                'email' => 'baru@siswa.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'status' => 'pending', // User ini seharusnya tidak bisa login
                'school_id' => $school->id,
                'class_id' => $class->id,
                'student_id' => null,
            ]
        );
    }
}
