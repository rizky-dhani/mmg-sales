<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Customer;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Project created successfully');
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['assigned_users'])) {
            $assignedUsers = $data['assigned_users'];
            unset($data['assigned_users']);
            session(['pending_collaborators' => $assignedUsers]);
        }

        if (isset($data['customer_id'])) {
            $customer = Customer::find($data['customer_id']);
            if ($customer) {
                $data['customer_name'] = $customer->name;
            }
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $assignedUsers = session('pending_collaborators', []);
        if (! empty($assignedUsers)) {
            $this->getRecord()->collaborators()->sync($assignedUsers);
            session()->forget('pending_collaborators');
        }
    }
}
