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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->enum('category', ['medical_equipment', 'pharmaceutical', 'consumables', 'diagnostics', 'other'])->default('other');
            $table->text('description')->nullable();
            $table->decimal('unit_price', 12, 2);
            $table->string('unit_of_measure')->default('pcs');
            $table->integer('stock_quantity')->default(0);
            $table->integer('minimum_stock')->default(10);
            $table->integer('reorder_quantity')->default(100);
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_prescription')->default(false);
            $table->string('manufacturer')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('storage_requirements')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
