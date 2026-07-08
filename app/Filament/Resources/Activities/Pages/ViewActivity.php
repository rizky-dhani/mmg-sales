<?php

namespace App\Filament\Resources\Activities\Pages;

use App\Filament\Resources\Activities\ActivityResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewActivity extends ViewRecord
{
    protected static string $resource = ActivityResource::class;

    public ?string $newComment = null;

    protected string $view = 'filament.resources.activities.pages.view-activity';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('addComment')
                ->label('Add Comment')
                ->icon(Heroicon::ChatBubbleLeft)
                ->color('info')
                ->form([
                    Textarea::make('comment')
                        ->label('Comment')
                        ->required()
                        ->rows(3)
                        ->maxLength(5000),
                ])
                ->action(function (array $data): void {
                    $this->getRecord()->comments()->create([
                        'user_id' => auth()->id(),
                        'comment' => $data['comment'],
                    ]);
                })
                ->visible(fn () => auth()->user()?->can('create_activity_comment')),
        ];
    }

    public function submitComment(): void
    {
        if (! filled($this->newComment)) {
            return;
        }

        if (! auth()->user()?->can('create_activity_comment')) {
            return;
        }

        $this->getRecord()->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $this->newComment,
        ]);

        $this->newComment = null;

        $this->dispatch('refreshPage');
    }
}
