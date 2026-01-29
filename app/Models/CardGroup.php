<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    /**
     * علاقة البطاقات في المجموعة
     */
    public function cards()
    {
        return $this->hasMany(Card::class, 'card_group_id', 'group_id');
    }

    /**
     * الحصول على عدد البطاقات المتاحة
     */
    public function getAvailableCardsAttribute()
    {
        return $this->sub_card_available - $this->sub_card_used;
    }

    /**
     * التحقق مما إذا كانت هناك بطاقات متاحة
     */
    public function hasAvailableCards()
    {
        return $this->available_cards > 0;
    }

    /**
     * زيادة عدد البطاقات المستخدمة
     */
    public function incrementUsedCards()
    {
        if ($this->hasAvailableCards()) {
            $this->increment('sub_card_used');
            return true;
        }
        return false;
    }

    /**
     * تقليل عدد البطاقات المستخدمة
     */
    public function decrementUsedCards()
    {
        if ($this->sub_card_used > 0) {
            $this->decrement('sub_card_used');
            return true;
        }
        return false;
    }
}
