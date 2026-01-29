<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Photo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'photos';
    protected $primaryKey = 'photo_id';

    protected $fillable = [
        'album_id',
        'original_name',
        'stored_name',
        'file_path',
        'file_size',
        'mime_type',
        'width',
        'height',
        'caption',
        'tags',
        'is_hidden',
        'is_archived',
        'view_count',
        'download_count',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'is_hidden' => 'boolean',
        'is_archived' => 'boolean',
        'view_count' => 'integer',
        'download_count' => 'integer',
        'tags' => 'array',
    ];

    /**
     * علاقة الألبوم
     */
    public function album()
    {
        return $this->belongsTo(Album::class, 'album_id', 'album_id');
    }

    /**
     * الحصول على المسار الكامل للملف
     */
    public function getFullPathAttribute()
    {
        return storage_path('app/' . $this->file_path);
    }

    /**
     * الحصول على عنوان URL للصورة
     */
    public function getUrlAttribute()
    {
        return asset('storage/' . str_replace('public/', '', $this->file_path));
    }

    /**
     * الحصول على الصورة المصغرة
     */
    public function getThumbnailUrlAttribute()
    {
        $path = dirname($this->file_path);
        $filename = pathinfo($this->stored_name, PATHINFO_FILENAME);

        return asset('storage/' . str_replace('public/', '', $path . '/thumbnails/' . $filename . '.jpg'));
    }

    /**
     * التحقق مما إذا كانت الصورة صورة (وليس فيديو)
     */
    public function getIsImageAttribute()
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * التحقق مما إذا كانت الصورة فيديو
     */
    public function getIsVideoAttribute()
    {
        return str_starts_with($this->mime_type, 'video/');
    }

    /**
     * نطاق الصور المرئية
     */
    public function scopeVisible($query)
    {
        return $query->where('is_hidden', false);
    }

    /**
     * نطاق الصور غير المؤرشفة
     */
    public function scopeNotArchived($query)
    {
        return $query->where('is_archived', false);
    }

    /**
     * زيادة عداد المشاهدات
     */
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    /**
     * زيادة عداد التحميلات
     */
    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }

    /**
     * البحث حسب الوسوم
     */
    public function scopeWithTags($query, array $tags)
    {
        return $query->where(function ($q) use ($tags) {
            foreach ($tags as $tag) {
                $q->orWhereJsonContains('tags', $tag);
            }
        });
    }
}
