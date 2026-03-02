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
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('principal_id')->nullable()->constrained('principals')->nullOnDelete();

            if (Schema::hasColumn('order_items', 'product_id')) {
                $table->dropForeign('order_items_product_id_foreign');
                $table->dropForeign('order_items_product_id_foreign_new');
            }

            $table->unsignedBigInteger('product_id')->nullable()->after('principal_id');
            $table->foreign('product_id', 'order_items_product_id_foreign')
                ->references('id')
                ->on('products')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['principal_id']);
            $table->dropForeign('order_items_product_id_foreign');
            $table->dropColumn(['principal_id', 'product_id']);
        });
    }
};
