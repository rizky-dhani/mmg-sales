<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Position;
use App\Models\Principal;
use App\Models\Product;
use App\Models\Project;
use App\Models\SubSegment;
use App\Models\User;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        /** @var User $user */
        $user = auth()->user();
        $userPosition = $user?->position;

        $findAncestorByLevel = function (?Position $position, int $targetLevel): ?int {
            if (! $position) {
                return null;
            }

            $current = $position;
            while ($current && $current->level > $targetLevel) {
                $current = $current->parent;
            }

            return $current?->id;
        };

        return $schema
            ->components([
                Section::make('Order Details')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                DatePicker::make('order_date')
                                    ->label('Order Date')
                                    ->default(now())
                                    ->required()
                                    ->readOnly()
                                    ->columnSpan(2),
                                TextInput::make('payment_method')
                                    ->label('Payment Method')
                                    ->placeholder('e.g. Bank Transfer, Credit Card')
                                    ->columnSpan(2),
                            ]),

                        Grid::make(5)
                            ->schema([
                                Select::make('cd_ncd_type')
                                    ->label('CD / NCD Type')
                                    ->options([
                                        'CD' => 'CD',
                                        'N-CD' => 'N-CD',
                                    ])
                                    ->required()
                                    ->live(),
                                Select::make('segment_id')
                                    ->label('Segment')
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
                                Select::make('sales_type_id')
                                    ->label('Purchase Type')
                                    ->relationship('salesType', 'name')
                                    ->required()
                                    ->preload()
                                    ->searchable(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('jual_kso')
                                    ->label('Jual / KSO')
                                    ->options([
                                        'Jual' => 'Jual',
                                        'KSO' => 'KSO',
                                        'Sample' => 'Sample',
                                    ])
                                    ->required()
                                    ->searchable(),
                                Select::make('distributor_id')
                                    ->label('Distributor')
                                    ->relationship('distributor', 'name')
                                    ->required()
                                    ->preload()
                                    ->searchable(),
                            ]),

                        TextInput::make('status')
                            ->default('pending')
                            ->hidden(),
                        TextInput::make('payment_status')
                            ->default('pending')
                            ->hidden(),
                        TextInput::make('tahun')
                            ->default(now()->year)
                            ->hidden(),
                        TextInput::make('bulan')
                            ->default(now()->month)
                            ->hidden(),
                    ]),

                Section::make('Sales Details')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(6)
                            ->schema([
                                Select::make('department_id')
                                    ->label('Department')
                                    ->relationship('department', 'name')
                                    ->default($user?->department_id)
                                    ->required()
                                    ->preload()
                                    ->searchable(),
                                Select::make('sr_position_id')
                                    ->label('Sales Rep')
                                    ->options(fn () => Position::where('level', 8)->pluck('name', 'id'))
                                    ->default($user?->position_id)
                                    ->preload()
                                    ->searchable(),
                                Select::make('pm_jpm_pe_position_id')
                                    ->label('PM/JPM/PE')
                                    ->options(fn () => Position::whereBetween('level', [5, 7])->pluck('name', 'id'))
                                    ->default(null)
                                    ->preload()
                                    ->searchable(),
                                Select::make('spv_position_id')
                                    ->label('Supervisor')
                                    ->options(fn () => Position::where('level', 4)->pluck('name', 'id'))
                                    ->default(fn () => $findAncestorByLevel($userPosition, 4))
                                    ->preload()
                                    ->searchable(),
                                Select::make('rsm_asm_position_id')
                                    ->label('RSM/ASM')
                                    ->options(fn () => Position::where('level', 3)->pluck('name', 'id'))
                                    ->default(fn () => $findAncestorByLevel($userPosition, 3))
                                    ->preload()
                                    ->searchable(),
                                Select::make('head_position_id')
                                    ->label('Head')
                                    ->options(fn () => Position::where('level', 2)->pluck('name', 'id'))
                                    ->default(fn () => $findAncestorByLevel($userPosition, 2))
                                    ->preload()
                                    ->searchable(),
                            ]),

                        Select::make('project_id')
                            ->label('Project')
                            ->relationship('project', 'title')
                            ->options(fn () => Project::query()
                                ->where(function ($query) {
                                    $userId = auth()->id();
                                    $query->where('assigned_to', $userId)
                                        ->orWhere('created_by', $userId);
                                })
                                ->pluck('title', 'id'))
                            ->searchable()
                            ->preload()
                            ->placeholder('Select a project'),
                    ]),

                Section::make('Customer Details')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('end_customer_id')
                                    ->label('End Customer')
                                    ->relationship('customer', 'name')
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
                                    ->preload()
                                    ->searchable(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Grid::make(1)
                                    ->schema([
                                        Textarea::make('billing_address')
                                            ->label('Billing Address')
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
                                    ->label('Shipping Address')
                                    ->rows(3)
                                    ->disabled(fn ($get) => $get('shipping_same_as_billing'))
                                    ->dehydrated()
                                    ->columnSpan(1),
                            ]),

                        Textarea::make('notes')
                            ->label('Notes')
                            ->columnSpanFull(),
                    ]),

                Section::make('Product Details')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('orderItems')
                            ->relationship()
                            ->schema([
                                Grid::make(5)
                                    ->schema([
                                        Select::make('principal_id')
                                            ->label('Principal')
                                            ->options(fn () => Principal::all()->pluck('name', 'id'))
                                            ->searchable()
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(fn ($set) => $set('product_id', null)),
                                        Select::make('product_id')
                                            ->label('Product')
                                            ->options(fn ($get) => $get('principal_id')
                                                ? Product::where('principal_id', $get('principal_id'))->pluck('name', 'id')
                                                : [])
                                            ->searchable()
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(fn ($set, $get) => self::updateLineTotal($set, $get)),
                                        TextInput::make('quantity')
                                            ->label('Quantity')
                                            ->numeric()
                                            ->default(1)
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(fn ($set, $get) => self::updateLineTotal($set, $get))
                                            ->columnSpan(1),
                                        Select::make('price_type')
                                            ->label('Price Type')
                                            ->options([
                                                'unit_price' => 'Unit Price',
                                                'ecatalog_price' => 'E-Catalog Price',
                                            ])
                                            ->default('unit_price')
                                            ->live()
                                            ->afterStateUpdated(fn ($set, $get) => self::updateLineTotal($set, $get))
                                            ->columnSpan(1),
                                        TextInput::make('unit_price')
                                            ->label('Unit Price')
                                            ->numeric()
                                            ->prefix('IDR')
                                            ->live()
                                            ->readOnly(fn ($get) => ! $get('principal_id') || ! $get('product_id'))
                                            ->afterStateUpdated(fn ($set, $get) => self::updateLineTotal($set, $get))
                                            ->columnSpan(1),
                                    ]),
                                Grid::make(1)
                                    ->schema([
                                        TextInput::make('subtotal')
                                            ->label('Line Total')
                                            ->numeric()
                                            ->prefix('IDR')
                                            ->readOnly()
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columns(1)
                            ->live()
                            ->afterStateUpdated(fn ($set, $get) => self::calculateOrderTotals($set, $get)),
                    ]),

                Section::make('Total')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('subtotal')
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
                                    ->afterStateUpdated(fn ($set, $get) => self::calculateNetSales($set, $get)),
                                TextInput::make('total_amount')
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

    protected static function updateLineTotal($set, $get): void
    {
        $productId = $get('product_id');
        $quantity = (int) $get('quantity');
        $priceType = $get('price_type') ?? 'unit_price';

        if ($productId) {
            $product = Product::find($productId);
            if ($product) {
                $unitPrice = $priceType === 'ecatalog_price'
                    ? ($product->ecatalog_price ?? $product->unit_price ?? 0)
                    : ($product->unit_price ?? 0);
                $set('unit_price', $unitPrice);
                $set('current_price', $unitPrice);
            }
        }

        $unitPrice = (float) $get('unit_price');
        $lineTotal = $quantity * $unitPrice;
        $set('subtotal', $lineTotal);
    }

    protected static function calculateOrderTotals($set, $get): void
    {
        $orderItems = $get('orderItems');
        $grossSales = 0;

        if ($orderItems && is_array($orderItems)) {
            foreach ($orderItems as $item) {
                $quantity = (int) ($item['quantity'] ?? 0);
                $unitPrice = (float) ($item['unit_price'] ?? 0);
                $grossSales += $quantity * $unitPrice;
            }
        }

        $set('subtotal', $grossSales);

        $discountPercent = (float) $get('discount_on');
        $netSales = $grossSales * (1 - ($discountPercent / 100));
        $set('total_amount', $netSales);
    }

    protected static function calculateNetSales($set, $get): void
    {
        $grossSales = (float) $get('subtotal');
        $discountPercent = (float) $get('discount_on');
        $netSales = $grossSales * (1 - ($discountPercent / 100));
        $set('total_amount', $netSales);
    }
}
