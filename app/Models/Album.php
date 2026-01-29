<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Album extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'albums';
    protected $primaryKey = 'album_id';

    protected $fillable = [
        'owner_type',
        'owner_id',
        'storage_library_id',
        'name',
        'description',
        'is_default',
        'is_visible',
        'view_count',
        'settings',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_visible' => 'boolean',
        'view_count' => 'integer',
        'settings' => 'array',
    ];

    /**
     * علاقة متعددة الأشكال للمالك
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /**
     * علاقة مكتبة التخزين
     */
    public function storageLibrary()
    {
        return $this->belongsTo(StorageLibrary::class, 'storage_library_id', 'storage_library_id');
    }

    /**
     * علاقة الصور
     */
    public function photos()
    {
        return $this->hasMany(Photo::class, 'album_id', 'album_id');
    }

    /**
     * علاقة البطاقات التي يمكنها الوصول إلى الألبوم
     */
    public function cards()
    {
        return $this->belongsToMany(Card::class, 'card_albums', 'album_id', 'card_id')
                    ->withTimestamps();
    }

    /**
     * الحصول على عدد الصور في الألبوم
     */
    public function getPhotoCountAttribute()
    {
        return $this->photos()->count();
    }

    /**
     * الحصول على الحجم الإجمالي للصور في الألبوم
     */
    public function getTotalSizeAttribute()
    {
        return $this->photos()->sum('file_size');
    }

    /**
     * نطاق الألبومات الافتراضية
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * نطاق الألبومات المرئية
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * نطاق الألبومات بناءً على نوع المالك
     */
    public function scopeByOwnerType($query, $ownerType)
    {
        return $query->where('owner_type', $ownerType);
    }

    /**
     * زيادة عداد المشاهدات
     */
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }
}
