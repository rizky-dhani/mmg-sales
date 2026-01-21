<?php

namespace App\Filament\Widgets;

use App\Models\Visit;
use App\Services\VisitScopeService;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SalesRepLeaderboardWidget extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Top Rep Customer Engagement';

    public function getTableRecordKey(Model|array $record): string
    {
        return $record->user_id . '-' . $record->customer_id;
    }

    public function table(Table $table): Table
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $service = app(VisitScopeService::class);

        return $table
            ->query(fn (): Builder => $service->getRepCustomerLeaderboardQuery($user))
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Sales Rep')
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.facility_name')
                    ->label('Customer')
                    ->sortable(),
                Tables\Columns\TextColumn::make('visit_count')
                    ->label('Total Visits')
                    ->badge()
                    ->color('success')
                    ->sortable(),
            ])
            ->paginated([5, 10])
            ->defaultPaginationPageOption(5);
    }
}