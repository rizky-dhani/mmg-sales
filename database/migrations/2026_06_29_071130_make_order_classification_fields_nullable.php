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
            $table->string('cd_ncd_type')->nullable()->change();
            $table->foreignId('segment_id')->nullable()->change();
            $table->string('sales_type_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('cd_ncd_type')->nullable(false)->change();
            $table->foreignId('segment_id')->nullable(false)->change();
            $table->string('sales_type_id')->nullable(false)->change();
        });
    }
};
