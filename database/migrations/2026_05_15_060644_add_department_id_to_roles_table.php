<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // Drop existing unique index on [name, guard_name]
            $table->dropUnique('roles_name_guard_name_unique');

            // Add department_id FK (nullable)
            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            // New unique index includes department_id
            // Allows: (Admin, web, 1), (Admin, web, 2)
            // Prevents duplicate (Admin, web, 1)
            $table->unique(['name', 'guard_name', 'department_id'], 'roles_name_guard_dept_unique');
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique('roles_name_guard_dept_unique');
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
            $table->unique(['name', 'guard_name']);
        });
    }
};
