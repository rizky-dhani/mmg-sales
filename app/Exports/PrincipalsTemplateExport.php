<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PrincipalsTemplateExport implements FromCollection, WithHeadings
{
    public function collection(): Collection
    {
        return collect([
            [
                'name' => 'PT Example Principal',
                'initial' => 'PRN',
                'description' => 'Principal description',
                'is_active' => 1,
                'principal_acc_code' => '',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'name',
            'initial',
            'description',
            'is_active',
            'principal_acc_code',
        ];
    }
}
