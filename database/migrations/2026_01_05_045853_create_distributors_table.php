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
        Schema::create('distributors', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Distributor name
            $table->string('code')->unique(); // Unique distributor code
            $table->text('address')->nullable(); // Address
            $table->string('city')->nullable(); // City
            $table->string('phone')->nullable(); // Phone number
            $table->string('email')->nullable(); // Email
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
        Schema::dropIfExists('distributors');
    }
};
