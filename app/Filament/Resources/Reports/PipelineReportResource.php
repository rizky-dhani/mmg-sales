<?php

namespace App\Filament\Resources\Reports;

use App\Filament\Resources\Reports\Pages\PipelineReportPage;
use App\Models\Project;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;

class PipelineReportResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;

    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Project Report';

    protected static ?string $slug = 'reports/project';

    public static function getPages(): array
    {
        return [
            'index' => PipelineReportPage::route('/'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('view_project_reports');
    }
}
