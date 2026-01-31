<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Studio extends Model
{
    use HasFactory;

    protected $table = 'studios';
    protected $primaryKey = 'studio_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'description',
        'logo',
        'address',
        'city',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    protected $appends = [
        'owner_name',
        'owner_email',
        'owner_phone',
    ];

    // ==================== RELATIONSHIPS ====================

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function storageLibraries(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StorageLibrary::class, 'studio_id', 'studio_id');
    }

    public function commissions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Commission::class, 'studio_id', 'studio_id');
    }

    public function albums(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Album::class, 'owner', 'owner_type', 'owner_id', 'studio_id');
    }

    public function cards(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Card::class, 'owner', 'owner_type', 'owner_id', 'studio_id');
    }

    /**
     * Get customers (users who hold cards linked to this studio).
     * Currently a placeholder to prevent crashes.
     */
    public function customers()
    {
        return $this->hasMany(User::class, 'id', 'id')->whereRaw('1=0');
    }

    // ==================== SCOPES ====================

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                  });
            });
        })
        ->when($filters['status_id'] ?? null, function ($q, $statusId) {
            $q->whereHas('user', function ($q) use ($statusId) {
                $q->where('user_status_id', $statusId);
            });
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereHas('user', function ($q) {
            $q->where('is_active', true);
        });
    }

    // ==================== ACCESSORS ====================

    public function getOwnerNameAttribute(): ?string
    {
        return $this->user->name ?? null;
    }

    public function getOwnerEmailAttribute(): ?string
    {
        return $this->user->email ?? null;
    }

    public function getOwnerPhoneAttribute(): ?string
    {
        return $this->user->phone ?? null;
    }

    public function getStatusAttribute(): ?string
    {
        return $this->user->status->name ?? null;
    }

    // ==================== BUSINESS METHODS ====================

    /**
     * Check if studio has active subscription.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->user && $this->user->activeSubscription()->exists();
    }

    /**
     * Get storage usage statistics.
     */
    public function getStorageStats(): array
    {
        $total = $this->storageLibraries()->sum('storage_limit');
        $used = $this->storageLibraries()->sum('used_space');

        return [
            'total' => $total,
            'used' => $used,
            'available' => $total - $used,
            'percentage' => $total > 0 ? round(($used / $total) * 100, 2) : 0,
        ];
    }
}
