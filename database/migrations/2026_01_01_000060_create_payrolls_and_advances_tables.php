<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_advances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guard_id')->constrained('guards')->cascadeOnDelete();
            $table->string('advance_code')->unique();
            $table->decimal('amount', 12, 2);
            $table->date('request_date');
            $table->enum('status', ['pending', 'approved', 'recovered', 'rejected'])->default('pending');
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('recovered_amount', 12, 2)->default(0);
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->string('payroll_code')->unique();
            $table->foreignId('guard_id')->constrained('guards')->cascadeOnDelete();
            $table->unsignedTinyInteger('month'); // 1-12
            $table->unsignedSmallInteger('year');
            $table->enum('salary_type', ['monthly', 'daily'])->default('monthly');
            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->decimal('allowances', 12, 2)->default(0);
            $table->decimal('overtime_hours', 6, 2)->default(0);
            $table->decimal('overtime_rate', 10, 2)->default(0);
            $table->decimal('overtime_amount', 12, 2)->default(0);
            $table->unsignedTinyInteger('total_working_days')->default(30);
            $table->decimal('present_days', 4, 1)->default(0);
            $table->decimal('leave_deductions', 12, 2)->default(0);
            $table->decimal('advance_deductions', 12, 2)->default(0);
            $table->decimal('other_deductions', 12, 2)->default(0);
            $table->decimal('gross_salary', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2)->default(0);
            $table->enum('status', ['pending', 'approved', 'paid'])->default('pending');
            $table->date('payment_date')->nullable();
            $table->enum('payment_mode', ['bank_transfer', 'cash', 'cheque', 'upi', 'neft'])->nullable();
            $table->string('payment_reference')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('generated_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['guard_id', 'month', 'year'], 'guard_month_payroll');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('salary_advances');
    }
};
