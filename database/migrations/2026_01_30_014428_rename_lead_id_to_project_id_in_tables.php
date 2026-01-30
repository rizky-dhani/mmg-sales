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
        if (Schema::hasTable('project_milestone') && Schema::hasColumn('project_milestone', 'lead_id')) {
            Schema::table('project_milestone', function (Blueprint $table) {
                $table->renameColumn('lead_id', 'project_id');
            });
        }

        if (Schema::hasTable('activities') && Schema::hasColumn('activities', 'lead_id')) {
            Schema::table('activities', function (Blueprint $table) {
                $table->renameColumn('lead_id', 'project_id');
            });
        }

        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'lead_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->renameColumn('lead_id', 'project_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('project_milestone') && Schema::hasColumn('project_milestone', 'project_id')) {
            Schema::table('project_milestone', function (Blueprint $table) {
                $table->renameColumn('project_id', 'lead_id');
            });
        }

        if (Schema::hasTable('activities') && Schema::hasColumn('activities', 'project_id')) {
            Schema::table('activities', function (Blueprint $table) {
                $table->renameColumn('project_id', 'lead_id');
            });
        }

        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'project_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->renameColumn('project_id', 'lead_id');
            });
        }
    }
};
