<?php

namespace App\Filament\Widgets;

use App\Filament\Traits\HasVisibilityScope;
use App\Models\Lead;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;

class LeadStatusChart extends ChartWidget
{
    use HasVisibilityScope;

    protected ?string $heading = 'Lead Status';

    protected static bool $isLazy = false;

    protected static ?string $height = '200px';

    public static function canView(): bool
    {
        return true;
    }

    protected function getData(): array
    {
        $user = auth()->user();

        $baseQuery = Lead::query();

        self::applyVisibilityScope($baseQuery, 'created_by');

        if ($user && ! $user->hasRole('Super Admin')) {
            $baseQuery->orWhereHas('collaborators', fn (Builder $q) => $q->where('users.id', $user->id));
        }

        $statuses = ['new', 'contacted', 'qualified', 'proposal', 'negotiation', 'won', 'lost'];
        $counts = (clone $baseQuery)
            ->whereIn('status', $statuses)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $labels = ['New', 'Contacted', 'Qualified', 'Proposal', 'Negotiation', 'Converted', 'Not Converted'];
        $data = array_map(fn ($s) => $counts[$s] ?? 0, $statuses);
        $total = array_sum($data);

        $labels = array_map(
            fn ($label, $count) => $total > 0
                ? "{$label} ({$count} | " . round($count / $total * 100) . '%)'
                : $label,
            $labels,
            $data,
        );

        return [
            'datasets' => [
                [
                    'label' => 'Leads',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgb(107, 114, 128)',  // gray — new
                        'rgb(14, 165, 233)',   // info — contacted
                        'rgb(168, 85, 247)',   // purple — qualified
                        'rgb(59, 130, 246)',   // blue — proposal
                        'rgb(234, 179, 8)',    // warning — negotiation
                        'rgb(34, 197, 94)',    // success — converted
                        'rgb(239, 68, 68)',    // danger — not converted
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'right',
                ],
            ],
        ];
    }
}
