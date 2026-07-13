<?php

namespace App\Filament\Actions;

use App\Imports\ContactsImport;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Maatwebsite\Excel\Facades\Excel;

class ImportContactsAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'importContacts';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Import Contacts')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('success')
            ->visible(fn () => auth()->user()->can('import_contacts'))
            ->form([
                FileUpload::make('file')
                    ->label('Excel File')
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-excel',
                        'application/zip',
                    ])
                    ->disk('public')
                    ->directory('imports')
                    ->required(),
            ])
            ->action(function (array $data): void {
                $file = storage_path('app/public/'.$data['file']);

                Excel::import(new ContactsImport, $file);
            })
            ->successNotificationTitle('Contacts imported successfully')
            ->failureNotificationTitle('Import failed');
    }
}
