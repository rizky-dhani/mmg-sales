<?php

namespace App\Filament\Resources\Reports\Pages;

use App\DTOs\ReportFilterData;
use App\Exports\SalesReportExport;
use App\Filament\Resources\Reports\SalesReportResource;
use App\Filament\Widgets\Reports\RevenueByCustomerGroupWidget;
use App\Filament\Widgets\Reports\RevenueByPrincipalWidget;
use App\Filament\Widgets\Reports\RevenueTrendWidget;
use App\Filament\Widgets\Reports\SalesReportStatsWidget;
use App\Filament\Widgets\Reports\TopSalesRepresentativesWidget;
use App\Filament\Widgets\Reports\TopTerritoriesWidget;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Department;
use App\Models\Distributor;
use App\Models\Principal;
use App\Models\Segment;
use App\Models\SubSegment;
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

class SalesReportPage extends Page
{
    use HasFiltersForm;

    protected static string $resource = SalesReportResource::class;

    protected string $view = 'filament.resources.reports.pages.sales-report';

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
            SalesReportStatsWidget::class,
            TopSalesRepresentativesWidget::class,
            TopTerritoriesWidget::class,
            RevenueTrendWidget::class,
            RevenueByPrincipalWidget::class,
            RevenueByCustomerGroupWidget::class,
        ];
    }

    public function getFooterWidgetsColumns(): int | array
    {
        return 3;
    }
    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Date Range')
                    ->columnSpan(2)
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                DatePicker::make('start_date')
                                    ->label('Start Date')
                                    ->default(now()->startOfYear())
                                    ->required(),
                                DatePicker::make('end_date')
                                    ->label('End Date')
                                    ->default(now()->endOfYear())
                                    ->required(),
                                DatePicker::make('comparison_start_date')
                                    ->label('Comparison Start'),
                                DatePicker::make('comparison_end_date')
                                    ->label('Comparison End'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Filters')
                    ->columnSpan(2)
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                Select::make('user_id')
                                    ->label('Sales Representative')
                                    ->options(User::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload(),
                                Select::make('territory_id')
                                    ->label('Territory')
                                    ->options(Territory::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload(),
                                Select::make('department_id')
                                    ->label('Department')
                                    ->options(Department::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload(),
                                Select::make('principal_id')
                                    ->label('Principal')
                                    ->options(Principal::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload(),
                                Select::make('distributor_id')
                                    ->label('Distributor')
                                    ->options(Distributor::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload(),
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
                                Select::make('sub_segment_id')
                                    ->label('Sub Segment')
                                    ->options(SubSegment::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn ($get) => $get('segment_id') !== null),
                                Select::make('order_status')
                                    ->label('Order Status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'pending' => 'Pending',
                                        'confirmed' => 'Confirmed',
                                        'processing' => 'Processing',
                                        'shipped' => 'Shipped',
                                        'delivered' => 'Delivered',
                                        'cancelled' => 'Cancelled',
                                        'returned' => 'Returned',
                                    ]),
                                Select::make('cd_ncd_type')
                                    ->label('CD/NCD Type')
                                    ->options([
                                        'CD' => 'CD',
                                        'NCD' => 'NCD',
                                    ]),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
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
                        new SalesReportExport($filterData),
                        'sales_report_'.now()->format('Y-m-d').'.xlsx'
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
        return [
            'hasComparison' => ReportFilterData::fromArray($this->filters)->hasComparison(),
        ];
    }
}
