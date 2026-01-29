<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $table = 'schools';
    protected $primaryKey = 'school_id';

    protected $fillable = [
        'user_id',
        'description',
        'logo',
        'school_type_id',
        'school_level_id',
        'address',
        'city',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    /**
     * علاقة المستخدم المالك للمدرسة
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * علاقة نوع المدرسة من القيم المحددة
     */
    public function type()
    {
        return $this->belongsTo(LookupValue::class, 'school_type_id', 'lookup_value_id');
    }

    /**
     * علاقة مستوى المدرسة من القيم المحددة
     */
    public function level()
    {
        return $this->belongsTo(LookupValue::class, 'school_level_id', 'lookup_value_id');
    }

    /**
     * علاقة مكتبات التخزين التابعة للمدرسة
     */
    public function storageLibraries()
    {
        return $this->hasMany(StorageLibrary::class, 'school_id', 'school_id');
    }

    /**
     * علاقة الألبومات المملوكة للمدرسة
     */
    public function albums()
    {
        return $this->morphMany(Album::class, 'owner');
    }

    /**
     * علاقة البطاقات المملوكة للمدرسة
     */
    public function cards()
    {
        return $this->morphMany(Card::class, 'owner');
    }

    /**
     * علاقة الطلاب (Placeholder)
     */
    public function students()
    {
        return $this->hasMany(User::class, 'id', 'user_id')->whereRaw('1=0');
    }

    /**
     * علاقة الصفوف (Placeholder)
     */
    public function classes()
    {
        // افترض وجود موديل ClassRoom مستقبلاً
        return $this->hasMany(User::class, 'id', 'user_id')->whereRaw('1=0');
    }
}
