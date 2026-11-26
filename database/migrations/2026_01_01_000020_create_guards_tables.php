<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guards', function (Blueprint $table) {
            $table->id();
            $table->string('guard_code')->unique();
            $table->string('full_name');
            $table->string('father_name')->nullable();
            $table->string('photo')->nullable();
            $table->date('dob')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->default('Male');
            $table->string('phone', 20);
            $table->string('alternate_phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('permanent_address')->nullable();
            $table->text('current_address')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->string('aadhaar_reference')->nullable(); // stored securely / masked
            $table->string('pan_number', 20)->nullable();
            $table->enum('police_verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->string('police_verification_ref')->nullable();
            $table->enum('background_verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->date('joining_date')->nullable();
            $table->enum('employment_type', ['full_time', 'part_time', 'contract'])->default('full_time');
            $table->string('designation')->default('Security Guard');
            $table->string('qualification')->nullable();
            $table->text('training_details')->nullable();
            $table->string('experience_years')->nullable();
            $table->string('blood_group', 10)->nullable();
            
            // Bank details (restricted access)
            $table->string('bank_name')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code', 20)->nullable();
            
            // Salary setup
            $table->enum('salary_type', ['monthly', 'daily'])->default('monthly');
            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->decimal('allowances', 12, 2)->default(0);
            $table->decimal('overtime_rate', 10, 2)->default(0);
            
            $table->enum('status', ['active', 'inactive', 'suspended', 'resigned', 'terminated'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('guard_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guard_id')->constrained('guards')->cascadeOnDelete();
            $table->enum('document_type', ['photograph', 'aadhaar', 'pan', 'police_verification', 'training_certificate', 'appointment_letter', 'other'])->default('other');
            $table->string('document_title');
            $table->string('document_number')->nullable();
            $table->string('file_path');
            $table->date('expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guard_documents');
        Schema::dropIfExists('guards');
    }
};
