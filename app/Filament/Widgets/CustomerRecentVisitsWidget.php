<?php

namespace App\Filament\Widgets;

use App\Models\Visit;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CustomerRecentVisitsWidget extends TableWidget
{
    public ?Model $record = null;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => 
                Visit::query()
                    ->where('customer_id', $this->record?->getKey())
                    ->latest('visit_started_at')
            )
            ->columns([
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
                Tables\Columns\IconColumn::make('is_worth_keeping')
                    ->label('Worth Keeping')
                    ->boolean(),
            ])
            ->paginated([5, 10])
            ->defaultPaginationPageOption(5);
    }
}
