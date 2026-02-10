<?php

namespace App\Filament\Widgets;

use App\Models\Activity;
use App\Services\ActivityScopeService;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RecentActivitiesWidget extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $service = app(ActivityScopeService::class);

        return $table
            ->query(fn (): Builder => $service->getActivityQuery($user)->latest('performed_at'))
            ->columns([
                Tables\Columns\TextColumn::make('customer.facility_name')
                    ->label('Customer')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Sales Rep')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('performed_at')
                    ->label('Date')
                    ->dateTime()
                    ->timezone('Asia/Jakarta')
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Subject')
                    ->limit(30),
            ])
            ->actions([
                Action::make('view')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Activity $record): string => route('filament.admin.resources.activities.view', $record)),
            ])
            ->paginated([5, 10])
            ->defaultPaginationPageOption(5);
    }
}
