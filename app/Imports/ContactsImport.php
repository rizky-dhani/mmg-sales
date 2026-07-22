<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ContactsImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Contacts List' => new ContactsSheetImport,
        ];
    }
}
