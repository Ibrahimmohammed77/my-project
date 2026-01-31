<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CardGroup extends Model
{
    use HasFactory;

    protected $table = 'card_groups';
    protected $primaryKey = 'group_id';

    protected $fillable = [
        'name',
        'description',
        'sub_card_available',
        'sub_card_used',
    ];

    protected $casts = [
        'sub_card_available' => 'integer',
        'sub_card_used' => 'integer',
    ];

    protected $appends = [
        'available_cards',
        'usage_percentage',
        'is_available',
    ];

    // ==================== RELATIONSHIPS ====================

    public function cards(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Card::class, 'card_group_id');
    }

    // ==================== SCOPES ====================

    public function scopeWithAvailableCards(Builder $query): Builder
    {
        return $query->whereRaw('sub_card_available > sub_card_used');
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('name');
    }

    // ==================== ACCESSORS ====================

    public function getAvailableCardsAttribute(): int
    {
        return max(0, $this->sub_card_available - $this->sub_card_used);
    }

    public function getUsagePercentageAttribute(): float
    {
        if ($this->sub_card_available == 0) {
            return 0;
        }

        return round(($this->sub_card_used / $this->sub_card_available) * 100, 2);
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->available_cards > 0;
    }

    // ==================== BUSINESS METHODS ====================

    /**
     * التحقق مما إذا كانت هناك بطاقات متاحة.
     */
    public function hasAvailableCards(): bool
    {
        return $this->available_cards > 0;
    }

    /**
     * زيادة عدد البطاقات المستخدمة.
     */
    public function incrementUsedCards(): bool
    {
        if ($this->hasAvailableCards()) {
            $this->increment('sub_card_used');
            return true;
        }

        return false;
    }

    /**
     * تقليل عدد البطاقات المستخدمة.
     */
    public function decrementUsedCards(): bool
    {
        if ($this->sub_card_used > 0) {
            $this->decrement('sub_card_used');
            return true;
        }

        return false;
    }

    /**
     * زيادة السعة المتاحة.
     */
    public function increaseCapacity(int $amount): bool
    {
        if ($amount <= 0) {
            return false;
        }

        return $this->update([
            'sub_card_available' => $this->sub_card_available + $amount
        ]);
    }

    /**
     * تقليل السعة المتاحة.
     */
    public function decreaseCapacity(int $amount): bool
    {
        if ($amount <= 0 || $amount > $this->available_cards) {
            return false;
        }

        return $this->update([
            'sub_card_available' => $this->sub_card_available - $amount
        ]);
    }

    /**
     * الحصول على إحصائيات المجموعة.
     */
    public function getStats(): array
    {
        return [
            'total_cards' => $this->cards()->count(),
            'active_cards' => $this->cards()->active()->count(),
            'inactive_cards' => $this->cards()->inactive()->count(),
            'expired_cards' => $this->cards()->expired()->count(),
            'available_cards' => $this->available_cards,
            'usage_percentage' => $this->usage_percentage,
        ];
    }
}
