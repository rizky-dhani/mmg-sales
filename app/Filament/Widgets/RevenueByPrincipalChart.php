<?php

namespace App\Filament\Widgets;

use App\Filament\Traits\HasVisibilityScope;
use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;

class RevenueByPrincipalChart extends ChartWidget
{
    use HasVisibilityScope;

    protected ?string $heading = 'Revenue by Principal';

    protected static bool $isLazy = false;

    protected static ?string $height = '200px';

    protected static ?int $sort = 20;

    public static function canView(): bool
    {
        return true;
    }

    protected function getData(): array
    {
        $user = auth()->user();

        $baseQuery = Order::query();

        self::applyVisibilityScope($baseQuery, 'created_by');

        $data = (clone $baseQuery)
            ->where('order_date', '>=', now()->subMonths(11)->startOfMonth())
            ->join('principals', 'orders.principal_id', '=', 'principals.id')
            ->selectRaw('principals.name as principal_name, SUM(orders.total_amount) as total')
            ->groupBy('principals.name')
            ->orderByDesc('total')
            ->limit(8)
            ->pluck('total', 'principal_name')
            ->toArray();

        $labels = array_keys($data);
        $values = array_map(fn ($v) => round((float) $v, 2), array_values($data));

        $colors = [
            'rgb(59, 130, 246)',   // blue
            'rgb(34, 197, 94)',    // green
            'rgb(234, 179, 8)',    // yellow
            'rgb(239, 68, 68)',    // red
            'rgb(168, 85, 247)',   // purple
            'rgb(14, 165, 233)',   // sky
            'rgb(249, 115, 22)',   // orange
            'rgb(107, 114, 128)',  // gray
        ];

        return [
            'datasets' => [
                [
                    'data' => $values,
                    'backgroundColor' => array_slice($colors, 0, count($values)),
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
