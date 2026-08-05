<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Models\Department;
use App\Models\Position;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
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
            $name = $department->name.' '.$name;
        }

        return $name;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Role created successfully');
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
