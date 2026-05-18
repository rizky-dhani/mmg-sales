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
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('name')->after('last_name');
        });

        DB::table('contacts')->update([
            'name' => DB::raw('CONCAT(TRIM(first_name), " ", TRIM(last_name))'),
        ]);

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('first_name')->after('contact_code');
            $table->string('last_name')->after('first_name');
        });

        DB::table('contacts')->update([
            'first_name' => DB::raw("SUBSTRING_INDEX(name, ' ', 1)"),
            'last_name' => DB::raw("SUBSTRING_INDEX(name, ' ', -1)"),
        ]);

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
};
