---
source: Filament Official Docs (v5.x)
library: Filament
package: filament/filament
topic: Navigation — adding pages, sorting, groups, clusters
fetched: 2026-06-05T00:00:00Z
official_docs: https://filamentphp.com/docs/5.x/navigation/overview
---

# Navigation: Adding Pages, Sorting, Groups, and Clusters

## Navigation Group Configuration

Define groups at the panel level in your configuration file:

```php
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\Support\Icons\Heroicon;

public function panel(Panel $panel): Panel
{
    return $panel
        ->navigationGroups([
            NavigationGroup::make()
                ->label('Shop')
                ->icon(Heroicon::OutlinedShoppingCart),
            NavigationGroup::make()
                ->label('Blog')
                ->icon(Heroicon::OutlinedPencil),
            NavigationGroup::make()
                ->label(fn (): string => __('navigation.settings'))
                ->icon(Heroicon::OutlinedCog6Tooth)
                ->collapsed(),
        ]);
}
```

### Assigning a Page to a Navigation Group

On the page class itself:

```php
protected static ?string $navigationGroup = 'Settings';
```

### Ordering Navigation Groups (Simple)

Just pass labels in the desired order:

```php
$panel->navigationGroups([
    'Shop',
    'Blog',
    'Settings',
]);
```

### Making Groups Non-Collapsible

```php
NavigationGroup::make()
    ->label('Settings')
    ->icon(Heroicon::OutlinedCog6Tooth)
    ->collapsible(false);

// Or globally:
$panel->collapsibleNavigationGroups(false);
```

### Extra HTML Attributes on Groups

```php
NavigationGroup::make()
    ->extraSidebarAttributes(['class' => 'featured-sidebar-group'])
    ->extraTopbarAttributes(['class' => 'featured-topbar-group']);
```

## Sorting Navigation Items

By default, navigation items are sorted alphabetically. Override with:

```php
protected static ?int $navigationSort = 3;
```

Lower values appear first — order is ascending.

## Navigation Icon

```php
use Filament\Support\Icons\Heroicon;

protected static ?string $navigationIcon = Heroicon::OutlinedCog6Tooth;
```

## Registering Custom Navigation Items (Non-Page Links)

```php
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\Support\Icons\Heroicon;

public function panel(Panel $panel): Panel
{
    return $panel
        ->navigationItems([
            NavigationItem::make('Analytics')
                ->url('https://filament.pirsch.io', shouldOpenInNewTab: true)
                ->icon(Heroicon::OutlinedPresentationChartLine)
                ->group('Reports')
                ->sort(3),
            NavigationItem::make('dashboard')
                ->label(fn (): string => __('filament-panels::pages/dashboard.title'))
                ->url(fn (): string => Dashboard::getUrl())
                ->isActiveWhen(fn () => original_request()->routeIs('filament.admin.pages.dashboard')),
        ]);
}
```

## Clusters

Clusters are a hierarchical structure that group [resources](../resources) and [custom pages](custom-pages) together under a single navigation item.

### Creating a Cluster

First, register cluster discovery:

```php
public function panel(Panel $panel): Panel
{
    return $panel
        ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters');
}
```

Then create a cluster:

```bash
php artisan make:filament-cluster Settings
```

This creates `app/Filament/Clusters/Settings/SettingsCluster.php`:

```php
<?php

namespace App\Filament\Clusters\Settings;

use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class SettingsCluster extends Cluster
{
    protected static ?string $navigationIcon = Heroicon::OutlinedSquares2x2;
}
```

### Adding Pages to a Cluster

Set the `$cluster` property on the page class:

```php
use App\Filament\Clusters\SettingsCluster;

protected static ?string $cluster = SettingsCluster::class;
```

When a page is in a cluster:
- It gets a URL prefixed with the cluster name
- A sub-navigation UI is added to the page
- The cluster name appears in breadcrumbs
- The individual page is removed from the main sidebar — only the cluster entry remains

### Setting Sub-Navigation Position

```php
use Filament\Pages\Enums\SubNavigationPosition;

// On the cluster (applies to all pages):
protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

// On an individual page:
protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
```

Options: `SubNavigationPosition::Start`, `SubNavigationPosition::End`, `SubNavigationPosition::Top` (renders as tabs).

### Removing Sub-Navigation from a Cluster

```php
protected static bool $shouldRegisterSubNavigation = false;
```

### Cluster Breadcrumb Customization

```php
protected static ?string $clusterBreadcrumb = 'cluster';

public static function getClusterBreadcrumb(): string
{
    return __('filament/clusters/cluster.name');
}
```

## Recommended Directory Structure for Clusters

```
app/Filament/
  Clusters/
    Settings/
      SettingsCluster.php
      Pages/
        ManageBranding.php
        ManageNotifications.php
      Resources/
        Colors/
          ColorResource.php
          Pages/
            CreateColor.php
            EditColor.php
            ListColors.php
```

This is a recommendation, not a requirement. The `make:filament-page` command will prompt you to create inside a cluster directory if clusters are discovered.

## Common Pitfalls

- `$navigationSort` controls ordering WITHIN a group. Items without a sort value are sorted alphabetically.
- Navigation groups defined via `navigationGroups()` establish an explicit order — items in unlisted groups may not appear.
- When using clusters, individual page navigation items are hidden from the main sidebar automatically.
- Generating URLs with `getUrl()` handles cluster prefixing automatically — avoid hardcoding URLs.
