<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ContactsTemplateSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection(): Collection
    {
        return collect([
            [
                'contact_code' => '',
                'customer_id' => '1',
                'name' => 'John Doe',
                'status' => 'active',
                'position' => 'Purchasing Manager',
                'department' => 'Procurement',
                'email' => 'john.doe@example.com',
                'phone' => '021-1234567',
                'mobile' => '0812-3456-7890',
                'is_primary' => '1',
                'is_billing_contact' => '0',
                'notes' => '',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'contact_code',
            'customer_id',
            'name',
            'status',
            'position',
            'department',
            'email',
            'phone',
            'mobile',
            'is_primary',
            'is_billing_contact',
            'notes',
        ];
    }

    public function title(): string
    {
        return 'Contacts List';
    }
}
