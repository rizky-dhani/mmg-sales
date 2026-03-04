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
        Schema::table('sub_segments', function (Blueprint $table) {
            $table->string('sub_segment_code', 255)->nullable()->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('sub_segments', function (Blueprint $table) {
            $table->dropColumn('sub_segment_code');
        });
    }
};
