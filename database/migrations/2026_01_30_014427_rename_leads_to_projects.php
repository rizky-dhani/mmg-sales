<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('leads') && ! Schema::hasTable('projects')) {
            Schema::rename('leads', 'projects');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('projects') && ! Schema::hasTable('leads')) {
            Schema::rename('projects', 'leads');
        }
    }
};
