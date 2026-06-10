---
source: Filament Official Docs (v5.x)
library: Filament
package: filament/filament
topic: Creating custom pages
fetched: 2026-06-05T00:00:00Z
official_docs: https://filamentphp.com/docs/5.x/navigation/custom-pages
---

# Custom Pages in Filament v5

## Creating a Page

To create a new page, use the Artisan command:

```bash
php artisan make:filament-page Settings
```

This command creates two files:
- A page class in the `/Pages` directory of the Filament directory: `app/Filament/Pages/Settings.php`
- A view in the `/pages` directory of the Filament views: `resources/views/filament/pages/settings.blade.php`

Page classes are full-page [Livewire](https://livewire.laravel.com) components with extra panel utilities.

## Page Registration

Pages can be auto-discovered:

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages');
}
```

Or manually registered:

```php
use App\Filament\Pages\Settings;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        ->pages([
            Settings::class,
        ]);
}
```

## Page Content Rendering

The page's Blade view is where you render content. The view file is at `resources/views/filament/pages/settings.blade.php`. Since pages are Livewire components, you can access public properties and call methods from the view using `$this`.

### Replacing the Page Header with a Custom View

Override the `getHeader()` method:

```php
use Illuminate\Contracts\View\View;

public function getHeader(): ?View
{
    return view('filament.settings.custom-header');
}
```

### Adding a Footer to the Page

Override the `getFooter()` method:

```php
use Illuminate\Contracts\View\View;

public function getFooter(): ?View
{
    return view('filament.settings.custom-footer');
}
```

## Customizing Page Metadata

### Page Title
```php
// Property
protected static ?string $title = 'Custom Page Title';

// Or method
public function getTitle(): string | Htmlable
{
    return __('Custom Page Title');
}
```

### Page Heading
```php
protected ?string $heading = 'Custom Page Heading';

public function getHeading(): string
{
    return __('Custom Page Heading');
}
```

### Page Subheading
```php
protected ?string $subheading = 'Custom Page Subheading';

public function getSubheading(): ?string
{
    return __('Custom Page Subheading');
}
```

### Page Slug (URL)
```php
protected static ?string $slug = 'custom-url-slug';
```

### Navigation Label
```php
protected static ?string $navigationLabel = 'Custom Navigation Label';

public static function getNavigationLabel(): string
{
    return __('Custom Navigation Label');
}
```

## Customizing Maximum Content Width

```php
use Filament\Support\Enums\Width;

public function getMaxContentWidth(): Width
{
    return Width::Full; // default is SevenExtraLarge
}
```

Available options: `ExtraSmall`, `Small`, `Medium`, `Large`, `ExtraLarge`, `TwoExtraLarge`, `ThreeExtraLarge`, `FourExtraLarge`, `FiveExtraLarge`, `SixExtraLarge`, `SevenExtraLarge`, `Full`, `MinContent`, `MaxContent`, `FitContent`, `Prose`, `ScreenSmall`, `ScreenMedium`, `ScreenLarge`, `ScreenExtraLarge`, `ScreenTwoExtraLarge`.

## Generating URLs to Pages

```php
use App\Filament\Pages\Settings;

Settings::getUrl();                              // /admin/settings
Settings::getUrl(['section' => 'notifications']); // /admin/settings?section=notifications
Settings::getUrl(panel: 'marketing');             // URL in another panel
```

## Adding Extra Body Attributes

```php
protected array $extraBodyAttributes = [];

public function getExtraBodyAttributes(): array
{
    return [
        'class' => 'settings-page',
    ];
}
```

## Common Pitfalls

- Page classes ARE Livewire components — you can use all Livewire features (wire:model, wire:click, public properties, etc.).
- When generating pages, the artisan command prompts if you want to place them inside a cluster directory (if clusters are discovered).
- Always use `getUrl()` instead of manually constructing URLs with `route()` — it handles cluster prefixes and panel context automatically.
- The view file is NOT a full HTML page — it renders inside the Filament panel layout (sidebar, topbar are auto-included).
