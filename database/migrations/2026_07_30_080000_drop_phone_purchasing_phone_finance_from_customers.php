<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['phone_purchasing', 'phone_finance']);
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('phone_purchasing')->nullable()->after('phone');
            $table->string('phone_finance')->nullable()->after('phone_purchasing');
        });
    }
};
