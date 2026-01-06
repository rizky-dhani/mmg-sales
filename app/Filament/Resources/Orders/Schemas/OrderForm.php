<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('tahun')
                    ->required()
                    ->numeric(),
                TextInput::make('bulan')
                    ->required()
                    ->numeric(),
                Select::make('department_id')
                    ->relationship('department', 'name')
                    ->required(),
                Select::make('head_position_id')
                    ->relationship('headPosition', 'name')
                    ->required(),
                Select::make('pm_jpm_pe_position_id')
                    ->relationship('pmJpmPePosition', 'name')
                    ->default(null),
                Select::make('rsm_asm_position_id')
                    ->relationship('rsmAsmPosition', 'name')
                    ->required(),
                Select::make('spv_position_id')
                    ->relationship('spvPosition', 'name')
                    ->required(),
                Select::make('sr_position_id')
                    ->relationship('srPosition', 'name')
                    ->required(),
                Select::make('area_city_id')
                    ->relationship('territory', 'name')
                    ->required(),
                Select::make('end_customer_id')
                    ->relationship('customer', 'facility_name')
                    ->required(),
                Select::make('customer_group_id')
                    ->relationship('customerGroup', 'name')
                    ->default(null),
                TextInput::make('cd_ncd_type')
                    ->required(),
                TextInput::make('ncd_subtype')
                    ->default(null),
                Select::make('segment_id')
                    ->relationship('segment', 'name')
                    ->required(),
                Select::make('principal_id')
                    ->relationship('principal', 'name')
                    ->required(),
                TextInput::make('reg_inst')
                    ->required(),
                Select::make('sales_type_id')
                    ->relationship('salesType', 'name')
                    ->required(),
                Select::make('item_id')
                    ->relationship('item', 'name')
                    ->required(),
                TextInput::make('qty_hna')
                    ->required()
                    ->numeric(),
                TextInput::make('total_hna_gross_sales')
                    ->required()
                    ->numeric(),
                TextInput::make('discount_on')
                    ->required()
                    ->numeric(),
                TextInput::make('net_sales_total')
                    ->required()
                    ->numeric(),
                Select::make('sub_segment_id')
                    ->relationship('subSegment', 'name')
                    ->default(null),
                TextInput::make('jual_kso')
                    ->required(),
                Select::make('distributor_id')
                    ->relationship('distributor', 'name')
                    ->required(),
                TextInput::make('order_number')
                    ->required(),
                Select::make('original_customer_id')
                    ->relationship('originalCustomer', 'facility_name')
                    ->default(null),
                Select::make('lead_id')
                    ->relationship('lead', 'company_name')
                    ->default(null),
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
                TextInput::make('subtotal')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('tax_amount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('discount_amount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('total_amount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                DatePicker::make('order_date')
                    ->required(),
                DatePicker::make('expected_delivery_date'),
                DatePicker::make('actual_delivery_date'),
                Textarea::make('shipping_address')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('billing_address')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('payment_method')
                    ->default(null),
                Select::make('payment_status')
                    ->options(['pending' => 'Pending', 'partial' => 'Partial', 'paid' => 'Paid', 'overdue' => 'Overdue'])
                    ->default('pending')
                    ->required(),
                Select::make('created_by')
                    ->relationship('creator', 'name')
                    ->default(null),
            ]);
    }
}
