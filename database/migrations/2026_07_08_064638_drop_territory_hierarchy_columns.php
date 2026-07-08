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
        Schema::table('territories', function (Blueprint $table) {
            $table->dropColumn([
                'wilayah_code',
                'type',
                'level',
                'parent_id',
                'manager_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('territories', function (Blueprint $table) {
            $table->string('wilayah_code')->nullable()->after('name');
            $table->string('type')->after('wilayah_code');
            $table->unsignedInteger('level')->after('type');
            $table->foreignId('parent_id')->nullable()->after('level')->constrained('territories');
            $table->foreignId('manager_id')->nullable()->after('parent_id')->constrained('users');
        });
    }
};
