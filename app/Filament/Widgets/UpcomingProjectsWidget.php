<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Project;
use Carbon\Carbon;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingProjectsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Projects Nearing Completion';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Project::query()
                    ->whereNotNull('estimated_completion_date')
                    ->where('estimated_completion_date', '>=', now()->startOfDay())
                    ->orderBy('estimated_completion_date', 'asc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Project Title')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer'),
                Tables\Columns\TextColumn::make('estimated_revenue')
                    ->label('Expected Revenue')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('estimated_completion_date')
                    ->label('Est. Finish')
                    ->date('d M Y')
                    ->formatStateUsing(fn ($state) => $state ? strtoupper(Carbon::parse($state)->translatedFormat('d M Y')) : '-')
                    ->color(fn ($state) => Carbon::parse($state)->isPast() ? 'danger' : (Carbon::parse($state)->diffInDays(now()) <= 7 ? 'warning' : 'success')),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'gray',
                        'contacted' => 'info',
                        'qualified' => 'primary',
                        'proposal' => 'warning',
                        'negotiation' => 'warning',
                        'won' => 'success',
                        'lost' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->actions([
                ViewAction::make()
                    ->url(fn (?Project $record): ?string => $record ? ProjectResource::getUrl('view', ['record' => $record]) : null),
            ])
            ->recordUrl(
                fn (?Project $record): ?string => $record ? ProjectResource::getUrl('view', ['record' => $record]) : null,
            );
    }
}
