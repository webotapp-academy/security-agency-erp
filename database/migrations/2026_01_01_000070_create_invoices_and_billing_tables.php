<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->string('billing_period'); // e.g. "November 2026"
            $table->date('issue_date');
            $table->date('due_date');
            $table->unsignedInteger('guard_count')->default(1);
            $table->decimal('rate_per_guard', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('additional_charges', 12, 2)->default(0);
            $table->string('additional_charges_desc')->nullable();
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('gst_rate', 5, 2)->default(18.00); // 18% GST
            $table->decimal('cgst_amount', 12, 2)->default(0);
            $table->decimal('sgst_amount', 12, 2)->default(0);
            $table->decimal('igst_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('balance_amount', 12, 2)->default(0);
            $table->enum('status', ['draft', 'sent', 'partially_paid', 'paid', 'overdue'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('payment_receipt_number')->unique();
            $table->date('payment_date');
            $table->decimal('amount', 12, 2);
            $table->enum('payment_mode', ['bank_transfer', 'cheque', 'cash', 'upi', 'neft'])->default('bank_transfer');
            $table->string('transaction_reference')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_payments');
        Schema::dropIfExists('invoices');
    }
};
