<?php

namespace App\Filament\Resources\Contacts\Pages;

use App\Exports\ContactsTemplateExport;
use App\Filament\Actions\ImportContactsAction;
use App\Filament\Resources\Contacts\ContactResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListContacts extends ListRecords
{
    protected static string $resource = ContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportContactsAction::make(),
            Action::make('downloadTemplate')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->action(fn () => Excel::download(new ContactsTemplateExport, 'contacts_template.xlsx'))
                ->visible(fn () => auth()->user()->can('download_contacts_template')),
            CreateAction::make()->color('success'),
        ];
    }
}
