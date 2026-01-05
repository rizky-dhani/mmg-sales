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
        Schema::create('sub_segments', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Sub-segment name
            $table->string('code')->unique(); // Unique code
            $table->unsignedBigInteger('segment_id'); // Link to parent segment
            $table->text('description')->nullable(); // Description
            $table->timestamps();
            
            $table->foreign('segment_id')->references('id')->on('segments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_segments');
    }
};