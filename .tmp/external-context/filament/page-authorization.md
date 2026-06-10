---
source: Filament Official Docs (v5.x)
library: Filament
package: filament/filament
topic: Authorization for custom pages (canAccess, middleware, policies)
fetched: 2026-06-05T00:00:00Z
official_docs: https://filamentphp.com/docs/5.x/navigation/custom-pages
---

# Authorization for Custom Pages

## `canAccess()` Method

Prevent pages from appearing in the menu AND being directly visited:

```php
public static function canAccess(): bool
{
    return auth()->user()->canManageSettings();
}
```

This is a **static** method — it runs on every Livewire request (not just page load).

### Authorization Re-runs on Every Livewire Request

Filament re-runs authorization on every Livewire request — both on initial page load and on every subsequent update (search, filter, pagination, action call, form interaction). If a user's permissions change while using the panel, the next interaction will be authorized against the current state.

For custom panel pages (anything extending `Filament\Pages\Page`), the page's `canAccess()` re-runs on every request via the `CanAuthorizeAccess` trait.

## Panel-Level Access

Panel-level access (`canAccessPanel`) is enforced by the panel's `Authenticate` middleware, which runs on every HTTP request (including Livewire updates). Users who lose panel access mid-session are bounced at the middleware layer before any component-level authorization is consulted.

## Gate-Based and Policy-Based Authorization

Filament automatically checks [Laravel Model Policies](https://laravel.com/docs/authorization#creating-policies) for standard CRUD operations on resources. When a policy exists for a resource's model, Filament checks methods like `viewAny()`, `create()`, `update()`, `view()`, `delete()` before allowing access.

**However**, Filament's automatic authorization only covers built-in resource operations. Any custom functionality — custom actions, custom pages, custom Livewire components, API endpoints — must be authorized by you.

### Custom Authorization on Actions

For bulk actions, you can check a policy method per record:

```php
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

BulkAction::make('delete')
    ->requiresConfirmation()
    ->authorizeIndividualRecords('delete')
    ->action(fn (Collection $records) => $records->each->delete());
```

## Authorization and the Livewire Request Lifecycle

When building custom Livewire components on a Filament panel, be aware that several Livewire activities run BEFORE Filament's authorization hooks fire:

- Public properties are deserialized from the request payload (Livewire's "synth" step) before any hooks run.
- The `boot()` and `boot{TraitName}()` lifecycle hooks fire before authorization.
- The user's `mount()` body runs before trait-level `mount{TraitName}` hooks on initial mount.
- Per-property `hydrate{PropertyName}()` hooks fire after Filament's hydrate-time authorization but still complete before the request progresses to update or render.

In practice, work that happens during these earlier hooks runs even when authorization will subsequently abort. Filament aborts before the response is rendered, so unauthorized data is never returned, but server-side side effects (database queries, audit logs, events, etc.) can occur before the abort.

**Best practice:** If your component does anything significant that should not happen for an unauthorized user — emitting events, writing to the database, calling external services — do that work in a method that runs AFTER authorization, such as:
- In the `mount()` body AFTER an explicit `$this->authorizeAccess()` call
- In an action method invoked via `wire:click` (always runs post-authorization)

Avoid putting such work in `boot()` or per-property hydrate hooks.

## Export File Authorization

For export-generated file downloads, by default only the user who started the export may download. To customize:

```php
use App\Policies\ExportPolicy;
use Filament\Actions\Exports\Models\Export;

protected $policies = [
    Export::class => ExportPolicy::class,
];
```

The `view()` method of the policy authorizes access to downloads. If you define a policy, the default logic (only the exporting user) is removed — you must add it back if needed.

## Common Pitfalls

- `canAccess()` is STATIC — don't try to access `$this` inside it (use `auth()->user()` instead).
- Authorization on custom pages is YOUR responsibility. Filament only auto-checks policies for standard resource CRUD.
- Remember that `boot()` and property hydrate hooks run BEFORE authorization. Sensitive operations should be placed after authorization checks.
- Resource-level `canAccess()` and page-specific `canAccess()` both run on every Livewire request.
