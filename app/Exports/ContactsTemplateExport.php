<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ContactsTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Contacts List' => new ContactsTemplateSheet,
            'Customer List' => new CustomerListSheet,
        ];
    }
}
