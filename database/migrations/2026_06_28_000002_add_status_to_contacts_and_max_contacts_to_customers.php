<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive'])->default('active')->after('name');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedSmallInteger('max_contact_persons')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('max_contact_persons');
        });
    }
};
