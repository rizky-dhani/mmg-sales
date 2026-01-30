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
        Schema::table('principals', function (Blueprint $table) {
            $table->decimal('annual_target', 16, 2)->default(0)->after('email');
            $table->enum('supplier_type', ['IVD', 'CL', 'Non-CL'])->nullable()->after('annual_target');
            $table->string('website')->nullable()->after('supplier_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('principals', function (Blueprint $table) {
            $table->dropColumn(['annual_target', 'supplier_type', 'website']);
        });
    }
};
