<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Item;
use App\Models\SubSegment;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Details')
                    ->columns(3)
                    ->schema([
                        TextInput::make('order_number')
                            ->label('Order Number')
                            ->default(fn () => 'MMG-ORD-'.now()->year.'-'.strtoupper(Str::random(8)))
                            ->required()
                            ->unique(ignoreRecord: true),
                        DatePicker::make('order_date')
                            ->label('Order Date')
                            ->default(now())
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, $state) {
                                if ($state) {
                                    $date = Carbon::parse($state);
                                    $set('tahun', $date->year);
                                    $set('bulan', $date->month);
                                }
                            }),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('tahun')
                                    ->label('Year')
                                    ->required()
                                    ->numeric()
                                    ->readOnly(),
                                TextInput::make('bulan')
                                    ->label('Month')
                                    ->required()
                                    ->numeric()
                                    ->readOnly(),
                            ])->columnSpan(1),
                    ]),

                Section::make('Status & Payment')
                    ->columns(3)
                    ->schema([
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'processing' => 'Processing',
                                'shipped' => 'Shipped',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled',
                                'returned' => 'Returned',
                            ])
                            ->default('draft')
                            ->required(),
                        Select::make('payment_status')
                            ->options([
                                'pending' => 'Pending',
                                'partial' => 'Partial',
                                'paid' => 'Paid',
                                'overdue' => 'Overdue',
                            ])
                            ->default('pending')
                            ->required(),
                        TextInput::make('payment_method')
                            ->placeholder('e.g. Bank Transfer, Credit Card'),
                    ]),

                Section::make('Organizational Hierarchy')
                    ->columns(3)
                    ->schema([
                        Select::make('department_id')
                            ->relationship('department', 'name')
                            ->required()
                            ->preload(),
                        Select::make('head_position_id')
                            ->label('Head')
                            ->relationship('headPosition', 'name')
                            ->required()
                            ->preload(),
                        Select::make('rsm_asm_position_id')
                            ->label('RSM/ASM')
                            ->relationship('rsmAsmPosition', 'name')
                            ->required()
                            ->preload(),
                        Select::make('spv_position_id')
                            ->label('Supervisor')
                            ->relationship('spvPosition', 'name')
                            ->required()
                            ->preload(),
                        Select::make('sr_position_id')
                            ->label('Sales Rep')
                            ->relationship('srPosition', 'name')
                            ->required()
                            ->preload(),
                        Select::make('pm_jpm_pe_position_id')
                            ->label('PM/JPM/PE')
                            ->relationship('pmJpmPePosition', 'name')
                            ->default(null)
                            ->preload(),
                    ]),

                Section::make('Customer & Logistics')
                    ->columns(2)
                    ->schema([
                        Select::make('end_customer_id')
                            ->label('End Customer')
                            ->relationship('customer', 'facility_name')
                            ->searchable()
                            ->required()
                            ->preload(),
                        Select::make('area_city_id')
                            ->label('Area / City')
                            ->relationship('territory', 'name')
                            ->searchable()
                            ->required()
                            ->preload(),
                        Select::make('customer_group_id')
                            ->label('Customer Group')
                            ->relationship('customerGroup', 'name')
                            ->default(null)
                            ->preload(),
                        Select::make('distributor_id')
                            ->relationship('distributor', 'name')
                            ->required()
                            ->preload(),
                    ]),

                Section::make('Product & Category')
                    ->columns(3)
                    ->schema([
                        Select::make('principal_id')
                            ->relationship('principal', 'name')
                            ->required()
                            ->live()
                            ->preload(),
                        Select::make('item_id')
                            ->label('Product Item')
                            ->options(fn (Get $get) => Item::query()
                                ->where('principal_id', $get('principal_id'))
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => self::calculateTotals($set, null)),
                        Select::make('segment_id')
                            ->relationship('segment', 'name')
                            ->required()
                            ->live()
                            ->preload(),
                        Select::make('sub_segment_id')
                            ->label('Sub Segment')
                            ->options(fn (Get $get) => SubSegment::query()
                                ->where('segment_id', $get('segment_id'))
                                ->pluck('name', 'id'))
                            ->required(),
                        Select::make('sales_type_id')
                            ->relationship('salesType', 'name')
                            ->required()
                            ->preload(),
                        TextInput::make('reg_inst')
                            ->label('Reg / Inst')
                            ->required(),
                        TextInput::make('cd_ncd_type')
                            ->label('CD / NCD Type')
                            ->required(),
                        TextInput::make('ncd_subtype')
                            ->label('NCD Subtype'),
                        TextInput::make('jual_kso')
                            ->label('Jual / KSO')
                            ->required(),
                    ]),

                Section::make('Financials')
                    ->columns(4)
                    ->schema([
                        TextInput::make('qty_hna')
                            ->label('Quantity')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateTotals($set, $get)),
                        TextInput::make('total_hna_gross_sales')
                            ->label('Gross Sales')
                            ->numeric()
                            ->prefix('IDR')
                            ->readOnly(),
                        TextInput::make('discount_on')
                            ->label('Discount (%)')
                            ->numeric()
                            ->default(0)
                            ->suffix('%')
                            ->live()
                            ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateTotals($set, $get)),
                        TextInput::make('net_sales_total')
                            ->label('Net Sales')
                            ->numeric()
                            ->prefix('IDR')
                            ->readOnly(),
                    ]),

                Section::make('Logistics & Notes')
                    ->columns(2)
                    ->schema([
                        Textarea::make('shipping_address')
                            ->rows(3),
                        Textarea::make('billing_address')
                            ->rows(3),
                        Textarea::make('notes')
                            ->columnSpanFull(),
                    ]),

                Select::make('created_by')
                    ->relationship('creator', 'name')
                    ->default(auth()->id())
                    ->hidden()
                    ->preload(),
            ]);
    }

    protected static function calculateTotals(Set $set, ?Get $get): void
    {
        if (! $get) {
            return;
        }

        $itemId = $get('item_id');
        $qty = (int) $get('qty_hna');
        $discountPercent = (float) $get('discount_on');

        if (! $itemId) {
            $set('total_hna_gross_sales', 0);
            $set('net_sales_total', 0);

            return;
        }

        $item = Item::find($itemId);
        $unitPrice = $item?->unit_price ?? 0;

        $gross = $unitPrice * $qty;
        $net = $gross * (1 - ($discountPercent / 100));

        $set('total_hna_gross_sales', $gross);
        $set('net_sales_total', $net);

        // Map to standard financial fields as well
        $set('subtotal', $gross);
        $set('total_amount', $net);
    }
}
