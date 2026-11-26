<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('incident_code')->unique();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->dateTime('incident_datetime');
            $table->string('reported_by');
            $table->enum('category', [
                'theft',
                'unauthorized_entry',
                'fire',
                'property_damage',
                'safety_incident',
                'visitor_dispute',
                'guard_misconduct',
                'other'
            ])->default('other');
            $table->text('description');
            $table->text('action_taken')->nullable();
            $table->string('involved_guards')->nullable();
            $table->enum('resolution_status', ['open', 'investigating', 'resolved', 'closed'])->default('open');
            $table->boolean('confidential')->default(false);
            $table->string('attachment')->nullable();
            $table->dateTime('resolved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_code')->unique();
            $table->date('expense_date');
            $table->enum('category', [
                'uniform_expenses',
                'equipment_expenses',
                'transportation',
                'recruitment',
                'office_rent',
                'electricity',
                'staff_salaries',
                'fuel_and_patrol',
                'other'
            ])->default('other');
            $table->decimal('amount', 12, 2);
            $table->enum('payment_mode', ['cash', 'bank_transfer', 'upi', 'cheque'])->default('bank_transfer');
            $table->string('payee');
            $table->string('reference_number')->nullable();
            $table->string('receipt_path')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('incidents');
    }
};
