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
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'product_acc_code') && ! Schema::hasColumn('products', 'internal_code')) {
                $table->renameColumn('product_acc_code', 'internal_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'internal_code') && ! Schema::hasColumn('products', 'product_acc_code')) {
                $table->renameColumn('internal_code', 'product_acc_code');
            }
        });
    }
};
