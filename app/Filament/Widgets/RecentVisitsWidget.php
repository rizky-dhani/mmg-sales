<?php

namespace App\Filament\Widgets;

use App\Models\Visit;
use App\Services\VisitScopeService;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RecentVisitsWidget extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $service = app(VisitScopeService::class);

        return $table
            ->query(fn (): Builder => $service->getVisitQuery($user)->latest('visit_started_at'))
            ->columns([
                Tables\Columns\TextColumn::make('customer.facility_name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Visitor')
                    ->sortable(),
                Tables\Columns\TextColumn::make('visit_started_at')
                    ->label('Date')
                    ->dateTime()
                    ->timezone('Asia/Jakarta')
                    ->sortable(),
                Tables\Columns\TextColumn::make('purpose')
                    ->label('Purpose')
                    ->limit(30),
            ])
            ->actions([
                Action::make('view')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Visit $record): string => route('filament.admin.resources.visits.view', $record)),
            ])
            ->paginated([5, 10])
            ->defaultPaginationPageOption(5);
    }
}