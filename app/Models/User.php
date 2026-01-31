<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    HasOne,
    HasMany,
    MorphOne,
    MorphMany
};

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasPermissions;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'phone',
        'profile_image',
        'user_status_id',
        'user_type_id',
        'verification_code',
        'verification_expiry',
        'last_login',
        'is_active',
        'email_verified',
        'email_verified_at',
        'phone_verified',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'verification_expiry' => 'datetime',
        'last_login' => 'datetime',
        'is_active' => 'boolean',
        'email_verified' => 'boolean',
        'phone_verified' => 'boolean',
    ];

    protected $appends = [
        'full_name',
        'has_profile_image',
        'profile_image_url',
        'user_type_code',
        'user_status_code'
    ];

    // ==================== RELATIONSHIPS ====================

    public function status(): BelongsTo
    {
        return $this->belongsTo(LookupValue::class, 'user_status_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(LookupValue::class, 'user_type_id');
    }

    public function studio(): HasOne
    {
        return $this->hasOne(Studio::class);
    }

    public function school(): HasOne
    {
        return $this->hasOne(School::class);
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
                    ->where(function($query) {
                        $query->where('end_date', '>=', now())
                              ->orWhere('auto_renew', true);
                    })
                    ->latest('end_date')
                    ->limit(1);
    }

    public function storageAccount(): MorphOne
    {
        return $this->morphOne(StorageAccount::class, 'owner');
    }

    public function albums(): MorphMany
    {
        return $this->morphMany(Album::class, 'owner');
    }

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class, 'holder_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    // ==================== SCOPES ====================

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    public function scopeEmailVerified(Builder $query): Builder
    {
        return $query->where('email_verified', true)
                     ->whereNotNull('email_verified_at');
    }

    public function scopePhoneVerified(Builder $query): Builder
    {
        return $query->where('phone_verified', true);
    }

    public function scopeByType(Builder $query, $type): Builder
    {
        return is_numeric($type)
            ? $query->where('user_type_id', $type)
            : $query->whereHas('type', fn($q) => $q->where('code', $type));
    }

    public function scopeByStatus(Builder $query, $status): Builder
    {
        return is_numeric($status)
            ? $query->where('user_status_id', $status)
            : $query->whereHas('status', fn($q) => $q->where('code', $status));
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('username', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    public function scopeWithRole(Builder $query, $role): Builder
    {
        if (is_string($role)) {
            return $query->whereHas('roles', fn($q) => $q->where('name', $role));
        }

        if (is_array($role)) {
            return $query->whereHas('roles', fn($q) => $q->whereIn('name', $role));
        }

        return $query;
    }

    public function scopeHasActiveSubscription(Builder $query): Builder
    {
        return $query->whereHas('subscriptions', function ($q) {
            $q->where('end_date', '>=', now())
              ->orWhere('auto_renew', true);
        });
    }

    public function scopeRecentlyUpdated(Builder $query, int $hours = 24): Builder
    {
        return $query->where('updated_at', '>=', now()->subHours($hours));
    }

    public function scopeRecentlyLoggedIn(Builder $query, int $days = 7): Builder
    {
        return $query->whereNotNull('last_login')
                     ->where('last_login', '>=', now()->subDays($days));
    }

    public function scopeWithCommonRelations(Builder $query): Builder
    {
        return $query->with([
            'status:id,code,name',
            'type:id,code,name',
            'roles:id,name',
            'activeSubscription',
            'storageAccount',
        ]);
    }

    // ==================== ACCESSORS ====================

    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    public function getHasProfileImageAttribute(): bool
    {
        return !empty($this->profile_image);
    }

    public function getProfileImageUrlAttribute(): ?string
    {
        return $this->profile_image ? asset('storage/' . $this->profile_image) : null;
    }

    public function getUserTypeCodeAttribute(): ?string
    {
        return $this->type->code ?? null;
    }

    public function getUserStatusCodeAttribute(): ?string
    {
        return $this->status->code ?? null;
    }

    // ==================== BOOT ====================

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($user) {
            if ($user->isDirty('email')) {
                $user->email = strtolower($user->email);
            }
        });
    }

    // ==================== BUSINESS METHODS ====================

    public function recordLogin(): void
    {
        $this->timestamps = false;
        $this->last_login = now();
        $this->save();
        $this->timestamps = true;

        ActivityLog::log($this->id, 'login', 'user', $this->id);
    }

    public function verifyEmail(): bool
    {
        $updated = $this->update([
            'email_verified' => true,
            'email_verified_at' => now(),
            'verification_code' => null,
            'verification_expiry' => null,
        ]);

        if ($updated) {
            ActivityLog::log($this->id, 'email_verified', 'user', $this->id);
        }

        return $updated;
    }

    public function verifyPhone(): bool
    {
        $updated = $this->update([
            'phone_verified' => true,
            'verification_code' => null,
            'verification_expiry' => null,
        ]);

        if ($updated) {
            ActivityLog::log($this->id, 'phone_verified', 'user', $this->id);
        }

        return $updated;
    }

    public function deactivate(?string $reason = null): bool
    {
        $data = ['is_active' => false];

        if ($reason) {
            $data['deactivation_reason'] = $reason;
        }

        $updated = $this->update($data);

        if ($updated) {
            ActivityLog::log($this->id, 'deactivated', 'user', $this->id, ['reason' => $reason]);
        }

        return $updated;
    }

    public function activate(): bool
    {
        $updated = $this->update(['is_active' => true]);

        if ($updated) {
            ActivityLog::log($this->id, 'activated', 'user', $this->id);
        }

        return $updated;
    }

    public function getStats(): array
    {
        return [
            'albums_count' => $this->albums()->count(),
            'photos_count' => $this->albums()->withCount('photos')->get()->sum('photos_count'),
            'cards_count' => $this->cards()->count(),
            'notifications_count' => $this->notifications()->unread()->count(),
        ];
    }

    public function loadMore(string $relation, int $perPage = 10, int $page = 1)
    {
        return $this->$relation()->paginate($perPage, ['*'], 'page', $page);
    }
}
