<?php

namespace App\Filament\Resources\Reports\Pages;

use App\DTOs\ReportFilterData;
use App\Exports\CustomerReportExport;
use App\Filament\Resources\Reports\CustomerReportResource;
use App\Filament\Widgets\Reports\CustomerDistributionWidget;
use App\Filament\Widgets\Reports\CustomerMonthlyRevenueTrendWidget;
use App\Filament\Widgets\Reports\CustomerReportStatsWidget;
use App\Filament\Widgets\Reports\CustomerRevenueByCustomerGroupWidget;
use App\Filament\Widgets\Reports\CustomerRevenueBySegmentWidget;
use App\Filament\Widgets\Reports\TopCustomersWidget;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Segment;
use App\Models\Territory;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Maatwebsite\Excel\Facades\Excel;

class CustomerReportPage extends Page
{
    use HasFiltersForm;

    protected static string $resource = CustomerReportResource::class;

    protected string $view = 'filament.resources.reports.pages.customer-report';

    public function mount(): void
    {
        $this->filters = [
            'start_date' => now()->startOfYear()->format('Y-m-d'),
            'end_date' => now()->endOfYear()->format('Y-m-d'),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            CustomerReportStatsWidget::class,
            CustomerMonthlyRevenueTrendWidget::class,
            CustomerDistributionWidget::class,
            TopCustomersWidget::class,
            CustomerRevenueByCustomerGroupWidget::class,
            CustomerRevenueBySegmentWidget::class,
        ];
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Date Range')
                    ->columnSpan(1)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('start_date')
                                    ->label('Start Date')
                                    ->default(now()->startOfYear())
                                    ->required(),
                                DatePicker::make('end_date')
                                    ->label('End Date')
                                    ->default(now()->endOfYear())
                                    ->required(),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Filters')
                    ->columnSpan(3)
                    ->schema([
                        Grid::make(5)
                            ->schema([
                                Select::make('customer_id')
                                    ->label('Customer')
                                    ->options(Customer::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload(),
                                Select::make('customer_group_id')
                                    ->label('Customer Group')
                                    ->options(CustomerGroup::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload(),
                                Select::make('segment_id')
                                    ->label('Segment')
                                    ->options(Segment::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload(),
                                Select::make('territory_id')
                                    ->label('Territory')
                                    ->options(Territory::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload(),
                                Select::make('user_id')
                                    ->label('Sales Representative')
                                    ->options(User::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload(),
                                Select::make('cd_ncd_type')
                                    ->label('CD/NCD Type')
                                    ->options([
                                        'CD' => 'CD',
                                        'NCD' => 'NCD',
                                    ]),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_excel')
                ->label('Export Excel')
                ->icon(Heroicon::OutlinedDocumentArrowDown)
                ->action(function () {
                    $filterData = ReportFilterData::fromArray($this->filters);

                    return Excel::download(
                        new CustomerReportExport($filterData),
                        'customer_report_'.now()->format('Y-m-d').'.xlsx'
                    );
                }),
            Action::make('reset_filters')
                ->label('Reset')
                ->icon(Heroicon::OutlinedArrowPath)
                ->color('gray')
                ->action(fn () => $this->mount()),
        ];
    }

    protected function getViewData(): array
    {
        return [];
    }
}
