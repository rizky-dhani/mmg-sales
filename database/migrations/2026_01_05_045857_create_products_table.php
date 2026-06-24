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
            $table->string('product_code', 30)->unique()->nullable();
            $table->string('internal_code', 50)->nullable();
            $table->string('name');
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->decimal('unit_price', 12, 2);
            $table->string('unit_of_measure')->default('pcs');
            $table->boolean('is_active')->default(true);
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
