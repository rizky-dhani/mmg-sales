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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The Sales Rep who performed it

            $table->string('type'); // call, email, meeting, presentation, demo, etc.
            $table->string('subject');
            $table->text('description')->nullable();

            $table->dateTime('performed_at');
            $table->integer('duration_minutes')->nullable();

            // Optional: categorization of the result
            $table->string('outcome')->nullable(); // interested, no answer, postponed, etc.

            $table->timestamps();

            // Index for performance tracking
            $table->index(['project_id', 'performed_at']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
