<?php

namespace App\Filament\Resources\Principals\Pages;

use App\Exports\PrincipalsTemplateExport;
use App\Filament\Actions\ImportPrincipalsAction;
use App\Filament\Resources\Principals\PrincipalResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Maatwebsite\Excel\Facades\Excel;

class ListPrincipals extends ListRecords
{
    protected static string $resource = PrincipalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportPrincipalsAction::make(),
            Action::make('download_template')
                ->label('Download Template')
                ->icon(Heroicon::OutlinedDocumentArrowDown)
                ->visible(fn () => auth()->user()->hasBaseRole('Super Admin'))
                ->action(fn () => Excel::download(new PrincipalsTemplateExport, 'principals_template.xlsx')),
            CreateAction::make(),
        ];
    }
}
