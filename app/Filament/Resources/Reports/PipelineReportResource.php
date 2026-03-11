<?php

namespace App\Filament\Resources\Reports;

use App\Filament\Resources\Reports\Pages\PipelineReportPage;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;

class PipelineReportResource extends Resource
{
    protected static ?string $model = \App\Models\Project::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;

    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Pipeline Report';

    protected static ?string $slug = 'reports/pipeline';

    public static function getPages(): array
    {
        return [
            'index' => PipelineReportPage::route('/'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('view_pipeline_reports');
    }
}
