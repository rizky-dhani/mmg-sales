<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasCode, HasFactory;

    protected $fillable = [
        'code',
        'order_id',
        'principal_id',
        'product_id',
        'item_id',
        'quantity',
        'unit_price',
        'current_price',
        'price_type',
        'discount_amount',
        'subtotal',
        'notes',
    ];

    protected $codeColumn = 'code';

    protected $codePrefix = 'ORI';

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'current_price' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function principal(): BelongsTo
    {
        return $this->belongsTo(Principal::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
