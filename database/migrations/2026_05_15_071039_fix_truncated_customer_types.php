<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix customer types that were truncated during the ENUM migration.
     * Old values (hospital, clinic, pharmacy) that weren't in the new ENUM
     * were silently truncated to an empty string by MySQL.
     */
    public function up(): void
    {
        DB::table('customers')
            ->where('type', '')
            ->orWhereNull('type')
            ->update(['type' => 'other']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reversible operation - this is a data cleanup.
    }
};
