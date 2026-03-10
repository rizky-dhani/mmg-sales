<?php

namespace App\Filament\Widgets\Reports;

use App\DTOs\ReportFilterData;
use App\Services\Reports\CustomerReportService;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;

class CustomerRevenueBySegmentWidget extends TableWidget
{
    use InteractsWithPageFilters;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->records(fn () => $this->getRecords())
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->label('Segment'),
                \Filament\Tables\Columns\TextColumn::make('revenue')
                    ->label('Revenue')
                    ->money('IDR')
                    ->alignRight(),
                \Filament\Tables\Columns\TextColumn::make('orders')
                    ->label('Orders')
                    ->alignRight(),
            ])
            ->paginated([10, 25, 50]);
    }

    protected function getRecords()
    {
        $filters = $this->pageFilters ?? [];
        $filterData = ReportFilterData::fromArray($filters);
        $service = app(CustomerReportService::class);
        $data = $service->generate($filterData);

        return $data->revenueBySegment;
    }
}
