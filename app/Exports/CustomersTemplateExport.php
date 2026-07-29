<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class CustomersTemplateExport implements FromCollection, WithHeadings, WithTitle
{
    public function collection(): Collection
    {
        return collect([
            [
                'internal_code' => '',
                'name' => 'PT Example Customer',
                'customer_name' => 'PT Example Customer',
                'type' => 'hospital_clinic',
                'other_type' => null,
                'tax_number' => '01.234.567.8-901.000',
                'address' => 'Jl. Example No. 123',
                'city' => 'Jakarta',
                'state' => 'DKI Jakarta',
                'postal_code' => '12345',
                'country' => 'Indonesia',
                'email' => 'info@example.com',
                'phone' => '021-1234567',
                'phone_purchasing' => null,
                'phone_finance' => null,
                'website' => 'https://example.com',
                'is_active' => 1,
                'cd_ncd_type' => 'CD',
                'customer_group_id' => 1,
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'internal_code',
            'name',
            'customer_name',
            'type',
            'other_type',
            'tax_number',
            'address',
            'city',
            'state',
            'postal_code',
            'country',
            'email',
            'phone',
            'phone_purchasing',
            'phone_finance',
            'website',
            'is_active',
            'cd_ncd_type',
            'customer_group_id',
        ];
    }

    public function title(): string
    {
        return 'Customers';
    }
}
