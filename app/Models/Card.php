<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    /**
     * إنشاء UUID تلقائياً
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($card) {
            if (empty($card->card_uuid)) {
                $card->card_uuid = \Illuminate\Support\Str::uuid()->toString();
            }

            if (empty($card->card_number)) {
                $card->card_number = (string) random_int(100000000000, 999999999999);
            }
        });
    }

    /**
     * علاقة المجموعة
     */
    public function group()
    {
        return $this->belongsTo(CardGroup::class, 'card_group_id', 'group_id');
    }

    /**
     * علاقة متعددة الأشكال للمالك
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /**
     * علاقة حامل البطاقة (المستخدم)
     */
    public function holder()
    {
        return $this->belongsTo(User::class, 'holder_id', 'id');
    }

    /**
     * علاقة نوع البطاقة من القيم المحددة
     */
    public function type()
    {
        return $this->belongsTo(LookupValue::class, 'card_type_id', 'lookup_value_id');
    }

    /**
     * علاقة حالة البطاقة من القيم المحددة
     */
    public function status()
    {
        return $this->belongsTo(LookupValue::class, 'card_status_id', 'lookup_value_id');
    }

    /**
     * علاقة الألبومات التي يمكن للبطاقة الوصول إليها
     */
    public function albums()
    {
        return $this->belongsToMany(Album::class, 'card_albums', 'card_id', 'album_id')
                    ->withTimestamps();
    }

    /**
     * التحقق مما إذا كانت البطاقة نشطة
     */
    public function getIsActiveAttribute()
    {
        $activeStatus = LookupValue::where('code', 'ACTIVE')->first();
        return $this->card_status_id == ($activeStatus->lookup_value_id ?? null);
    }

    /**
     * التحقق مما إذا كانت البطاقة منتهية الصلاحية
     */
    public function getIsExpiredAttribute()
    {
        if (!$this->expiry_date) {
            return false;
        }

        return $this->expiry_date->isPast();
    }

    /**
     * تنشيط البطاقة
     */
    public function activate()
    {
        $activeStatus = LookupValue::where('code', 'ACTIVE')->first();

        $this->update([
            'card_status_id' => $activeStatus->lookup_value_id,
            'activation_date' => now(),
        ]);
    }

    /**
     * تعطيل البطاقة
     */
    public function deactivate()
    {
        $inactiveStatus = LookupValue::where('code', 'INACTIVE')->first();

        $this->update([
            'card_status_id' => $inactiveStatus->lookup_value_id,
        ]);
    }

    /**
     * تحديث وقت الاستخدام الأخير
     */
    public function updateLastUsed()
    {
        $this->update(['last_used' => now()]);
    }

    /**
     * إضافة ألبوم للبطاقة
     */
    public function addAlbum($albumId)
    {
        $this->albums()->attach($albumId);
    }

    /**
     * إزالة ألبوم من البطاقة
     */
    public function removeAlbum($albumId)
    {
        $this->albums()->detach($albumId);
    }
}
