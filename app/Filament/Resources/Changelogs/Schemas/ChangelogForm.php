<?php

namespace App\Filament\Resources\Changelogs\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ChangelogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Changelog Details')
                    ->schema([
                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255),
                        Select::make('type')
                            ->label('Type')
                            ->options([
                                'bug_fix' => 'Bug Fix',
                                'new_feature' => 'New Feature',
                                'improvement' => 'Improvement',
                                'breaking_change' => 'Breaking Change',
                                'other' => 'Other',
                            ])
                            ->required(),
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3),
                        Textarea::make('changes')
                            ->label('Changes')
                            ->helperText('List the changes made in detail')
                            ->rows(5),
                        Checkbox::make('is_published')
                            ->label('Publish this changelog'),
                    ]),
            ]);
    }
}
