<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    use HasCode, HasFactory;

    // Level constants for order form role mapping
    // Lowest level number = highest position in the company hierarchy
    const HEAD_LEVEL = 1;

    const RSM_ASM_LEVEL = 2;

    const SPV_LEVEL = 3;

    const SR_LEVEL = 4;

    protected $fillable = [
        'name',
        'code',
        'level',
        'parent_id',
        'department_id',
        'description',
    ];

    protected $codeColumn = 'code';

    protected $codePrefix = 'POS';

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Position::class, 'parent_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function getAllDescendantIds(): array
    {
        $ids = [$this->id];

        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->getAllDescendantIds());
        }

        return $ids;
    }
}
