<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guard_id')->constrained('guards')->cascadeOnDelete();
            $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent', 'half_day', 'weekly_off', 'leave', 'holiday', 'overtime'])->default('present');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->integer('late_minutes')->default(0);
            $table->decimal('overtime_hours', 5, 2)->default(0.00);
            $table->foreignId('verified_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            // GPS verification readiness
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();

            $table->unique(['guard_id', 'attendance_date'], 'guard_daily_attendance');
            $table->index(['attendance_date', 'status']);
        });

        Schema::create('attendance_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained('attendances')->cascadeOnDelete();
            $table->foreignId('changed_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->text('reason')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guard_id')->constrained('guards')->cascadeOnDelete();
            $table->enum('leave_type', ['casual', 'sick', 'earned', 'unpaid'])->default('casual');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days_count')->default(1);
            $table->text('reason');
            $table->string('document_path')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaves');
        Schema::dropIfExists('attendance_audits');
        Schema::dropIfExists('attendances');
    }
};
