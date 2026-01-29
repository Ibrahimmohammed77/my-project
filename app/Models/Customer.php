<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';
    protected $primaryKey = 'customer_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * علاقة المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * علاقة الجندر (النوع) من القيم المحددة
     */
    public function gender()
    {
        return $this->belongsTo(LookupValue::class, 'gender_id', 'lookup_value_id');
    }

    /**
     * الحصول على الاسم الكامل للعميل
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * علاقة الألبومات الخاصة بالعميل
     */
    public function albums()
    {
        return $this->morphMany(Album::class, 'owner');
    }

    /**
     * علاقة البطاقات الخاصة بالعميل
     */
    public function cards()
    {
        return $this->morphMany(Card::class, 'owner');
    }
}
