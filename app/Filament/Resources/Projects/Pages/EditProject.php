<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Customer;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->successNotificationTitle('Project updated successfully');
    }

    protected function getHeaderActions(): array
    {
        return [
            ProjectResource::getChecklistAction(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['assigned_users'])) {
            $assignedUsers = $data['assigned_users'];
            unset($data['assigned_users']);

            $record = $this->getRecord();
            if ($record) {
                $record->collaborators()->sync($assignedUsers);
            }
        }

        if (isset($data['customer_id'])) {
            $customer = Customer::find($data['customer_id']);
            if ($customer) {
                $data['customer_name'] = $customer->name;
            }
        }

        return $data;
    }
}
