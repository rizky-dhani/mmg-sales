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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->integer('tahun');
            $table->integer('bulan');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('head_position_id')->constrained('positions')->onDelete('cascade');
            $table->foreignId('pm_jpm_pe_position_id')->nullable()->constrained('positions')->onDelete('set null');
            $table->foreignId('rsm_asm_position_id')->constrained('positions')->onDelete('cascade');
            $table->foreignId('spv_position_id')->constrained('positions')->onDelete('cascade');
            $table->foreignId('sr_position_id')->constrained('positions')->onDelete('cascade');
            $table->foreignId('area_city_id')->constrained('territories')->onDelete('cascade');
            $table->foreignId('end_customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('customer_group_id')->nullable()->constrained()->onDelete('set null');
            $table->string('cd_ncd_type');
            $table->string('ncd_subtype')->nullable();
            $table->foreignId('segment_id')->constrained()->onDelete('cascade');
            $table->foreignId('principal_id')->constrained()->onDelete('cascade');
            $table->string('reg_inst');
            $table->foreignId('sales_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->integer('qty_hna');
            $table->bigInteger('total_hna_gross_sales');
            $table->decimal('discount_on', 5, 2);
            $table->bigInteger('net_sales_total');
            $table->foreignId('sub_segment_id')->nullable()->constrained()->onDelete('set null');
            $table->string('jual_kso');
            $table->foreignId('distributor_id')->constrained()->onDelete('cascade');

            // Standard order fields
            $table->string('order_number')->unique();
            $table->foreignId('original_customer_id')->nullable()->constrained('customers')->onDelete('set null'); // Original customer if different from end customer
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['draft', 'pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'returned'])->default('draft');
            $table->decimal('subtotal', 16, 2)->default(0);
            $table->decimal('tax_amount', 16, 2)->default(0);
            $table->decimal('discount_amount', 16, 2)->default(0);
            $table->decimal('total_amount', 16, 2)->default(0);
            $table->date('order_date');
            $table->date('expected_delivery_date')->nullable();
            $table->date('actual_delivery_date')->nullable();
            $table->text('shipping_address')->nullable();
            $table->text('billing_address')->nullable();
            $table->text('notes')->nullable();
            $table->string('payment_method')->nullable();
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'overdue'])->default('pending');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
