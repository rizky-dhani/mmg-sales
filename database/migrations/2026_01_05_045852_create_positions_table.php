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
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // HEAD, PM/JPM/PE, RSM/ASM, SPV, SR
            $table->string('code')->unique(); // Unique code for the position
            $table->integer('level'); // Hierarchy level (1 for HEAD, 2 for RSM/ASM, etc.)
            $table->unsignedBigInteger('parent_id')->nullable(); // For hierarchical relationship
            $table->unsignedBigInteger('department_id'); // Link to department
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('positions')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
