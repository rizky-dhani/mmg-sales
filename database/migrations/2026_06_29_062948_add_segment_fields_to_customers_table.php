<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('segment_id')->nullable()->after('cd_ncd_type')->constrained()->nullOnDelete();
            $table->foreignId('sub_segment_id')->nullable()->after('segment_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['segment_id']);
            $table->dropForeign(['sub_segment_id']);
            $table->dropColumn(['segment_id', 'sub_segment_id']);
        });
    }
};
