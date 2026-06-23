<?php

namespace App\Models;

use App\Services\ResourceCodeGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun',
        'bulan',
        'department_id',
        'head_position_id',
        'pm_jpm_pe_position_id',
        'rsm_asm_position_id',
        'spv_position_id',
        'sr_position_id',
        'area_city_id',
        'end_customer_id',
        'customer_group_id',
        'cd_ncd_type',
        'ncd_subtype',
        'segment_id',
        'principal_id',
        'reg_inst',
        'sales_type_id',
        'item_id',
        'qty_hna',
        'total_hna_gross_sales',
        'discount_on',
        'net_sales_total',
        'sub_segment_id',
        'jual_kso',
        'distributor_id',
        'order_number',
        'original_customer_id',
        'lead_id',
        'status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'order_date',
        'expected_delivery_date',
        'actual_delivery_date',
        'shipping_address',
        'billing_address',
        'notes',
        'payment_method',
        'created_by',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->order_number)) {
                $generator = app(ResourceCodeGenerator::class);
                $year = $model->tahun ?? now()->year;
                $model->order_number = $generator->generateForOrder($year);
            }
        });
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function headPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'head_position_id');
    }

    public function pmJpmPePosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'pm_jpm_pe_position_id');
    }

    public function rsmAsmPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'rsm_asm_position_id');
    }

    public function spvPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'spv_position_id');
    }

    public function srPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'sr_position_id');
    }

    public function territory(): BelongsTo
    {
        return $this->belongsTo(Territory::class, 'area_city_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'end_customer_id');
    }

    public function originalCustomer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'original_customer_id');
    }

    public function customerGroup(): BelongsTo
    {
        return $this->belongsTo(CustomerGroup::class);
    }

    public function segment(): BelongsTo
    {
        return $this->belongsTo(Segment::class);
    }

    public function subSegment(): BelongsTo
    {
        return $this->belongsTo(SubSegment::class);
    }

    public function principal(): BelongsTo
    {
        return $this->belongsTo(Principal::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function paymentStatuses(): HasMany
    {
        return $this->hasMany(PaymentStatus::class);
    }

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }
}
