<?php

namespace App\Exports;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class VisitsMultiSheetExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(
        protected Builder $query,
        protected string $groupBy = 'user' // 'user' or 'company'
    ) {}

    public function sheets(): array
    {
        $sheets = [];

        if ($this->groupBy === 'user') {
            $userIds = (clone $this->query)->distinct()->pluck('user_id')->toArray();
            $users = User::whereIn('id', $userIds)->get();

            foreach ($users as $user) {
                $sheets[] = new VisitsExport(
                    (clone $this->query)->where('user_id', $user->id),
                    $user->name
                );
            }
        } elseif ($this->groupBy === 'company') {
            $companyIds = (clone $this->query)->distinct()->pluck('company_id')->toArray();
            $companies = Company::whereIn('id', $companyIds)->get();

            foreach ($companies as $company) {
                $sheets[] = new VisitsExport(
                    (clone $this->query)->where('company_id', $company->id),
                    $company->facility_name
                );
            }
        }

        // Fallback if no sheets generated
        if (empty($sheets)) {
            $sheets[] = new VisitsExport(clone $this->query, 'All Data');
        }

        return $sheets;
    }
}
