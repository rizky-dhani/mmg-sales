<?php

namespace App\Console\Commands;

use App\DTOs\ReportFilterData;
use App\Exports\PipelineReportExport;
use App\Exports\SalesReportExport;
use App\Mail\ReportDigestMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class SendReportDigest extends Command
{
    protected $signature = 'reports:send-digest
        {--period=weekly : daily|weekly}
        {--role=Management Director : Role to target}';

    protected $description = 'Email scheduled report digests to role-based recipients';

    public function handle(): int
    {
        $period = $this->option('period');
        $role = $this->option('role');

        $users = User::role($role)->get();

        if ($users->isEmpty()) {
            $this->warn("No users found with role '{$role}'");

            return self::SUCCESS;
        }

        $sent = 0;

        foreach ($users as $user) {
            $filterData = $this->filterForUser($user, $role);

            $filename = 'digest_'.strtolower(str_replace(' ', '_', $role)).'_'.$user->id.'_'.now()->format('Ymd_His').'.xlsx';
            $diskPath = 'reports/'.$filename;

            Excel::store(new SalesReportExport($filterData), $diskPath, 'local');
            Excel::store(new PipelineReportExport($filterData), $diskPath, 'local');

            $absolutePath = storage_path('app/'.$diskPath);

            Mail::to($user->email)->send(new ReportDigestMail(
                period: $period,
                userName: $user->name,
                attachmentPath: $absolutePath,
            ));

            $sent++;
        }

        $this->info("Sent {$period} digest to {$sent} user(s) with role '{$role}'");

        return self::SUCCESS;
    }

    protected function filterForUser(User $user, string $role): ReportFilterData
    {
        $filters = [
            'start_date' => now()->startOfMonth()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ];

        if (in_array($role, ['Sales Regional Manager', 'Sales Area Manager'])) {
            $filters['territory_id'] = $user->territory_id;
        } elseif (in_array($role, ['Sales Supervisor', 'Sales Staff'])) {
            $filters['user_id'] = $user->id;
        }

        return ReportFilterData::fromArray($filters);
    }
}