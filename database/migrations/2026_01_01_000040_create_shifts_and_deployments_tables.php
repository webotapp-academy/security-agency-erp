<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Morning, Evening, Night, General
            $table->string('code', 20)->unique();
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('duration_hours', 4, 2)->default(8.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('deployments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guard_id')->constrained('guards')->cascadeOnDelete();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->string('designation')->default('Security Guard');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('duty_instructions')->nullable();
            $table->enum('status', ['active', 'transferred', 'relieved'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // To ensure query performance and logic checks
            $table->index(['guard_id', 'status']);
            $table->index(['site_id', 'status']);
        });

        Schema::create('duty_rosters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->foreignId('guard_id')->constrained('guards')->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained('shifts')->cascadeOnDelete();
            $table->date('roster_date');
            $table->enum('status', ['scheduled', 'completed', 'absent', 'replaced'])->default('scheduled');
            $table->foreignId('replacement_guard_id')->nullable()->constrained('guards')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['site_id', 'guard_id', 'roster_date', 'shift_id'], 'roster_unique_slot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('duty_rosters');
        Schema::dropIfExists('deployments');
        Schema::dropIfExists('shifts');
    }
};
