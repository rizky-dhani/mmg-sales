<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['sales_type_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('sales_type_id')->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('sales_type_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('sales_type_id')->constrained('sales_types')->onDelete('cascade');
        });
    }
};
