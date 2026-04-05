<?php
// database/migrations/xxxx_add_approval_columns_to_borrowings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            // Ganti enum status lama → tambah pending, rejected, return_requested
            $table->enum('status', [
                'pending',           // Siswa request, nunggu acc admin
                'borrowed',          // Admin approve pinjam
                'rejected',          // Admin tolak request pinjam
                'return_requested',  // Siswa minta balikin, nunggu acc admin
                'returned',          // Admin approve pengembalian
                'late',              // Terlambat (auto)
            ])->default('pending')->change();

            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('return_requested_at')->nullable();
            $table->text('rejection_reason')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropColumn(['approved_by', 'approved_at', 'return_requested_at', 'rejection_reason']);
            $table->enum('status', ['borrowed', 'returned', 'late'])->default('borrowed')->change();
        });
    }
};
