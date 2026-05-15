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

        if (DB::getDriverName() === 'sqlite') {
            // Restore ENUM-like CHECK constraint in SQLite via a string column
            Schema::table('customers', function (Blueprint $table) {
                $table->string('type', 50)->default('other')->change();
            });
        } else {
            DB::statement("ALTER TABLE customers MODIFY COLUMN type ENUM('hospital', 'clinic', 'pharmacy', 'laboratory', 'distributor', 'other') NOT NULL DEFAULT 'other'");
        }
    }
};
