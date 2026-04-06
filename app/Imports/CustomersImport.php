<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\CustomerGroup;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomersImport implements ToCollection, WithHeadingRow
{
    /**
     * Allowed values for the customer type ENUM column.
     */
    protected const ALLOWED_TYPES = [
        'hospital',
        'clinic',
        'pharmacy',
        'laboratory',
        'distributor',
        'other',
    ];

    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            if (empty($row['name'])) {
                continue;
            }

            $customerGroupId = $this->resolveCustomerGroupId($row['customer_group'] ?? $row['customer_group_id'] ?? null);
            $typeData = $this->normalizeType($row['type'] ?? null);

            Customer::updateOrCreate(
                ['name' => $row['name']],
                [
                    'name' => $row['name'],
                    'customer_name' => $row['customer_name'] ?? $row['name'],
                    'type' => $typeData['type'],
                    'other_type' => $typeData['other_type'] ?? $row['other_type'] ?? null,
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
                ]
            );
        }
    }

    /**
     * Normalize the type value to match the ENUM column.
     * If the type doesn't match allowed values, sets type to 'other'
     * and stores the original value in other_type.
     */
    protected function normalizeType(?string $type): array
    {
        if (empty($type)) {
            return ['type' => 'other', 'other_type' => null];
        }

        // Trim whitespace and convert to lowercase for comparison
        $normalizedType = strtolower(trim($type));

        // Check if it matches an allowed type
        if (in_array($normalizedType, self::ALLOWED_TYPES, true)) {
            return ['type' => $normalizedType, 'other_type' => null];
        }

        // Store custom type in other_type and default to 'other'
        return ['type' => 'other', 'other_type' => $type];
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
