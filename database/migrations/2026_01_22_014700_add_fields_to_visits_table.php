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
        Schema::table('visits', function (Blueprint $table) {
            $table->string('visit_type')->after('contact_id');
            $table->string('meeting_link')->nullable()->after('visit_type');
            $table->string('messaging_platform')->nullable()->after('meeting_link');
            $table->integer('confidence_level')->default(0)->after('is_worth_keeping');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn(['visit_type', 'meeting_link', 'messaging_platform', 'confidence_level']);
        });
    }
};
