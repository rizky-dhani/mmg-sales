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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('internal_code')->unique(); // Internal code
            $table->string('principle_code')->nullable(); // Principle code
            $table->string('name'); // Item name
            $table->unsignedBigInteger('principal_id'); // Link to principal
            $table->text('description')->nullable(); // Description
            $table->decimal('unit_price', 15, 2)->default(0); // Unit price
            $table->string('unit')->default('unit'); // Unit of measure
            $table->boolean('is_active')->default(true); // Active status
            $table->timestamps();

            $table->foreign('principal_id')->references('id')->on('principals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
