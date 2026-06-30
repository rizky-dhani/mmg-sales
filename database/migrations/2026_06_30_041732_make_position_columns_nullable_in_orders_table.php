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
            $table->foreignId('head_position_id')->nullable()->change();
            $table->foreignId('rsm_asm_position_id')->nullable()->change();
            $table->foreignId('spv_position_id')->nullable()->change();
            $table->foreignId('sr_position_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('head_position_id')->nullable(false)->change();
            $table->foreignId('rsm_asm_position_id')->nullable(false)->change();
            $table->foreignId('spv_position_id')->nullable(false)->change();
            $table->foreignId('sr_position_id')->nullable(false)->change();
        });
    }
};
