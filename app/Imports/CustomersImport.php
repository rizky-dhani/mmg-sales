<?php

namespace App\Imports;

use App\Models\Customer;
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
                'customer_group_id' => $row['customer_group_id'] ?? null,
                'customer_code' => $row['customer_code'] ?? null,
                'customer_acc_code' => $row['customer_acc_code'] ?? null,
            ]);
        }
    }
}
