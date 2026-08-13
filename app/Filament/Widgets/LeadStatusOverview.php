<?php

namespace App\Filament\Widgets;

use App\Filament\Traits\HasVisibilityScope;
use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class LeadStatusOverview extends BaseWidget
{
    use HasVisibilityScope;

    protected int|string|array $columnSpan = 'full';

    protected int|array|null $columns = 4;

    public static function canView(): bool
    {
        return true;
    }

    protected function getStats(): array
    {
        $user = auth()->user();

        $baseQuery = Lead::query();

        // Apply role-based visibility: staff sees own, managers see subordinates, etc.
        self::applyVisibilityScope($baseQuery, 'created_by');

        // Include leads where user is a collaborator (skip for Super Admin)
        if ($user && ! $user->hasRole('Super Admin')) {
            $baseQuery->orWhereHas('collaborators', fn (Builder $q) => $q->where('users.id', $user->id));
        }

        $totalCount = (clone $baseQuery)->count();
        $newCount = (clone $baseQuery)->where('status', 'new')->count();
        $contactedCount = (clone $baseQuery)->where('status', 'contacted')->count();
        $inProgressCount = (clone $baseQuery)->whereNotIn('status', ['new', 'won', 'lost'])->count();
        $proposalCount = (clone $baseQuery)->where('status', 'proposal')->count();
        $negotiationCount = (clone $baseQuery)->where('status', 'negotiation')->count();
        $wonCount = (clone $baseQuery)->where('status', 'won')->count();
        $lostCount = (clone $baseQuery)->where('status', 'lost')->count();

        return [
            Stat::make('New', $newCount)
                ->description('Fresh leads')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('gray')
                ->url(route('filament.admin.resources.leads.index', ['tableFilters[status][value]' => 'new'])),

            Stat::make('Contacted', $contactedCount)
                ->description('Initial outreach made')
                ->descriptionIcon('heroicon-m-chat-bubble-left-ellipsis')
                ->color('info')
                ->url(route('filament.admin.resources.leads.index', ['tableFilters[status][value]' => 'contacted'])),

            Stat::make('In Progress', $inProgressCount)
                ->description('Active negotiations/proposals')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info')
                ->url(route('filament.admin.resources.leads.index')),

            Stat::make('Proposal', $proposalCount)
                ->description('Proposals sent')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary')
                ->url(route('filament.admin.resources.leads.index', ['tableFilters[status][value]' => 'proposal'])),

            Stat::make('Negotiation', $negotiationCount)
                ->description('Under negotiation')
                ->descriptionIcon('heroicon-m-chat-bubble-bottom-center-text')
                ->color('warning')
                ->url(route('filament.admin.resources.leads.index', ['tableFilters[status][value]' => 'negotiation'])),

            Stat::make('Converted', $wonCount)
                ->description('Partnerships established')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->url(route('filament.admin.resources.leads.index', ['tableFilters[status][value]' => 'won'])),

            Stat::make('Not Converted', $lostCount)
                ->description('Opportunities learned from')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->url(route('filament.admin.resources.leads.index', ['tableFilters[status][value]' => 'lost'])),

            Stat::make('Total', $totalCount)
                ->description('All leads')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->url(route('filament.admin.resources.leads.index')),
        ];
    }
}
