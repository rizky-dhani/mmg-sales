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
        if (Schema::hasTable('lead_milestone') && ! Schema::hasTable('project_milestone')) {
            Schema::rename('lead_milestone', 'project_milestone');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('project_milestone') && ! Schema::hasTable('lead_milestone')) {
            Schema::rename('project_milestone', 'lead_milestone');
        }
    }
};
