<?php

namespace App\Filament\Widgets;

use App\Models\Activity;
use App\Models\Contact;
use App\Models\Customer;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class ActivityAttendanceWidget extends Widget implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected string $view = 'filament.widgets.activity-attendance-widget';

    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    public ?Activity $activeVisit = null;

    public function mount(): void
    {
        $this->activeVisit = $this->getActiveVisit();
    }

    public function getActiveVisit(): ?Activity
    {
        return Activity::where('user_id', Auth::id())
            ->whereNull('visit_ended_at')
            ->whereNotNull('visit_started_at')
            ->latest('visit_started_at')
            ->first();
    }

    public function startVisitAction(): Action
    {
        return Action::make('startVisit')
            ->label('Start Visit (Check-in)')
            ->color('success')
            ->icon('heroicon-m-play-circle')
            ->form([
                Select::make('customer_id')
                    ->label('Customer')
                    ->options(Customer::pluck('facility_name', 'id'))
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('contact_id', null)),
                Select::make('contact_id')
                    ->label('Contact Person')
                    ->options(fn (callable $get) => Contact::where('customer_id', $get('customer_id'))
                        ->get()
                        ->mapWithKeys(fn ($contact) => [$contact->id => "{$contact->first_name} {$contact->last_name}"])
                    )
                    ->searchable(),
                Select::make('type')
                    ->options([
                        'In-person Meeting' => 'In-person',
                        'Online Meeting' => 'Video Call',
                        'Phone Call' => 'Phone Call',
                        'Messaging' => 'Messaging',
                    ])
                    ->required()
                    ->live(),
                TextInput::make('location')
                    ->placeholder('e.g. Hospital Lobby, Cafe, Office')
                    ->visible(fn ($get) => $get('type') === 'In-person Meeting'),
                TextInput::make('meeting_link')
                    ->label('Meeting Link')
                    ->url()
                    ->placeholder('https://zoom.us/j/...')
                    ->visible(fn ($get) => $get('type') === 'Online Meeting')
                    ->required(),
                TextInput::make('messaging_platform')
                    ->label('Messaging Platform')
                    ->placeholder('e.g. WhatsApp, Telegram, Slack')
                    ->visible(fn ($get) => $get('type') === 'Messaging')
                    ->required(),
                TextInput::make('purpose')
                    ->label('Strategic Purpose')
                    ->required(),
                Textarea::make('expectations')
                    ->label('What do you expect from this visit?')
                    ->required(),
                Textarea::make('targets')
                    ->label('What are your targets for this visit?')
                    ->required(),
            ])
            ->action(function (array $data): void {
                $activity = Activity::create([
                    'user_id' => Auth::id(),
                    'customer_id' => $data['customer_id'],
                    'contact_id' => $data['contact_id'],
                    'type' => $data['type'],
                    'subject' => $data['purpose'] ?? 'Sales Visit',
                    'location' => $data['location'] ?? null,
                    'meeting_link' => $data['meeting_link'] ?? null,
                    'messaging_platform' => $data['messaging_platform'] ?? null,
                    'purpose' => $data['purpose'],
                    'expectations' => $data['expectations'],
                    'targets' => $data['targets'],
                    'visit_started_at' => now(),
                    'performed_at' => now(),
                ]);

                $this->activeVisit = $activity;
            });
    }

    public function endVisitAction(): Action
    {
        return Action::make('endVisit')
            ->label('End Visit (Check-out)')
            ->color('danger')
            ->icon('heroicon-m-stop-circle')
            ->schema([
                Textarea::make('description')
                    ->label('Visit Summary / Outcome')
                    ->required(),
                Slider::make('confidence_level')
                    ->label('Confidence Level')
                    ->minValue(0)
                    ->maxValue(100)
                    ->step(5)
                    ->default(50)
                    ->tooltips(),
            ])
            ->action(function (array $data): void {
                if ($this->activeVisit) {
                    $this->activeVisit->update([
                        'description' => $data['description'],
                        'confidence_level' => $data['confidence_level'],
                        'visit_ended_at' => now(),
                    ]);

                    $this->activeVisit = null;
                }
            });
    }
}