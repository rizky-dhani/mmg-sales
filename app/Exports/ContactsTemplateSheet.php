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
                'Customer' => '1',
                'Name' => 'John Doe',
                'Status' => 'active',
                'Position' => 'Purchasing Manager',
                'Department' => 'Procurement',
                'Email' => 'john.doe@example.com',
                'Phone' => '021-1234567',
                'Mobile' => '0812-3456-7890',
                'Is Primary' => '1',
                'Is Billing Contact' => '0',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'Customer',
            'Name',
            'Status',
            'Position',
            'Department',
            'Email',
            'Phone',
            'Mobile',
            'Is Primary',
            'Is Billing Contact',
        ];
    }

    public function title(): string
    {
        return 'Contacts List';
    }
}
