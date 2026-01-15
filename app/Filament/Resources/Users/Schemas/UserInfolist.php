<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile Information')
                    ->columns(6)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('name')
                            ->weight('bold'),
                        TextEntry::make('email')
                            ->copyable(),
                        TextEntry::make('department.name'),
                        TextEntry::make('position.name'),
                        TextEntry::make('territory.name')
                            ->label('Primary Territory'),
                        TextEntry::make('manager.name')
                            ->label('Reports To'),
                    ]),
            ]);
    }
}
