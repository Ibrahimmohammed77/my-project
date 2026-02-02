<?php

namespace App\Helpers;

use App\Models\School;
use App\Models\StorageLibrary;
use App\Models\Studio;
use Exception;

class StorageLibraryHelper
{
    /**
     * Ensure storage library exists for school, create if missing
     */
    public static function ensureStorageLibraryForSchool(School $school): StorageLibrary
    {
        $storageLibrary = StorageLibrary::where('school_id', $school->school_id)->first();

        if (!$storageLibrary) {
            $storageLibrary = self::createStorageLibraryForSchool($school);
        }

        return $storageLibrary;
    }

    /**
     * Create storage library for school based on active subscription
     */
    public static function createStorageLibraryForSchool(School $school): StorageLibrary
    {
        $user = $school->user;
        $activeSubscription = $user->activeSubscription;

        if (!$activeSubscription) {
            throw new Exception('المدرسة ليس لديها اشتراك نشط');
        }

        $plan = $activeSubscription->plan;
        if (!$plan) {
            throw new Exception('خطة الاشتراك غير موجودة');
        }

        // Get storage quota from plan (in MB), convert to bytes
        $storageQuotaMB = $plan->storage_quota ?? 1024; // Default 1GB
        $storageLimitBytes = $storageQuotaMB * 1024 * 1024;

        // Create storage library
        $storageLibrary = StorageLibrary::create([
            'name' => 'مكتبة ' . $school->name,
            'description' => 'مكتبة التخزين الرئيسية للمدرسة',
            'school_id' => $school->school_id,
            'storage_limit' => $storageLimitBytes,
            'used_storage' => 0,
        ]);

        return $storageLibrary;
    }

    /**
     * Ensure storage library exists for studio, create if missing
     */
    public static function ensureStorageLibraryForStudio(Studio $studio): StorageLibrary
    {
        $storageLibrary = StorageLibrary::where('studio_id', $studio->studio_id)->first();

        if (!$storageLibrary) {
            $storageLibrary = self::createStorageLibraryForStudio($studio);
        }

        return $storageLibrary;
    }

    /**
     * Create storage library for studio based on active subscription
     */
    public static function createStorageLibraryForStudio(Studio $studio): StorageLibrary
    {
        $user = $studio->user;
        $activeSubscription = $user->activeSubscription;

        if (!$activeSubscription) {
            throw new Exception('الاستوديو ليس لديه اشتراك نشط');
        }

        $plan = $activeSubscription->plan;
        if (!$plan) {
            throw new Exception('خطة الاشتراك غير موجودة');
        }

        // Get storage quota from plan (in MB), convert to bytes
        $storageQuotaMB = $plan->storage_quota ?? 1024; // Default 1GB
        $storageLimitBytes = $storageQuotaMB * 1024 * 1024;

        // Create storage library
        $storageLibrary = StorageLibrary::create([
            'name' => 'مكتبة ' . $studio->name,
            'description' => 'مكتبة التخزين الرئيسية للاستوديو',
            'studio_id' => $studio->studio_id,
            'storage_limit' => $storageLimitBytes,
            'used_storage' => 0,
        ]);

        return $storageLibrary;
    }
}
