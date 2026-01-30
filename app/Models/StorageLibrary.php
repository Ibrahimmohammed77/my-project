<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorageLibrary extends Model
{
    use HasFactory;

    protected $table = 'storage_libraries';
    protected $primaryKey = 'storage_library_id';

    protected $fillable = [
        'studio_id',
        'user_id',
        'name',
        'description',
        'storage_limit',
    ];

    protected $casts = [
        'storage_limit' => 'integer',
    ];

    /**
     * علاقة الاستوديو
     */
    public function studio()
    {
        return $this->belongsTo(Studio::class, 'studio_id', 'studio_id');
    }

    /**
     * علاقة المستخدم المالك لمكتبة التخزين
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * علاقة الصور في مكتبة التخزين عبر الألبومات
     */
    public function photos()
    {
        return $this->hasManyThrough(Photo::class, Album::class, 'storage_library_id', 'album_id', 'storage_library_id', 'album_id');
    }

    /**
     * علاقة الألبومات في مكتبة التخزين
     */
    public function albums()
    {
        return $this->hasMany(Album::class, 'storage_library_id', 'storage_library_id');
    }

    /**
     * علاقة العمولات المرتبطة بمكتبة التخزين
     */
    public function commissions()
    {
        return $this->hasMany(Commission::class, 'storage_library_id', 'storage_library_id');
    }

    /**
     * الحصول على المساحة المستخدمة في المكتبة
     */
    public function getUsedStorageAttribute()
    {
        return $this->photos()->sum('file_size');
    }

    /**
     * الحصول على المساحة المتاحة
     */
    public function getAvailableStorageAttribute()
    {
        if ($this->storage_limit > 0) {
            return $this->storage_limit - $this->used_storage;
        }

        $plan = $this->user->activeSubscription?->plan;
        return $plan ? $plan->storage_limit - $this->used_storage : 0;
    }
}
