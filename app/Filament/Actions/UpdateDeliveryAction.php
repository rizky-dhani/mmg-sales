<?php

namespace App\Filament\Actions;

use App\Models\DeliveryStatus;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;

class UpdateDeliveryAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'updateDelivery';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Update Delivery')
            ->icon(Heroicon::Truck)
            ->color('info')
            ->visible(fn () => auth()->user()->can('update_delivery_order'))
            ->form([
                TextInput::make('carrier')
                    ->label('Carrier')
                    ->placeholder('e.g. JNE, J&T, SiCepat'),
                TextInput::make('tracking_number')
                    ->label('Tracking Number')
                    ->placeholder('Enter tracking number'),
                DatePicker::make('shipped_date')
                    ->label('Shipped Date')
                    ->default(now()->toDateString()),
                DatePicker::make('delivered_date')
                    ->label('Delivered Date'),
                TextInput::make('proof_of_delivery')
                    ->label('Proof of Delivery URL')
                    ->placeholder('https://...'),
                Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3),
            ])
            ->action(function (array $data, Order $record): void {
                $record->deliveryStatuses()->create($data);
            })
            ->successNotificationTitle('Delivery status updated');
    }
}
