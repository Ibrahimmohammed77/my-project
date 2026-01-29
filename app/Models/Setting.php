<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';
    protected $primaryKey = 'setting_id';

    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_type_id',
        'description',
        'is_public',
    ];

    protected $casts = [
        'setting_value' => 'array',
        'is_public' => 'boolean',
    ];

    /**
     * علاقة نوع الإعداد من القيم المحددة
     */
    public function type()
    {
        return $this->belongsTo(LookupValue::class, 'setting_type_id', 'lookup_value_id');
    }

    /**
     * الحصول على قيمة الإعداد مع القيمة الافتراضية
     */
    public function getValueAttribute()
    {
        return $this->setting_value;
    }

    /**
     * نطاق الإعدادات العامة
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * نطاق الإعدادات الخاصة
     */
    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    /**
     * الحصول على إعداد بواسطة المفتاح
     */
    public static function getByKey($key, $default = null)
    {
        $setting = self::where('setting_key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return $setting->value;
    }

    /**
     * تحديث أو إنشاء إعداد
     */
    public static function set($key, $value, $typeId = null, $description = null, $isPublic = false)
    {
        return self::updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => $value,
                'setting_type_id' => $typeId,
                'description' => $description,
                'is_public' => $isPublic,
            ]
        );
    }

    /**
     * الحصول على جميع الإعدادات العامة كمصفوفة
     */
    public static function getPublicSettings()
    {
        return self::public()->get()->pluck('value', 'setting_key')->toArray();
    }

    /**
     * التحقق مما إذا كان المفتاح موجوداً
     */
    public static function has($key)
    {
        return self::where('setting_key', $key)->exists();
    }

    /**
     * الحصول على الإعدادات حسب النوع
     */
    public static function getByType($typeCode)
    {
        $type = LookupValue::where('code', $typeCode)->first();

        if (!$type) {
            return collect();
        }

        return self::where('setting_type_id', $type->lookup_value_id)->get();
    }

    /**
     * معالجة قيمة الإعداد بناءً على النوع
     */
    public function getProcessedValue()
    {
        $type = $this->type->code ?? '';
        $value = $this->value;

        switch ($type) {
            case 'boolean':
                return (bool) $value;
            case 'integer':
                return (int) $value;
            case 'decimal':
                return (float) $value;
            case 'json':
                return is_array($value) ? $value : json_decode($value, true);
            case 'array':
                return is_array($value) ? $value : explode(',', $value);
            default:
                return $value;
        }
    }
}
