<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Models\Department;
use App\Models\Position;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['name'] = self::generateRoleName($data['position_id'] ?? null, $data['department_id'] ?? null);

        return $data;
    }

    protected static function generateRoleName(?int $positionId, ?int $departmentId): string
    {
        $position = $positionId ? Position::find($positionId) : null;
        $department = $departmentId ? Department::find($departmentId) : null;

        $name = $position?->name ?? 'Unnamed';
        if ($department) {
            $name .= ' - '.$department->name;
        }

        return $name;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Role updated successfully');
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
