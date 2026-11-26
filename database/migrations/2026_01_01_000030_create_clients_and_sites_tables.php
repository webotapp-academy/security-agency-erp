<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('client_code')->unique();
            $table->string('client_name');
            $table->string('company_name')->nullable();
            $table->string('contact_person');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->text('billing_address')->nullable();
            $table->string('gstin', 30)->nullable();
            $table->string('state')->default('Assam');
            $table->string('pincode', 10)->nullable();
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->decimal('contract_amount', 12, 2)->default(0);
            $table->string('payment_terms')->default('Net 30');
            $table->string('contract_document')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('site_code')->unique();
            $table->string('site_name');
            $table->string('site_type')->default('Office'); // Residential apartment, Factory, Warehouse, Office, Shopping mall, School, Hospital, Construction site, Other
            $table->text('address');
            $table->string('contact_person')->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->integer('required_guards')->default(1);
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('duty_instructions')->nullable();
            $table->text('shift_requirements')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sites');
        Schema::dropIfExists('clients');
    }
};
