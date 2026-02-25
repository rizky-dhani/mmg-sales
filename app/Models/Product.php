<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasCode, HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'category',
        'description',
        'unit_price',
        'currency',
        'unit_of_measure',
        'stock_quantity',
        'minimum_stock',
        'reorder_quantity',
        'is_active',
        'requires_prescription',
        'manufacturer',
        'expiry_date',
        'storage_requirements',
        'principal_id',
        'product_code',
    ];

    protected $codeColumn = 'product_code';

    protected $codePrefix = 'PRO';

    public function principal(): BelongsTo
    {
        return $this->belongsTo(Principal::class);
    }
}
