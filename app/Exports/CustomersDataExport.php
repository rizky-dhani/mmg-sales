<?php

namespace App\Exports;

use App\Models\Customer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class CustomersDataExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    public function collection(): Collection
    {
        return Customer::query()
            ->select([
                'customer_acc_code',
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
            ])
            ->orderBy('name')
            ->get();
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

    public function map($row): array
    {
        return [
            $row->customer_acc_code,
            $row->name,
            $row->customer_name,
            $row->type,
            $row->other_type,
            $row->tax_number,
            $row->address,
            $row->city,
            $row->state,
            $row->postal_code,
            $row->country,
            $row->email,
            $row->phone,
            $row->phone_purchasing,
            $row->phone_finance,
            $row->website,
            $row->is_active,
            $row->cd_ncd_type,
            $row->customer_group_id,
        ];
    }

    public function title(): string
    {
        return 'Customers';
    }
}
