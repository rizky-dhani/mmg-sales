<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Services\VisitScopeService;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SalesRepLeaderboardWidget extends TableWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Visit Leaderboard';

    public function table(Table $table): Table
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $service = app(VisitScopeService::class);

        // Get IDs of all subordinates recursively including the user
        $subordinateIds = $service->getAllSubordinateIds($user)->push($user->id);

        return $table
            ->query(
                User::query()
                    ->whereIn('id', $subordinateIds)
                    ->withCount('visits')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Sales Rep'),
                Tables\Columns\TextColumn::make('visits_count')
                    ->label('Total Visits')
                    ->badge()
                    ->color('success')
                    ->sortable(),
            ])
            ->defaultSort('visits_count', 'desc')
            ->paginated([5, 10])
            ->defaultPaginationPageOption(5);
    }
}
