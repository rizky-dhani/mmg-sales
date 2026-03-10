<?php

namespace App\Filament\Widgets\Reports;

use App\DTOs\ReportFilterData;
use App\Services\Reports\SalesReportService;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;

class RevenueByPrincipalWidget extends TableWidget
{
    use InteractsWithPageFilters;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->records(fn () => $this->getRecords())
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->label('Principal'),
                \Filament\Tables\Columns\TextColumn::make('revenue')
                    ->label('Revenue')
                    ->money('IDR')
                    ->alignRight(),
                \Filament\Tables\Columns\TextColumn::make('orders')
                    ->label('Orders')
                    ->alignRight(),
                \Filament\Tables\Columns\TextColumn::make('percentage')
                    ->label('% of Total')
                    ->state(fn ($record) => $this->calculatePercentage($record))
                    ->alignRight(),
            ])
            ->paginated([10, 25, 50]);
    }

    protected function getRecords()
    {
        $filters = $this->pageFilters ?? [];
        $filterData = ReportFilterData::fromArray($filters);
        $service = app(SalesReportService::class);
        $data = $service->generate($filterData);

        return $data->revenueByPrincipal;
    }

    private function calculatePercentage($record): string
    {
        $filters = $this->pageFilters ?? [];
        $filterData = ReportFilterData::fromArray($filters);
        $service = app(SalesReportService::class);
        $data = $service->generate($filterData);
        $total = $data->revenueByPrincipal->sum('revenue');
        $percentage = $total > 0 ? ($record['revenue'] / $total) * 100 : 0;

        return number_format($percentage, 1).'%';
    }
}
