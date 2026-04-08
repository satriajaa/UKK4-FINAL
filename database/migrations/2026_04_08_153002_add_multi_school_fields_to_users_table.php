<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add multi-school fields
            $table->foreignId('school_id')->nullable()->after('id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('class_id')->nullable()->after('school_id')->constrained('classes')->onDelete('set null');

            // Modify/add fields
            $table->string('username', 50)->unique()->after('class_id');
            $table->string('full_name', 100)->after('password');
            $table->enum('role', ['super_admin', 'school_admin', 'student'])->after('full_name');
            $table->string('student_id', 20)->nullable()->after('role');
            $table->string('photo', 255)->nullable()->after('student_id');
            $table->enum('status', ['pending', 'approved', 'rejected', 'inactive'])->default('pending')->after('photo');
            $table->text('address')->nullable()->after('status');
            $table->string('phone', 15)->nullable()->after('address');

            // Approval tracking
            $table->foreignId('approved_by')->nullable()->after('phone')->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable()->after('approved_by');

            // Soft deletes
            $table->softDeletes();

            // Indexes
            $table->index('role');
            $table->index('status');
            $table->index('school_id');
            $table->unique(['school_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropForeign(['class_id']);
            $table->dropForeign(['approved_by']);

            $table->dropIndex(['role']);
            $table->dropIndex(['status']);
            $table->dropIndex(['school_id']);
            $table->dropUnique(['school_id', 'student_id']);

            $table->dropColumn([
                'school_id',
                'class_id',
                'username',
                'full_name',
                'role',
                'student_id',
                'photo',
                'status',
                'address',
                'phone',
                'approved_by',
                'approved_at',
                'deleted_at'
            ]);
        });
    }
};
