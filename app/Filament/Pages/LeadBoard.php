<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Leads\Schemas\LeadForm;
use App\Models\Lead;
use Filament\Actions\CreateAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Builder;
use Relaticle\Flowforge\Board;
use Relaticle\Flowforge\BoardPage;
use Relaticle\Flowforge\Column;

class LeadBoard extends BoardPage
{
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-view-columns';

    protected static ?string $navigationLabel = 'Lead Board';

    protected static ?string $title = 'Lead Board';

    protected static string|null|\UnitEnum $navigationGroup = 'CRM';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    public function board(Board $board): Board
    {
        return $board
            ->query($this->getEloquentQuery())
            ->recordTitleAttribute('title')
            ->columnIdentifier('status')
            ->positionIdentifier('position') // Enable drag-and-drop with position field
            ->cardSchema(fn (Schema $schema) => $schema
                ->components([
                    TextEntry::make('title')
                        ->label('')
                        ->weight('bold')
                        ->size(TextSize::Medium)
                        ->placeholder('Untitled Lead')
                        ->url(fn (Lead $record) => route('filament.admin.resources.leads.edit', $record)),

                    TextEntry::make('customer_name')
                        ->label('')
                        ->size(TextSize::Small)
                        ->icon('heroicon-m-building-office')
                        ->color('gray'),

                    TextEntry::make('status')
                        ->label('')
                        ->badge()
                        ->formatStateUsing(fn (string $state): string => ucfirst($state))
                        ->color(fn (string $state): string => match ($state) {
                            'new' => 'gray',
                            'contacted' => 'info',
                            'qualified' => 'warning',
                            'proposal' => 'info',
                            'negotiation' => 'primary',
                            'won' => 'success',
                            'lost' => 'danger',
                            default => 'gray',
                        }),

                    Group::make([
                        TextEntry::make('assignedUser.name')
                            ->label('')
                            ->badge()
                            ->icon('heroicon-m-user')
                            ->color('gray')
                            ->placeholder('Unassigned'),

                        TextEntry::make('estimated_value')
                            ->label('')
                            ->badge()
                            ->money('IDR')
                            ->icon('heroicon-m-banknotes')
                            ->color('success'),

                        TextEntry::make('aging')
                            ->label('')
                            ->badge()
                            ->icon('heroicon-m-clock')
                            ->color(fn (Lead $record): string => match (true) {
                                $record->created_at->diffInDays($record->converted_at ?? now()) > 45 => 'danger',
                                $record->created_at->diffInDays($record->converted_at ?? now()) > 14 => 'warning',
                                default => 'success',
                            }),
                    ])
                        ->columns(1)
                        ->extraAttributes(['class' => 'flex flex-wrap gap-2']),

                    TextEntry::make('latestActivity.subject')
                        ->label('Last Activity')
                        ->placeholder('No activity yet')
                        ->color('gray')
                        ->size(TextSize::ExtraSmall),
                ])
            )
            ->columns([
                Column::make('new')->label('New')->color('gray'),
                Column::make('contacted')->label('Contacted')->color('blue'),
                Column::make('qualified')->label('Qualified')->color('warning'),
                Column::make('proposal')->label('Proposal')->color('info'),
                Column::make('negotiation')->label('Negotiation')->color('primary'),
                Column::make('won')->label('Won')->color('success'),
                Column::make('lost')->label('Lost')->color('danger'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->model(Lead::class)
                ->form(fn (Schema $schema) => LeadForm::configure($schema)->getComponents())
                ->successRedirectUrl(fn (Lead $record): string => route('filament.admin.resources.leads.edit', $record)),
        ];
    }

    public function getEloquentQuery(): Builder
    {
        return Lead::query()->with(['latestActivity', 'assignedUser']);
    }
}
