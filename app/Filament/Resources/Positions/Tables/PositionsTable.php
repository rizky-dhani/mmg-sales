<?php

namespace App\Filament\Resources\Positions\Tables;

use App\Models\Department;
use App\Models\Position;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PositionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(fn (Builder $query): Builder => $query
                ->orderBy(
                    Department::select('name')
                        ->whereColumn('departments.id', 'positions.department_id')
                )
                ->orderBy('level')
                ->orderBy(
                    Position::select('level')
                        ->whereColumn('positions.id', 'positions.parent_id')
                )
            )
            ->columns([
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Position Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('level')
                    ->label('Level')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('department.name')
                    ->label('Department')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('parent.name')
                    ->label('Reports To')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->date('d M Y')
                    ->formatStateUsing(fn ($state) => strtoupper(Carbon::parse($state)->translatedFormat('d M Y')))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
