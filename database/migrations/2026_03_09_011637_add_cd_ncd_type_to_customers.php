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
        Schema::table('customers', function (Blueprint $table) {
            $table->enum('cd_ncd_type', ['CD', 'NCD'])->nullable();
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->timestamp('closed_at')->nullable()->after('estimated_completion_date');
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->string('customer_acc_code', 50)->nullable()->after('customer_code');
        });
        Schema::table('principals', function (Blueprint $table) {
            $table->string('principal_acc_code', 50)->nullable()->after('principal_code');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->string('product_acc_code', 50)->nullable()->after('product_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('cd_ncd_type');
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('closed_at');
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('customer_acc_code');
        });
        Schema::table('principals', function (Blueprint $table) {
            $table->dropColumn('principal_acc_code');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('product_acc_code');
        });
    }
};
