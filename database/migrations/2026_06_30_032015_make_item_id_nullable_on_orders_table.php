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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('item_id')->nullable()->change();
            $table->integer('qty_hna')->nullable()->change();
            $table->bigInteger('total_hna_gross_sales')->nullable()->change();
            $table->bigInteger('net_sales_total')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('item_id')->nullable(false)->change();
            $table->integer('qty_hna')->nullable(false)->change();
            $table->bigInteger('total_hna_gross_sales')->nullable(false)->change();
            $table->bigInteger('net_sales_total')->nullable(false)->change();
        });
    }
};
