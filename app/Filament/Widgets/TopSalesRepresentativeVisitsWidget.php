<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Services\ActivityScopeService;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TopSalesRepresentativeVisitsWidget extends TableWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Top Sales Representatives by Customer Visits';

    public function getTableRecordKey(Model|array $record): string
    {
        return $record->user_id.'-'.$record->customer_id;
    }

    public function table(Table $table): Table
    {
        /** @var User $user */
        $user = Auth::user();
        $service = app(ActivityScopeService::class);

        return $table
            ->query(fn (): Builder => $this->getVisitQuery($service, $user))
            ->columns([
                Tables\Columns\TextColumn::make('rank')
                    ->label('Rank')
                    ->state(fn ($record, $rowLoop): int => $rowLoop->iteration)
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Sales Rep')
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->sortable(),
                Tables\Columns\TextColumn::make('visit_count')
                    ->label('Visits')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_visit')
                    ->label('Last Visit')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->paginated([5, 10])
            ->defaultPaginationPageOption(5);
    }

    protected function getVisitQuery(ActivityScopeService $service, User $user): Builder
    {
        return $service->getActivityQuery($user)
            ->where(function ($query): void {
                $query->whereNotNull('visit_started_at')
                    ->orWhereNotNull('customer_id');
            })
            ->whereNotNull('customer_id')
            ->selectRaw('user_id, customer_id, COUNT(*) as visit_count, MAX(performed_at) as last_visit')
            ->groupBy('user_id', 'customer_id')
            ->with(['user:id,name', 'customer:id,name'])
            ->orderByDesc('visit_count');
    }
}
