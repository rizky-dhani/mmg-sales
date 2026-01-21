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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The Sales Rep
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('contact_id')->nullable()->constrained()->onDelete('set null');

            $table->dateTime('visit_started_at')->nullable();
            $table->dateTime('visit_ended_at')->nullable();
            $table->string('location')->nullable(); // Where the meeting happened

            // The "Intent" (Rep's input before/during visit)
            $table->string('purpose'); // Why are they visiting?
            $table->text('expectations'); // What do they expect?
            $table->text('targets'); // What are the targets?

            // The "Outcome" (Rep's input after visit)
            $table->text('summary_notes')->nullable();

            // The "Stakeholder Evaluation" (Stakeholder's input)
            $table->text('stakeholder_feedback')->nullable();
            $table->boolean('is_worth_keeping')->nullable(); // Decision on customer value

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
