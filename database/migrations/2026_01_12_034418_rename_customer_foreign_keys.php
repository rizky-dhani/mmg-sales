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
        Schema::table('leads', function (Blueprint $table) {
            $table->renameColumn('customer_id', 'company_id');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->renameColumn('customer_id', 'company_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('end_customer_id', 'end_company_id');
            $table->renameColumn('customer_group_id', 'company_group_id');
            $table->renameColumn('original_customer_id', 'original_company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('original_company_id', 'original_customer_id');
            $table->renameColumn('company_group_id', 'customer_group_id');
            $table->renameColumn('end_company_id', 'end_customer_id');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->renameColumn('company_id', 'customer_id');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->renameColumn('company_id', 'customer_id');
        });
    }
};