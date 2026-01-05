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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Customer name
            $table->string('code')->unique(); // Unique customer code
            $table->string('type')->nullable(); // Type (end customer, distributor, etc.)
            $table->text('address')->nullable(); // Address
            $table->string('city')->nullable(); // City
            $table->string('phone')->nullable(); // Phone number
            $table->string('email')->nullable(); // Email
            $table->unsignedBigInteger('customer_group_id')->nullable(); // Link to customer group
            $table->unsignedBigInteger('area_city_id')->nullable(); // Link to area/city
            $table->text('description')->nullable(); // Description
            $table->boolean('is_active')->default(true); // Active status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};