<?php

namespace App\Filament\Widgets\Reports;

use App\DTOs\ReportFilterData;
use App\Services\Reports\PipelineReportService;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;

class PipelineBySalesRepresentativeWidget extends TableWidget
{
    use InteractsWithPageFilters;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->records(fn () => $this->getRecords())
            ->columns([
                TextColumn::make('name')
                    ->label('Sales Rep'),
                TextColumn::make('creator_name')
                    ->label('Added By')
                    ->state(fn ($record): string => $record['creator_name'] ?? '-'),
                TextColumn::make('count')
                    ->label('Projects')
                    ->alignRight(),
                TextColumn::make('value')
                    ->label('Pipeline Value')
                    ->money('IDR')
                    ->alignRight(),
            ])
            ->paginated([10, 25, 50]);
    }

    protected function getRecords()
    {
        $filters = $this->pageFilters ?? [];
        $filterData = ReportFilterData::fromArray($filters);
        $service = app(PipelineReportService::class);
        $data = $service->generate($filterData);

        return $data->pipelineBySalesRep;
    }
}
