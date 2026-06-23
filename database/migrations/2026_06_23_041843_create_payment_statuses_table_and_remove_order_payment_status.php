<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['pending', 'partial', 'full'])->default('pending');
            $table->decimal('amount', 16, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_statuses');

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'overdue'])->default('pending');
        });
    }
};
