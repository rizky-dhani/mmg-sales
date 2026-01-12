<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Item;
use App\Models\SubSegment;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $userPosition = $user?->position;

        return $schema
            ->components([
                Grid::make()
                    ->columnSpanFull()
                    ->columns(6)
                    ->schema([
                        Section::make('Order Details')
                            ->columnSpan(2)
                            ->columns(3)
                            ->schema([
                                TextInput::make('order_number')
                                    ->label('Order Number')
                                    ->default(fn () => 'MMG-ORD-'.now()->year.'-'.strtoupper(Str::random(8)))
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->readOnly(),
                                DatePicker::make('order_date')
                                    ->label('Order Date')
                                    ->default(now())
                                    ->required()
                                    ->readOnly(),
                                TextInput::make('payment_method')
                                    ->label('Payment Method')
                                    ->placeholder('e.g. Bank Transfer, Credit Card'),

                                // Automatic/Hidden fields
                                TextInput::make('tahun')
                                    ->default(now()->year)
                                    ->hidden(),
                                TextInput::make('bulan')
                                    ->default(now()->month)
                                    ->hidden(),
                                TextInput::make('status')
                                    ->default('pending')
                                    ->hidden(),
                                TextInput::make('payment_status')
                                    ->default('pending')
                                    ->hidden(),
                            ]),

                        Section::make('Organizational Hierarchy')
                            ->columnSpan(4)
                            ->columns(6)
                            ->schema([
                                Select::make('department_id')
                                    ->relationship('department', 'name')
                                    ->default($user?->department_id)
                                    ->required()
                                    ->preload()
                                    ->searchable(),
                                Select::make('sr_position_id')
                                    ->label('Sales Rep')
                                    ->relationship('srPosition', 'name')
                                    ->default($user?->position_id)
                                    ->required()
                                    ->preload()
                                    ->searchable(),
                                Select::make('spv_position_id')
                                    ->label('Supervisor')
                                    ->relationship('spvPosition', 'name')
                                    ->default($userPosition?->parent_id)
                                    ->required()
                                    ->preload()
                                    ->searchable(),
                                Select::make('rsm_asm_position_id')
                                    ->label('RSM/ASM')
                                    ->relationship('rsmAsmPosition', 'name')
                                    ->default($userPosition?->parent?->parent_id)
                                    ->required()
                                    ->preload()
                                    ->searchable(),
                                Select::make('head_position_id')
                                    ->label('Head')
                                    ->relationship('headPosition', 'name')
                                    ->default($userPosition?->parent?->parent?->parent_id)
                                    ->required()
                                    ->preload()
                                    ->searchable(),
                                Select::make('pm_jpm_pe_position_id')
                                    ->label('PM/JPM/PE')
                                    ->relationship('pmJpmPePosition', 'name')
                                    ->default(null)
                                    ->preload()
                                    ->searchable(),
                            ]),
                    ]),

                Section::make('Company, Logistics & Notes')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                Select::make('end_company_id')
                                    ->label('End Company')
                                    ->relationship('company', 'facility_name')
                                    ->searchable()
                                    ->required()
                                    ->preload(),
                                Select::make('area_city_id')
                                    ->label('Area / City')
                                    ->relationship('territory', 'name')
                                    ->searchable()
                                    ->required()
                                    ->preload(),
                                Select::make('company_group_id')
                                    ->label('Company Group')
                                    ->relationship('companyGroup', 'name')
                                    ->default(null)
                                    ->preload()
                                    ->searchable(),
                                Select::make('distributor_id')
                                    ->relationship('distributor', 'name')
                                    ->required()
                                    ->preload()
                                    ->searchable(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Grid::make(1)
                                    ->schema([
                                        Textarea::make('billing_address')
                                            ->rows(3)
                                            ->live()
                                            ->afterStateUpdated(function ($get, $set, $state) {
                                                if ($get('shipping_same_as_billing')) {
                                                    $set('shipping_address', $state);
                                                }
                                            }),
                                        Checkbox::make('shipping_same_as_billing')
                                            ->label('Shipping address same as billing')
                                            ->default(true)
                                            ->live()
                                            ->afterStateUpdated(function ($get, $set, $state) {
                                                if ($state) {
                                                    $set('shipping_address', $get('billing_address'));
                                                }
                                            }),
                                    ])->columnSpan(1),

                                Textarea::make('shipping_address')
                                    ->rows(3)
                                    ->disabled(fn ($get) => $get('shipping_same_as_billing'))
                                    ->dehydrated()
                                    ->columnSpan(1),
                            ]),

                        Textarea::make('notes')
                            ->columnSpanFull(),
                    ]),

                Section::make('Product & Financials')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('principal_id')
                                    ->relationship('principal', 'name')
                                    ->required()
                                    ->live()
                                    ->preload()
                                    ->searchable(),
                                Select::make('item_id')
                                    ->label('Product Item')
                                    ->options(fn ($get) => Item::query()
                                        ->where('principal_id', $get('principal_id'))
                                        ->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn ($set, $get) => self::calculateTotals($set, $get)),
                                Select::make('sales_type_id')
                                    ->relationship('salesType', 'name')
                                    ->required()
                                    ->preload()
                                    ->searchable(),
                                Select::make('segment_id')
                                    ->relationship('segment', 'name')
                                    ->required()
                                    ->live()
                                    ->preload()
                                    ->searchable(),
                                Select::make('sub_segment_id')
                                    ->label('Sub Segment')
                                    ->options(fn ($get) => SubSegment::query()
                                        ->where('segment_id', $get('segment_id'))
                                        ->pluck('name', 'id'))
                                    ->required()
                                    ->preload()
                                    ->searchable(),
                                Select::make('reg_inst')
                                    ->label('Reg / Inst')
                                    ->options([
                                        'REG' => 'REG',
                                        'INST' => 'INST',
                                    ])
                                    ->required()
                                    ->searchable(),
                                Select::make('cd_ncd_type')
                                    ->label('CD / NCD Type')
                                    ->options([
                                        'CD' => 'CD',
                                        'N-CD' => 'N-CD',
                                    ])
                                    ->required()
                                    ->live()
                                    ->searchable(),
                                TextInput::make('ncd_subtype')
                                    ->label('NCD Subtype')
                                    ->disabled(fn ($get) => $get('cd_ncd_type') !== 'N-CD')
                                    ->dehydrated(),
                                Select::make('jual_kso')
                                    ->label('Jual / KSO')
                                    ->options([
                                        'Jual' => 'Jual',
                                        'KSO' => 'KSO',
                                    ])
                                    ->required()
                                    ->searchable(),
                            ]),

                        Grid::make(4)
                            ->schema([
                                TextInput::make('qty_hna')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn ($set, $get) => self::calculateTotals($set, $get)),
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
                                    ->afterStateUpdated(fn ($set, $get) => self::calculateTotals($set, $get)),
                                TextInput::make('net_sales_total')
                                    ->label('Net Sales')
                                    ->numeric()
                                    ->prefix('IDR')
                                    ->readOnly(),
                            ]),
                    ]),

                Select::make('created_by')
                    ->relationship('creator', 'name')
                    ->default(auth()->id())
                    ->hidden()
                    ->preload(),
            ]);
    }

    protected static function calculateTotals($set, $get): void
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
