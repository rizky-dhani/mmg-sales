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
        'category',
        'description',
        'unit_price',
        'unit_of_measure',
        'is_active',
        'principal_id',
    ];

    protected $codeColumn = 'product_code';

    protected $codePrefix = 'PRO';

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
        ];
    }

    public function principal(): BelongsTo
    {
        return $this->belongsTo(Principal::class);
    }
}
