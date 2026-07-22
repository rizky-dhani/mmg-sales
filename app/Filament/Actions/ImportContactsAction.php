<?php

namespace App\Filament\Actions;

use App\Imports\ContactsImport;
use App\Imports\ContactsSheetImport;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Maatwebsite\Excel\Exceptions\SheetNotFoundException;
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
            ->color('warning')
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

                $import = new ContactsImport;

                try {
                    Excel::import($import, $file);
                } catch (SheetNotFoundException) {
                    $fallback = new ContactsSheetImport;
                    Excel::import($fallback, $file);
                    $count = $fallback->importedCount;
                }

                $count ??= $import->sheet->importedCount;

                $this->successNotificationTitle(
                    $count > 0
                        ? "{$count} contacts imported successfully"
                        : 'No contacts were imported — check column names match the template'
                );
            })
            ->failureNotificationTitle('Import failed');
    }
}
