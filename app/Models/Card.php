<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Card extends Model
{
    use HasFactory;

    protected $table = 'cards';
    protected $primaryKey = 'card_id';

    protected $fillable = [
        'card_uuid',
        'card_number',
        'card_group_id',
        'owner_type',
        'owner_id',
        'holder_id',
        'card_type_id',
        'card_status_id',
        'activation_date',
        'expiry_date',
        'last_used',
        'notes',
    ];

    protected $casts = [
        'activation_date' => 'datetime',
        'expiry_date' => 'date',
        'last_used' => 'datetime',
    ];

    protected $appends = [
        'is_active',
        'is_expired',
        'formatted_card_number',
        'holder_name',
        'owner_name',
    ];

    // ==================== BOOT ====================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($card) {
            if (empty($card->card_uuid)) {
                $card->card_uuid = Str::uuid()->toString();
            }

            if (empty($card->card_number)) {
                // Generate 9-digit card number
                $card->card_number = self::generateCardNumber();
            }
        });
    }

    // ==================== STATIC METHODS ====================

    /**
     * Generate a unique 9-digit card number.
     */
    public static function generateCardNumber(): string
    {
        do {
            // Generate 9-digit number starting with 1-9
            $number = mt_rand(100000000, 999999999);
            $cardNumber = (string) $number;
        } while (self::where('card_number', $cardNumber)->exists());

        return $cardNumber;
    }

    // ==================== RELATIONSHIPS ====================

    public function group(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CardGroup::class, 'card_group_id');
    }

    public function owner(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function holder(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'holder_id');
    }

    public function type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LookupValue::class, 'card_type_id');
    }

    public function status(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LookupValue::class, 'card_status_id');
    }

    public function albums(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Album::class, 'card_albums', 'card_id', 'album_id')
                    ->withTimestamps();
    }

    // ==================== SCOPES ====================

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereHas('status', function ($q) {
            $q->where('code', 'ACTIVE');
        });
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->whereHas('status', function ($q) {
            $q->where('code', 'INACTIVE');
        });
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('expiry_date', '<', now());
    }

    public function scopeValid(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('expiry_date')
              ->orWhere('expiry_date', '>=', now());
        });
    }

    public function scopeByGroup(Builder $query, $groupId): Builder
    {
        return $query->where('card_group_id', $groupId);
    }

    public function scopeByType(Builder $query, $type): Builder
    {
        if (is_numeric($type)) {
            return $query->where('card_type_id', $type);
        }

        return $query->whereHas('type', function ($q) use ($type) {
            $q->where('code', $type);
        });
    }

    public function scopeByStatus(Builder $query, $status): Builder
    {
        if (is_numeric($status)) {
            return $query->where('card_status_id', $status);
        }

        return $query->whereHas('status', function ($q) use ($status) {
            $q->where('code', $status);
        });
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('card_number', 'like', "%{$search}%")
              ->orWhere('card_uuid', 'like', "%{$search}%")
              ->orWhereHas('holder', function ($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
              });
        });
    }

    public function scopeWithCardRelations(Builder $query): Builder
    {
        return $query->with([
            'group:group_id,name',
            'holder:id,name,email,phone',
            'type:lookup_value_id,code,name',
            'status:lookup_value_id,code,name',
            'owner',
        ]);
    }

    public function loadCardRelations(): self
    {
        return $this->load([
            'group:group_id,name',
            'holder:id,name,email,phone',
            'type:lookup_value_id,code,name',
            'status:lookup_value_id,code,name',
            'owner',
        ]);
    }

    // ==================== ACCESSORS ====================

    public function getIsActiveAttribute(): bool
    {
        return $this->status->code === 'ACTIVE' ?? false;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date ? $this->expiry_date->isPast() : false;
    }

    public function getFormattedCardNumberAttribute(): string
    {
        return substr($this->card_number, 0, 3) . '-' .
               substr($this->card_number, 3, 3) . '-' .
               substr($this->card_number, 6, 3);
    }

    public function getHolderNameAttribute(): ?string
    {
        return $this->holder->name ?? null;
    }

    public function getOwnerNameAttribute(): ?string
    {
        if ($this->owner) {
            return $this->owner->name ?? null;
        }

        return $this->group->name ?? null;
    }

    // ==================== BUSINESS METHODS ====================

    /**
     * تنشيط البطاقة.
     */
    public function activate(): bool
    {
        $activeStatus = LookupValue::where('code', 'ACTIVE')->first();

        return $this->update([
            'card_status_id' => $activeStatus->lookup_value_id ?? null,
            'activation_date' => now(),
        ]);
    }

    /**
     * تعطيل البطاقة.
     */
    public function deactivate(): bool
    {
        $inactiveStatus = LookupValue::where('code', 'INACTIVE')->first();

        return $this->update([
            'card_status_id' => $inactiveStatus->lookup_value_id ?? null,
        ]);
    }

    /**
     * تحديث وقت الاستخدام الأخير.
     */
    public function markAsUsed(): bool
    {
        return $this->update(['last_used' => now()]);
    }

    /**
     * ربط الألبوم بالبطاقة.
     */
    public function attachAlbum(int $albumId): void
    {
        $this->albums()->syncWithoutDetaching($albumId);
    }

    /**
     * فصل الألبوم عن البطاقة.
     */
    public function detachAlbum(int $albumId): void
    {
        $this->albums()->detach($albumId);
    }

    /**
     * ربط عدة ألبومات بالبطاقة.
     */
    public function syncAlbums(array $albumIds): void
    {
        $this->albums()->sync($albumIds);
    }

    /**
     * التحقق مما إذا كانت البطاقة صالحة للاستخدام.
     */
    public function isValid(): bool
    {
        return $this->is_active && !$this->is_expired;
    }

    /**
     * الحصول على الأيام المتبقية حتى انتهاء الصلاحية.
     */
    public function getDaysUntilExpiry(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }

        return now()->diffInDays($this->expiry_date, false);
    }
}
