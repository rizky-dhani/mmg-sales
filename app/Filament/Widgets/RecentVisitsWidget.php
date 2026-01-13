<?php

namespace App\Filament\Widgets;

use App\Models\Visit;
use App\Services\VisitScopeService;
use Filament\Tables;
use Filament\Tables\Table;
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
                Tables\Columns\TextColumn::make('company.facility_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Visitor')
                    ->sortable(),
                Tables\Columns\TextColumn::make('visit_started_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('purpose')
                    ->label('Purpose')
                    ->limit(30),
            ])
            ->paginated([5, 10])
            ->defaultPaginationPageOption(5);
    }
}