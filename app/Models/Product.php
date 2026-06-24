<?php

namespace App\Models;

use App\Services\ResourceCodeGenerator;
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
        'internal_code',
        'unit_price',
        'ecatalog_price',
        'unit_of_measure',
        'is_active',
        'principal_id',
    ];

    protected $codeColumn = 'internal_code';

    protected function casts(): array
    {
        return [
            'unit_price' => 'integer',
            'ecatalog_price' => 'integer',
        ];
    }

    public function principal(): BelongsTo
    {
        return $this->belongsTo(Principal::class);
    }

    public function generateCode(): string
    {
        $principal = $this->principal ?? Principal::find($this->principal_id);
        $initial = $principal?->initial ?? 'XX';

        $generator = app(ResourceCodeGenerator::class);
        $sequence = $generator->getNextSequenceValue('TD', null, $this->getTable(), $this->getCodeColumnName());

        return sprintf('%s-TD-%06d', strtoupper($initial), $sequence);
    }
}
