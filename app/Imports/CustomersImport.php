<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\CustomerGroup;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomersImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            if (empty($row['name'])) {
                continue;
            }

            $customerGroupId = $this->resolveCustomerGroupId($row['customer_group'] ?? $row['customer_group_id'] ?? null);

            Customer::create([
                'name' => $row['name'],
                'customer_name' => $row['customer_name'] ?? $row['name'],
                'type' => $row['type'] ?? 'hospital',
                'other_type' => $row['other_type'] ?? null,
                'tax_number' => $row['tax_number'] ?? null,
                'address' => $row['address'] ?? null,
                'city' => $row['city'] ?? null,
                'state' => $row['state'] ?? null,
                'postal_code' => $row['postal_code'] ?? null,
                'country' => $row['country'] ?? 'Indonesia',
                'email' => $row['email'] ?? null,
                'phone' => $row['phone'] ?? null,
                'website' => $row['website'] ?? null,
                'is_active' => $row['is_active'] ?? 1,
                'cd_ncd_type' => $row['cd_ncd_type'] ?? null,
                'customer_group_id' => $customerGroupId,
                'customer_acc_code' => $row['customer_acc_code'] ?? null,
            ]);
        }
    }

    /**
     * Resolve customer group ID from value (ID, name, or code).
     * Returns null if not found.
     */
    private function resolveCustomerGroupId(?string $value): ?int
    {
        if (empty($value)) {
            return null;
        }

        // If numeric, treat as ID
        if (is_numeric($value)) {
            return CustomerGroup::where('id', $value)->value('id');
        }

        // Try to find by name or code (case-insensitive)
        $group = CustomerGroup::query()
            ->whereRaw('LOWER(name) = ?', [strtolower($value)])
            ->orWhereRaw('LOWER(code) = ?', [strtolower($value)])
            ->first();

        return $group?->id;
    }
}
