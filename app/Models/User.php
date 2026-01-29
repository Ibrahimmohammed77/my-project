<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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

    /**
     * علاقة حالة المستخدم مع التحميل المتباطئ
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(LookupValue::class, 'user_status_id', 'lookup_value_id');
    }

    /**
     * علاقة نوع المستخدم مع التحميل المتباطئ
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(LookupValue::class, 'user_type_id', 'lookup_value_id');
    }


    /**
     * علاقة الاستوديو مع التحميل المتباطئ
     */
    public function studio(): HasOne
    {
        return $this->hasOne(Studio::class, 'user_id');
    }

    /**
     * علاقة المدرسة مع التحميل المتباطئ
     */
    public function school(): HasOne
    {
        return $this->hasOne(School::class, 'user_id');
    }

    /**
     * علاقة العميل مع التحميل المتباطئ
     */
    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'user_id');
    }

    /**
     * علاقة الاشتراكات مع التحميل المتباطئ
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'user_id');
    }

    /**
     * علاقة الاشتراك النشط حالياً مع تحسين الاستعلام
     */
    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class, 'user_id')
                    ->where(function($query) {
                        $query->where('end_date', '>=', now())
                              ->orWhere('auto_renew', true);
                    })
                    ->orderBy('end_date', 'desc')
                    ->limit(1);
    }

    /**
     * علاقة حساب التخزين الخاص بالمستخدم مع التحميل المتباطئ
     */
    public function storageAccount(): MorphOne
    {
        return $this->morphOne(StorageAccount::class, 'owner');
    }

    /**
     * علاقة الألبومات المملوكة للمستخدم مع التحميل المتباطئ
     */
    public function albums(): MorphMany
    {
        return $this->morphMany(Album::class, 'owner');
    }

    /**
     * علاقة البطاقات المرتبطة بالمستخدم كحامل للبطاقة مع التحميل المتباطئ
     */
    public function cards(): HasMany
    {
        return $this->hasMany(Card::class, 'holder_id');
    }

    /**
     * علاقة التنبيهات مع التحميل المتباطئ
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    /**
     * علاقة سجل النشاطات مع التحميل المتباطئ
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    /**
     * نطاقات البحث (Scopes)
     */

    /**
     * نطاق المستخدمين النشطين فقط
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * نطاق المستخدمين غير النشطين
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * نطاق المستخدمين الذين تم التحقق من بريدهم الإلكتروني
     */
    public function scopeEmailVerified(Builder $query): Builder
    {
        return $query->where('email_verified', true)
                     ->whereNotNull('email_verified_at');
    }

    /**
     * نطاق المستخدمين الذين تم التحقق من هاتفهم
     */
    public function scopePhoneVerified(Builder $query): Builder
    {
        return $query->where('phone_verified', true);
    }

    /**
     * نطاق المستخدمين حسب النوع
     */
    public function scopeByType(Builder $query, $type): Builder
    {
        if (is_numeric($type)) {
            return $query->where('user_type_id', $type);
        }

        return $query->whereHas('type', function ($q) use ($type) {
            $q->where('code', $type);
        });
    }

    /**
     * نطاق المستخدمين حسب الحالة
     */
    public function scopeByStatus(Builder $query, $status): Builder
    {
        if (is_numeric($status)) {
            return $query->where('user_status_id', $status);
        }

        return $query->whereHas('status', function ($q) use ($status) {
            $q->where('code', $status);
        });
    }

    /**
     * نطاق البحث عن المستخدمين
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('username', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    /**
     * نطاق المستخدمين الذين لديهم دور معين
     */
    public function scopeWithRole(Builder $query, $role): Builder
    {
        if (is_string($role)) {
            return $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }

        if (is_array($role)) {
            return $query->whereHas('roles', function ($q) use ($role) {
                $q->whereIn('name', $role);
            });
        }

        return $query;
    }

    /**
     * نطاق المستخدمين الذين لديهم اشتراك نشط
     */
    public function scopeHasActiveSubscription(Builder $query): Builder
    {
        return $query->whereHas('subscriptions', function ($q) {
            $q->where('end_date', '>=', now())
              ->orWhere('auto_renew', true);
        });
    }

    /**
     * نطاق المستخدمين المحدثين حديثاً
     */
    public function scopeRecentlyUpdated(Builder $query, int $hours = 24): Builder
    {
        return $query->where('updated_at', '>=', now()->subHours($hours));
    }

    /**
     * نطاق المستخدمين الذين قاموا بتسجيل الدخول مؤخراً
     */
    public function scopeRecentlyLoggedIn(Builder $query, int $days = 7): Builder
    {
        return $query->whereNotNull('last_login')
                     ->where('last_login', '>=', now()->subDays($days));
    }

    /**
     * نطاق المستخدمين مع التحميل المبكر للعلاقات الشائعة
     */
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

    /**
     * خصائص محسوبة
     */

    /**
     * الحصول على الاسم الكامل
     */
    public function getFullNameAttribute(): string
    {
        return $this->name;
    }


    /**
     * التحقق مما إذا كان المستخدم لديه صورة ملف شخصي
     */
    public function getHasProfileImageAttribute(): bool
    {
        return !empty($this->profile_image);
    }

    /**
     * الحصول على رابط صورة الملف الشخصي
     */
    public function getProfileImageUrlAttribute(): ?string
    {
        if (!$this->profile_image) {
            return null;
        }

        return asset('storage/' . $this->profile_image);
    }

    /**
     * الحصول على نوع المستخدم كـ string
     */
    public function getUserTypeCodeAttribute(): ?string
    {
        return $this->type->code ?? null;
    }

    /**
     * الحصول على حالة المستخدم كـ string
     */
    public function getUserStatusCodeAttribute(): ?string
    {
        return $this->status->code ?? null;
    }

    /**
     * الأحداث
     */

    /**
     * تحويل البريد الإلكتروني إلى أحرف صغيرة قبل الحفظ
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($user) {
            if ($user->isDirty('email')) {
                $user->email = strtolower($user->email);
            }
        });
    }

    /**
     * طرق إضافية للأداء
     */

    /**
     * تسجيل آخر دخول مع التحديث المجمع
     */
    public function recordLogin(): void
    {
        $this->timestamps = false; // تعطيل timestamps لتجنب تحديث updated_at
        $this->last_login = now();
        $this->save();
        $this->timestamps = true;

        // تسجيل النشاط
        ActivityLog::log($this->id, 'login', 'user', $this->id);
    }

    /**
     * تحديث حالة التحقق من البريد الإلكتروني
     */
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

    /**
     * تحديث حالة التحقق من الهاتف
     */
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

    /**
     * تعطيل المستخدم
     */
    public function deactivate(string $reason = null): bool
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

    /**
     * تنشيط المستخدم
     */
    public function activate(): bool
    {
        $updated = $this->update(['is_active' => true]);

        if ($updated) {
            ActivityLog::log($this->id, 'activated', 'user', $this->id);
        }

        return $updated;
    }

    /**
     * الحصول على الإحصائيات
     */
    public function getStats(): array
    {
        return [
            'albums_count' => $this->albums()->count(),
            'photos_count' => $this->albums()->withCount('photos')->get()->sum('photos_count'),
            'cards_count' => $this->cards()->count(),
            'notifications_count' => $this->notifications()->unread()->count(),
        ];
    }


    /**
     * تحميل التالي للعلاقات الكبيرة
     */
    public function loadMore($relation, $perPage = 10, $page = 1)
    {
        return $this->$relation()->paginate($perPage, ['*'], 'page', $page);
    }
}