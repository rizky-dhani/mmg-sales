<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (self $model): void {
            if ($model->product_id && ! $model->item_id) {
                $model->item_id = $model->product_id;
            }
        });
    }

    protected $fillable = [
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
        return $this->belongsTo(Product::class, 'item_id');
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
