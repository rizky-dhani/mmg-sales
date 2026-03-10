<?php

namespace App\Filament\Widgets\Reports;

use App\DTOs\ReportFilterData;
use App\Services\Reports\ProductReportService;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;

class TopSellingProductsWidget extends TableWidget
{
    use InteractsWithPageFilters;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->records(fn () => $this->getRecords())
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('rank')
                    ->label('Rank')
                    ->state(fn ($record, $rowLoop) => $rowLoop->iteration),
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->label('Product'),
                \Filament\Tables\Columns\TextColumn::make('revenue')
                    ->label('Revenue')
                    ->money('IDR')
                    ->alignRight(),
                \Filament\Tables\Columns\TextColumn::make('quantity')
                    ->label('Qty')
                    ->alignRight()
                    ->numeric(),
                \Filament\Tables\Columns\TextColumn::make('orders')
                    ->label('Orders')
                    ->alignRight(),
            ])
            ->paginated([15, 25, 50]);
    }

    protected function getRecords()
    {
        $filters = $this->pageFilters ?? [];
        $filterData = ReportFilterData::fromArray($filters);
        $service = app(ProductReportService::class);
        $data = $service->generate($filterData);

        return $data->topItems;
    }
}
