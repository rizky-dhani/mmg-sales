<?php

namespace App\Exports;

use App\Models\Activity;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ActivitiesMultiSheetExport implements WithMultipleSheets
{
    protected $query;

    protected $groupBy;

    public function __construct($query = null, string $groupBy = 'user_id')
    {
        $this->query = $query ?? Activity::query();
        $this->groupBy = $groupBy;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Get all records to group them
        $records = (clone $this->query)->get();

        if ($this->groupBy === 'user_id') {
            $grouped = $records->groupBy('user_id');
            foreach ($grouped as $userId => $items) {
                $userName = $items->first()->user?->name ?? "User {$userId}";
                $sheets[] = new ActivitiesExport(
                    (clone $this->query)->where('user_id', $userId),
                    substr($userName, 0, 31)
                );
            }
        } elseif ($this->groupBy === 'customer_id') {
            $grouped = $records->groupBy('customer_id');
            foreach ($grouped as $customerId => $items) {
                $customerName = $items->first()->customer?->facility_name ?? "Customer {$customerId}";
                $sheets[] = new ActivitiesExport(
                    (clone $this->query)->where('customer_id', $customerId),
                    substr($customerName, 0, 31)
                );
            }
        }

        if (empty($sheets)) {
            $sheets[] = new ActivitiesExport(clone $this->query, 'All Data');
        }

        return $sheets;
    }
}
