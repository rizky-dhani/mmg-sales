<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Territory extends Model
{
    protected $fillable = [
        'name',
        'wilayah_code',
        'type',
        'level',
        'parent_id',
        'manager_id',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Territory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Territory::class, 'parent_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
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

    public function getAllAncestorIds(): array
    {
        $ids = [];

        if ($this->parent) {
            $ids[] = $this->parent_id;
            $ids = array_merge($ids, $this->parent->getAllAncestorIds());
        }

        return $ids;
    }
}
