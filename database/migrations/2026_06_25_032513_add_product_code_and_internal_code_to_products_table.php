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
            if (! Schema::hasColumn('products', 'product_code')) {
                $table->string('product_code', 30)->unique()->nullable()->after('id');
            }
            if (! Schema::hasColumn('products', 'internal_code')) {
                $table->string('internal_code', 50)->nullable()->after('product_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['product_code', 'internal_code']);
        });
    }
};
