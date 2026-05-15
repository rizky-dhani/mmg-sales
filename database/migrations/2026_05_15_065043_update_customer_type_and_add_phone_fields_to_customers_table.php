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
        // Step 1: Migrate existing data to new type values
        // hospital, clinic, pharmacy → hospital_clinic
        DB::table('customers')
            ->whereIn('type', ['hospital', 'clinic', 'pharmacy'])
            ->update(['type' => 'hospital_clinic']);

        // laboratory, distributor → other
        DB::table('customers')
            ->whereIn('type', ['laboratory', 'distributor'])
            ->update(['type' => 'other']);

        if (DB::getDriverName() === 'sqlite') {
            // SQLite doesn't support ENUM or MODIFY COLUMN.
            // The original migration created a CHECK constraint via $table->enum().
            // Change to string to remove the CHECK constraint and accept new values.
            Schema::table('customers', function (Blueprint $table) {
                $table->string('type', 50)->default('other')->change();
            });
        } else {
            // MySQL/MariaDB: Modify the ENUM values
            DB::statement("ALTER TABLE customers MODIFY COLUMN type ENUM('hospital_clinic', 'pt_cv', 'other') NOT NULL DEFAULT 'other'");
        }

        Schema::table('customers', function (Blueprint $table) {
            $table->string('phone_purchasing', 255)->nullable()->after('phone');
            $table->string('phone_finance', 255)->nullable()->after('phone_purchasing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['phone_purchasing', 'phone_finance']);
        });

        // Restore original ENUM first, then map data back (best-effort)
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('customers', function (Blueprint $table) {
                $table->string('type', 50)->default('other')->change();
            });
        } else {
            DB::statement("ALTER TABLE customers MODIFY COLUMN type ENUM('hospital', 'clinic', 'pharmacy', 'laboratory', 'distributor', 'other') NOT NULL DEFAULT 'other'");
        }

        // Best-effort reverse data migration
        DB::table('customers')
            ->where('type', 'hospital_clinic')
            ->update(['type' => 'hospital']);

        DB::table('customers')
            ->where('type', 'pt_cv')
            ->update(['type' => 'other']);
    }
};
