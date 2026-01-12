<?php

namespace App\Filament\Resources\CompanyGroups\Pages;

use App\Filament\Resources\CompanyGroups\CompanyGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCompanyGroup extends EditRecord
{
    protected static string $resource = CompanyGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
