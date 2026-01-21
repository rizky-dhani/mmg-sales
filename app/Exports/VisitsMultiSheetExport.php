<?php

namespace App\Exports;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class VisitsMultiSheetExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(
        protected Builder $query,
        protected string $groupBy = 'user' // 'user' or 'customer'
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
        } elseif ($this->groupBy === 'customer') {
            $customerIds = (clone $this->query)->distinct()->pluck('customer_id')->toArray();
            $companies = Customer::whereIn('id', $customerIds)->get();

            foreach ($companies as $customer) {
                $sheets[] = new VisitsExport(
                    (clone $this->query)->where('customer_id', $customer->id),
                    $customer->facility_name
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
