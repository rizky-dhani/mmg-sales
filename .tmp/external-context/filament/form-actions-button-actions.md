---
source: Filament Official Docs (v5.x)
library: Filament
package: filament/filament
topic: Form actions and button actions on custom pages
fetched: 2026-06-05T00:00:00Z
official_docs: https://filamentphp.com/docs/5.x/navigation/custom-pages
---

# Actions on Custom Pages

Actions are buttons that perform tasks or visit URLs. Since all Filament pages are Livewire components, pages already have `InteractsWithActions` trait, `HasActions` interface, and the `<x-filament-actions::modals />` Blade component set up.

## Header Actions

Add actions to the page header (top-right area):

```php
use Filament\Actions\Action;

protected function getHeaderActions(): array
{
    return [
        Action::make('edit')
            ->url(route('posts.edit', ['post' => $this->post])),
        Action::make('delete')
            ->requiresConfirmation()
            ->action(fn () => $this->post->delete()),
    ];
}
```

### Aligning Header Actions on Mobile

```php
use Filament\Support\Enums\Alignment;

protected ?Alignment $headerActionsAlignment = Alignment::End;
```

## Form Actions (Below Form)

For pages with forms (e.g., resource Create/Edit pages), override `getFormActions()`:

```php
use Filament\Actions\Action;

protected function getFormActions(): array
{
    return [
        ...parent::getFormActions(),
        Action::make('close')->action('createAndClose'),
    ];
}

public function createAndClose(): void
{
    // ...
}
```

## Opening an Action Modal on Page Load

Set the `$defaultAction` property:

```php
use Filament\Actions\Action;

public $defaultAction = 'onboarding';

public function onboardingAction(): Action
{
    return Action::make('onboarding')
        ->modalHeading('Welcome')
        ->visible(fn (): bool => ! auth()->user()->isOnBoarded());
}
```

With arguments:

```php
public $defaultActionArguments = ['step' => 2];
```

Or via query string: `/admin/products/edit/932510?action=onboarding`

## Action with Modal Form

```php
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;

Action::make('updateEmail')
    ->schema([
        TextInput::make('email')
            ->email()
            ->required(),
    ])
    ->action(function (array $data, User $record) {
        $record->update($data);
    })
```

## Action with Confirmation Modal

```php
Action::make('delete')
    ->color('danger')
    ->icon('heroicon-o-trash')
    ->modalIcon('heroicon-o-trash')
    ->modalHeading('Delete Record')
    ->modalDescription('Are you sure? This cannot be undone.')
    ->requiresConfirmation()
    ->action(fn () => $this->record->delete()),
```

## Action with Custom Modal Content

```php
Action::make('advance')
    ->action(fn (Post $record) => $record->advance())
    ->modalContent(view('filament.pages.actions.advance'))
```

With data passed to the view:

```php
->modalContent(fn (Contract $record): View => view(
    'filament.pages.actions.advance',
    ['record' => $record],
))
```

## Action Without Modal (Direct Execution)

```php
Action::make('approve')
    ->label('Approve')
    ->color('success')
    ->icon('heroicon-o-check')
    ->action(function () {
        // Direct execution — no modal
        $this->record->approve();
        
        Notification::make()
            ->title('Approved successfully')
            ->success()
            ->send();
    }),
```

## Bulk Actions (Table Actions on Pages)

When your custom page contains a table, you can define bulk actions:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->bulkActions([
            BulkAction::make('delete')
                ->requiresConfirmation()
                ->action(fn (Collection $records) => $records->each->delete()),
        ]);
}
```

## Refreshing Form Data After Action

On Edit/View resource pages:

```php
Action::make('approve')
    ->action(function (Post $record) {
        $record->approve();
        $this->refreshFormData(['status']);
    })
```

## Notification After Action

```php
use Filament\Notifications\Notification;

Action::make('process')
    ->action(function () {
        // do work...
        
        Notification::make()
            ->title('Processed successfully')
            ->success()
            ->send();
    }),
```

## Action with Conditional Visibility

```php
Action::make('delete')
    ->visible(fn (): bool => auth()->user()->can('delete', $this->record)),
```

## Executing Code When Modal Opens

```php
Action::make('create')
    ->mountUsing(function (Schema $form) {
        $form->fill();
        // ...
    })
```

**Pitfall:** The `mountUsing()` method, by default, initializes the form. If you override it, you must call `$form->fill()` to ensure the form is initialized correctly.

## Common Pitfalls

- Actions are Livewire-based — they only work after the page has fully loaded. Don't rely on them for server-side redirects before page render.
- When an action callback returns a `Response` object (file download, redirect), don't also call `->successNotificationTitle()` — the response replaces the normal Livewire update.
- For actions with `->schema()`, the form uses `Filament\Schemas\Schema`, not `Filament\Forms\Form`. Use `Get $get` and `Set $set` from `Filament\Schemas\Components\Utilities\`.
- The `mountUsing()` override removes default form initialization — always call `$form->fill()` if you override it.
- Action names must be unique within a page. Two actions with `make('delete')` will conflict.
