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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('contact_person');
            $table->string('email');
            $table->string('phone');
            $table->enum('status', ['new', 'contacted', 'qualified', 'proposal', 'negotiation', 'won', 'lost'])->default('new');
            $table->enum('source', ['website', 'referral', 'cold_call', 'trade_show', 'partner', 'other'])->default('other');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->decimal('estimated_value', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('converted_at')->nullable();
            $table->timestamp('last_contacted_at')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
