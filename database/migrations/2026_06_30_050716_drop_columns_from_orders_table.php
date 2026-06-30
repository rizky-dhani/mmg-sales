<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $fksToDrop = ['area_city_id', 'item_id', 'original_customer_id', 'sub_segment_id'];
        foreach ($fksToDrop as $column) {
            $fkName = "orders_{$column}_foreign";
            try {
                DB::statement("ALTER TABLE `orders` DROP FOREIGN KEY `{$fkName}`");
            } catch (\Exception $e) {
                // FK may have already been dropped in a partial previous run
            }
        }

        // Now drop all 11 columns
        $colsToDrop = [
            'area_city_id', 'customer_group_id', 'cd_ncd_type', 'ncd_subtype',
            'segment_id', 'item_id', 'sub_segment_id', 'original_customer_id',
            'discount_on', 'qty_hna', 'total_hna_gross_sales',
        ];
        foreach ($colsToDrop as $column) {
            if (Schema::hasColumn('orders', $column)) {
                DB::statement("ALTER TABLE `orders` DROP COLUMN `{$column}`");
            }
        }
    }

    public function down(): void
    {
        Schema::table('orders', function ($table) {
            $table->foreignId('area_city_id')->constrained('territories')->onDelete('cascade');
            $table->foreignId('customer_group_id')->nullable()->constrained()->onDelete('set null');
            $table->string('cd_ncd_type');
            $table->string('ncd_subtype')->nullable();
            $table->foreignId('segment_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->foreignId('sub_segment_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('original_customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->decimal('discount_on', 5, 2);
            $table->integer('qty_hna');
            $table->bigInteger('total_hna_gross_sales');
        });
    }
};
