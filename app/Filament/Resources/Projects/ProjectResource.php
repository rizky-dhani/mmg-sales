<?php

namespace App\Filament\Resources\Projects;

use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\Projects\Pages\ViewProject;
use App\Filament\Resources\Projects\Schemas\ProjectForm;
use App\Filament\Resources\Projects\Schemas\ProjectInfolist;
use App\Filament\Resources\Projects\Tables\ProjectsTable;
use App\Models\Milestone;
use App\Models\Project;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedLightBulb;

    protected static string|\UnitEnum|null $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'customer_name';

    public static function form(Schema $schema): Schema
    {
        return ProjectForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProjectInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectsTable::configure($table);
    }

    public static function getChecklistAction(string $action = Action::class): mixed
    {
        return $action::make('updateChecklist')
            ->label('Checklist')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->modalHeading('Strategic Checklist')
            ->modalWidth('4xl')
            ->mountUsing(function (Schema $form, Project $record) {
                // \Log::info('Mounting action for project: ' . $record->id);
                $form->fill([
                    'milestones' => $record->milestones->map(fn ($m) => [
                        'milestone_id' => $m->id,
                        'is_completed' => $m->pivot->is_completed,
                        'notes' => $m->pivot->notes,
                    ])->toArray(),
                ]);
            })
            ->form([
                Repeater::make('milestones')
                    ->schema([
                        Select::make('milestone_id')
                            ->label('Milestone')
                            ->options(Milestone::pluck('name', 'id'))
                            ->required()
                            ->distinct()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                        Toggle::make('is_completed')
                            ->label('Completed')
                            ->live(),
                        TextInput::make('notes')
                            ->label('Notes')
                            ->columnSpan(2),
                    ])
                    ->columns(4)
                    ->itemLabel(function (array $state): ?string {
                        $milestone = Milestone::find($state['milestone_id'] ?? null);
                        if (! $milestone) {
                            return 'New Milestone';
                        }
                        $status = ($state['is_completed'] ?? false) ? '✅' : '⏳';

                        return "{$status} {$milestone->name} ({$milestone->weight}%)";
                    })
                    ->reorderable(false),
            ])
            ->action(function (Project $record, array $data) {
                dd($data);
                $milestones = [];
                foreach ($data['milestones'] as $item) {
                    if (! isset($item['milestone_id'])) {
                        continue;
                    }

                    $existing = $record->milestones()->where('milestones.id', $item['milestone_id'])->first();

                    $milestones[$item['milestone_id']] = [
                        'is_completed' => $item['is_completed'],
                        'notes' => $item['notes'],
                        'completed_at' => ($item['is_completed'] && (! $existing || ! $existing->pivot->is_completed))
                            ? now()
                            : ($existing ? $existing->pivot->completed_at : null),
                    ];
                }

                $record->milestones()->sync($milestones);
                $record->updateConfidenceLevel();
            })
            ->after(function () {
                Notification::make()
                    ->title('Confidence Level Updated')
                    ->success()
                    ->send();
            });
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CollaboratorsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'view' => ViewProject::route('/{record}'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }
}
