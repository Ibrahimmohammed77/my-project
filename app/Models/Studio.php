<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Studio extends Model
{
    use HasFactory;

    protected $table = 'studios';
    protected $primaryKey = 'studio_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'description',
        'logo',
        'address',
        'city',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    /**
     * علاقة المستخدم المالك للاستوديو
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * علاقة مكتبات التخزين التابعة للاستوديو
     */
    public function storageLibraries()
    {
        return $this->hasMany(StorageLibrary::class, 'studio_id', 'studio_id');
    }

    /**
     * علاقة العمولات الخاصة بالاستوديو
     */
    public function commissions()
    {
        return $this->hasMany(Commission::class, 'studio_id', 'studio_id');
    }

    /**
     * علاقة الألبومات المملوكة للاستوديو
     */
    public function albums()
    {
        return $this->morphMany(Album::class, 'owner');
    }

    /**
     * علاقة البطاقات المملوكة للاستوديو
     */
    public function cards()
    {
        return $this->morphMany(Card::class, 'owner');
    }

    /**
     * علاقة العملاء (Placeholder)
     */
    public function customers()
    {
        return User::whereHas('cards', function($q) {
            $q->where('owner_id', $this->studio_id)
              ->where('owner_type', self::class);
        });
    }

    /**
     * Filter studios by search term and status.
     */
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(function ($q) use ($search) {
                // Search in Studio fields
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  // Search in related User fields (Name, Email, Phone)
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                  });
            });
        });

        $query->when($filters['status_id'] ?? null, function ($q, $statusId) {
            $q->whereHas('user', function ($q) use ($statusId) {
                $q->where('user_status_id', $statusId);
            });
        });
    }
}
