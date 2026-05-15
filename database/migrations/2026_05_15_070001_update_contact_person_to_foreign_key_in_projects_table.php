<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite: drop column and re-add as foreign key
            Schema::table('projects', function (Blueprint $table) {
                $table->dropColumn('contact_person');
            });
            Schema::table('projects', function (Blueprint $table) {
                $table->foreignId('contact_person')->nullable()->constrained('contacts')->nullOnDelete();
            });
        } else {
            // MySQL/MariaDB: modify column type and add foreign key
            DB::statement('ALTER TABLE projects MODIFY COLUMN contact_person BIGINT(20) UNSIGNED NULL');
            Schema::table('projects', function (Blueprint $table) {
                $table->foreign('contact_person')->references('id')->on('contacts')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['contact_person']);
        });

        if (DB::getDriverName() === 'sqlite') {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropColumn('contact_person');
            });
            Schema::table('projects', function (Blueprint $table) {
                $table->string('contact_person');
            });
        } else {
            DB::statement('ALTER TABLE projects MODIFY COLUMN contact_person VARCHAR(255) NOT NULL');
        }
    }
};
