<?php

namespace App\Filament\Actions;

use App\Models\Order;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;

class UpdatePaymentAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'updatePayment';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Update Payment')
            ->icon(Heroicon::CurrencyDollar)
            ->color('success')
            ->visible(fn () => auth()->user()->can('update_payment_order'))
            ->form([
                Select::make('status')
                    ->label('Payment Status')
                    ->options([
                        'pending' => 'Pending',
                        'partial' => 'Partial',
                        'full' => 'Full',
                    ])
                    ->required(),
                TextInput::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->prefix('IDR')
                    ->required(),
                DatePicker::make('effective_date')
                    ->label('Effective Date')
                    ->default(now()->toDateString()),
                Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3),
            ])
            ->action(function (array $data, Order $record): void {
                $record->paymentStatuses()->create([
                    'status' => $data['status'],
                    'amount' => $data['amount'],
                    'notes' => $data['notes'] ?? null,
                ]);
            })
            ->successNotificationTitle('Payment status updated');
    }
}
