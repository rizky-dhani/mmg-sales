<?php

namespace App\Models;

use App\Traits\HasCode;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasCode, HasFactory, HasRoles, Notifiable;

    protected static function booted(): void
    {
        static::creating(function (self $user): void {
            if (empty($user->password)) {
                $user->password = Hash::make('Mmg2026!');
            }
        });

        static::updating(function (self $user): void {
            if ($user->isDirty('department_id')) {
                $newDepartmentId = $user->department_id;
                $rolesToDetach = $user->roles()
                    ->whereNotNull('department_id')
                    ->where('department_id', '!=', $newDepartmentId)
                    ->pluck('id');

                if ($rolesToDetach->isNotEmpty()) {
                    $user->roles()->detach($rolesToDetach);
                }
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'department_id',
        'position_id',
        'territory_id',
        'manager_id',
        'sales_target',
        'target_metadata',
        'code',
        'is_active',
    ];

    protected $codeColumn = 'code';

    protected $codePrefix = 'USR';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'sales_target' => 'decimal:2',
            'target_metadata' => 'array',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function territory(): BelongsTo
    {
        return $this->belongsTo(Territory::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function targets(): HasMany
    {
        return $this->hasMany(Target::class);
    }

    /**
     * Check if user has a role by base name (matches 'Staff', 'Staff - Sales', etc.).
     */
    public function hasBaseRole(string $roleName): bool
    {
        return $this->roles()
            ->where(function ($q) use ($roleName) {
                $q->where('name', $roleName)
                    ->orWhere('name', 'like', "{$roleName} - %");
            })
            ->exists();
    }

    public function hasAnyBaseRole(array $roleNames): bool
    {
        return $this->roles()
            ->where(function ($query) use ($roleNames): void {
                foreach ($roleNames as $name) {
                    $query->orWhere(function ($q) use ($name) {
                        $q->where('name', $name)
                            ->orWhere('name', 'like', "{$name} - %");
                    });
                }
            })
            ->exists();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
