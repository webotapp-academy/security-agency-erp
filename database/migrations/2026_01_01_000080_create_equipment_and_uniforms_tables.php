<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->unique();
            $table->string('name'); // Uniform, Shoes, ID card, Torch, Baton, Raincoat, Safety helmet, Walkie-talkie
            $table->enum('category', ['uniform', 'shoes', 'id_card', 'torch', 'baton', 'raincoat', 'safety_gear', 'communication', 'other'])->default('other');
            $table->unsignedInteger('total_quantity')->default(0);
            $table->unsignedInteger('available_quantity')->default(0);
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->string('condition_status')->default('Good'); // Good, Fair, New
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('equipment_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_item_id')->constrained('equipment_items')->cascadeOnDelete();
            $table->foreignId('guard_id')->constrained('guards')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->date('issue_date');
            $table->date('return_date')->nullable();
            $table->string('condition_on_issue')->default('Good');
            $table->string('condition_on_return')->nullable();
            $table->enum('status', ['issued', 'returned', 'lost', 'damaged'])->default('issued');
            $table->decimal('penalty_amount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_assignments');
        Schema::dropIfExists('equipment_items');
    }
};
