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
        Schema::table('activities', function (Blueprint $table) {
            // Make project_id nullable first
            $table->foreignId('project_id')->nullable()->change();

            if (! Schema::hasColumn('activities', 'customer_id')) {
                $table->foreignId('customer_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('activities', 'contact_id')) {
                $table->foreignId('contact_id')->nullable()->after('customer_id')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('activities', 'visit_started_at')) {
                $table->dateTime('visit_started_at')->nullable()->after('performed_at');
            }
            if (! Schema::hasColumn('activities', 'visit_ended_at')) {
                $table->dateTime('visit_ended_at')->nullable()->after('visit_started_at');
            }
            if (! Schema::hasColumn('activities', 'location')) {
                $table->string('location')->nullable()->after('visit_ended_at');
            }
            if (! Schema::hasColumn('activities', 'purpose')) {
                $table->string('purpose')->nullable()->after('location');
            }
            if (! Schema::hasColumn('activities', 'expectations')) {
                $table->text('expectations')->nullable()->after('purpose');
            }
            if (! Schema::hasColumn('activities', 'targets')) {
                $table->text('targets')->nullable()->after('expectations');
            }
            if (! Schema::hasColumn('activities', 'stakeholder_feedback')) {
                $table->text('stakeholder_feedback')->nullable()->after('targets');
            }
            if (! Schema::hasColumn('activities', 'is_worth_keeping')) {
                $table->boolean('is_worth_keeping')->default(true)->after('stakeholder_feedback');
            }
            if (! Schema::hasColumn('activities', 'confidence_level')) {
                $table->integer('confidence_level')->default(0)->after('is_worth_keeping');
            }
            if (! Schema::hasColumn('activities', 'next_contact_date')) {
                $table->date('next_contact_date')->nullable()->after('confidence_level');
            }
            if (! Schema::hasColumn('activities', 'follow_up_notes')) {
                $table->text('follow_up_notes')->nullable()->after('next_contact_date');
            }
            if (! Schema::hasColumn('activities', 'meeting_link')) {
                $table->string('meeting_link')->nullable()->after('follow_up_notes');
            }
            if (! Schema::hasColumn('activities', 'messaging_platform')) {
                $table->string('messaging_platform')->nullable()->after('meeting_link');
            }
        });

        // Migrate data from visits to activities
        if (Schema::hasTable('visits')) {
            $visits = DB::table('visits')->get();

            foreach ($visits as $visit) {
                DB::table('activities')->insert([
                    'project_id' => null, // explicitly set to null to avoid default value issues
                    'user_id' => $visit->user_id,
                    'customer_id' => $visit->customer_id,
                    'contact_id' => $visit->contact_id,
                    'type' => $visit->visit_type,
                    'subject' => $visit->purpose ?? 'Sales Visit',
                    'description' => $visit->summary_notes,
                    'performed_at' => $visit->visit_started_at,
                    'visit_started_at' => $visit->visit_started_at,
                    'visit_ended_at' => $visit->visit_ended_at,
                    'location' => $visit->location,
                    'purpose' => $visit->purpose,
                    'expectations' => $visit->expectations,
                    'targets' => $visit->targets,
                    'stakeholder_feedback' => $visit->stakeholder_feedback,
                    'is_worth_keeping' => $visit->is_worth_keeping,
                    'confidence_level' => $visit->confidence_level,
                    'next_contact_date' => $visit->next_contact_date,
                    'follow_up_notes' => $visit->follow_up_notes,
                    'meeting_link' => $visit->meeting_link,
                    'messaging_platform' => $visit->messaging_platform,
                    'created_at' => $visit->created_at,
                    'updated_at' => $visit->updated_at,
                ]);
            }

            Schema::dropIfExists('visits');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable(false)->change();
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['contact_id']);
            $table->dropColumn([
                'customer_id',
                'contact_id',
                'visit_started_at',
                'visit_ended_at',
                'location',
                'purpose',
                'expectations',
                'targets',
                'stakeholder_feedback',
                'is_worth_keeping',
                'confidence_level',
                'next_contact_date',
                'follow_up_notes',
                'meeting_link',
                'messaging_platform',
            ]);
        });
    }
};
