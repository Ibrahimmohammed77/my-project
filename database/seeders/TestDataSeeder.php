<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Studio;
use App\Models\School;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\StorageLibrary;
use App\Models\Album;
use App\Models\Photo;
use App\Models\CardGroup;
use App\Models\Card;
use App\Models\Customer;
use App\Models\LookupValue;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        $activeSubscriptionStatus = LookupValue::where('code', 'ACTIVE')
            ->whereHas('master', function($q){ $q->where('code', 'SUBSCRIPTION_STATUS'); })
            ->first()->lookup_value_id;

        $standardCardType = LookupValue::where('code', 'STANDARD')
            ->whereHas('master', function($q){ $q->where('code', 'CARD_TYPE'); })
            ->first()->lookup_value_id;

        $activeCardStatus = LookupValue::where('code', 'ACTIVE')
            ->whereHas('master', function($q){ $q->where('code', 'CARD_STATUS'); })
            ->first()->lookup_value_id;

        $primaryLevel = LookupValue::where('code', 'PRIMARY')
            ->whereHas('master', function($q){ $q->where('code', 'SCHOOL_LEVEL'); })
            ->first()->lookup_value_id;

        $privateType = LookupValue::where('code', 'PRIVATE')
            ->whereHas('master', function($q){ $q->where('code', 'SCHOOL_TYPE'); })
            ->first()->lookup_value_id;

        $proPlan = Plan::where('name', 'الباقة الاحترافية')->first();

        // 1. Setup Studio Owner
        $studioOwner = User::where('email', 'studio@example.com')->first();
        if ($studioOwner) {
            $studio = Studio::updateOrCreate(
                ['user_id' => $studioOwner->id],
                [
                    'description' => 'استوديو الأمل للتصوير الاحترافي',
                    'address' => 'شارع التخصصي، الرياض',
                    'city' => 'الرياض',
                    'settings' => ['theme' => 'light']
                ]
            );

            Subscription::updateOrCreate(
                ['user_id' => $studioOwner->id],
                [
                    'plan_id' => $proPlan->plan_id,
                    'start_date' => now(),
                    'end_date' => $endDate = now()->addYear(),
                    'renewal_date' => $endDate->copy()->subDays(7),
                    'auto_renew' => true,
                    'subscription_status_id' => $activeSubscriptionStatus
                ]
            );

            // Create Storage Libraries
            $library1 = StorageLibrary::updateOrCreate(
                ['studio_id' => $studio->studio_id, 'name' => 'المكتبة السحابية 1'],
                [
                    'user_id' => $studioOwner->id,
                    'description' => 'مساحة تخزين مخصصة للألبومات المدرسية',
                    'storage_limit' => 50 * 1024 * 1024 * 1024 // 50GB
                ]
            );

            $library2 = StorageLibrary::updateOrCreate(
                ['studio_id' => $studio->studio_id, 'name' => 'المكتبة السحابية 2'],
                [
                    'user_id' => $studioOwner->id,
                    'description' => 'مساحة تخزين للمناسبات الخاصة',
                    'storage_limit' => 30 * 1024 * 1024 * 1024 // 30GB
                ]
            );

            // Create Albums
            $album1 = Album::updateOrCreate(
                ['storage_library_id' => $library1->storage_library_id, 'name' => 'تخرج مدارس الرياض 2024'],
                [
                    'owner_type' => Studio::class,
                    'owner_id' => $studio->studio_id,
                    'description' => 'صور التخرج للمرحلة الثانوية',
                    'is_visible' => true,
                    'is_default' => true
                ]
            );

            $album2 = Album::updateOrCreate(
                ['storage_library_id' => $library1->storage_library_id, 'name' => 'حفل التكريم السنوي'],
                [
                    'owner_type' => Studio::class,
                    'owner_id' => $studio->studio_id,
                    'description' => 'صور حفل تكريم الطلاب المتفوقين',
                    'is_visible' => true,
                    'is_default' => false
                ]
            );

            // Add Photos to Album 1
            for ($i = 1; $i <= 15; $i++) {
                Photo::updateOrCreate(
                    ['album_id' => $album1->album_id, 'original_name' => "IMG_{$i}.JPG"],
                    [
                        'stored_name' => Str::random(20) . ".jpg",
                        'file_path' => "photos/demo/IMG_{$i}.jpg",
                        'file_size' => rand(2 * 1024 * 1024, 8 * 1024 * 1024),
                        'mime_type' => 'image/jpeg',
                        'width' => 4000,
                        'height' => 3000,
                        'review_status' => ($i <= 10) ? Photo::STATUS_APPROVED : Photo::STATUS_PENDING
                    ]
                );
            }

            // Add Photos to Album 2
            for ($i = 1; $i <= 8; $i++) {
                Photo::updateOrCreate(
                    ['album_id' => $album2->album_id, 'original_name' => "EVENT_{$i}.JPG"],
                    [
                        'stored_name' => Str::random(20) . ".jpg",
                        'file_path' => "photos/events/EVENT_{$i}.jpg",
                        'file_size' => rand(3 * 1024 * 1024, 10 * 1024 * 1024),
                        'mime_type' => 'image/jpeg',
                        'width' => 5000,
                        'height' => 3500,
                        'review_status' => Photo::STATUS_APPROVED
                    ]
                );
            }

            // Create Card Groups
            $group1 = CardGroup::updateOrCreate(
                ['name' => 'مجموعة تخرج 2024'],
                [
                    'description' => 'كروت الوصول لصور التخرج',
                    'sub_card_available' => 50,
                    'sub_card_used' => 20
                ]
            );

            $group2 = CardGroup::updateOrCreate(
                ['name' => 'مجموعة حفل التكريم'],
                [
                    'description' => 'كروت الوصول لصور حفل التكريم',
                    'sub_card_available' => 30,
                    'sub_card_used' => 10
                ]
            );

            // Create cards for group 1 and link to album 1
            for ($i = 1; $i <= 20; $i++) {
                $card = Card::updateOrCreate(
                    ['card_number' => "GR1-" . str_pad($i, 4, '0', STR_PAD_LEFT)],
                    [
                        'card_uuid' => Str::uuid(),
                        'card_group_id' => $group1->group_id,
                        'owner_type' => Studio::class,
                        'owner_id' => $studio->studio_id,
                        'card_type_id' => $standardCardType,
                        'card_status_id' => $activeCardStatus,
                        'expiry_date' => now()->addYear()
                    ]
                );
                
                $card->albums()->syncWithoutDetaching([$album1->album_id]);
            }

            // Create cards for group 2 and link to album 2
            for ($i = 1; $i <= 10; $i++) {
                $card = Card::updateOrCreate(
                    ['card_number' => "GR2-" . str_pad($i, 4, '0', STR_PAD_LEFT)],
                    [
                        'card_uuid' => Str::uuid(),
                        'card_group_id' => $group2->group_id,
                        'owner_type' => Studio::class,
                        'owner_id' => $studio->studio_id,
                        'card_type_id' => $standardCardType,
                        'card_status_id' => $activeCardStatus,
                        'expiry_date' => now()->addMonths(6)
                    ]
                );
                
                $card->albums()->syncWithoutDetaching([$album2->album_id]);
            }

            // Create Customers
            for ($i = 1; $i <= 5; $i++) {
                $customerUser = User::updateOrCreate(
                    ['email' => "customer{$i}@example.com"],
                    [
                        'name' => "عميل رقم {$i}",
                        'username' => "customer{$i}",
                        'password' => bcrypt('password'),
                        'phone' => '+96650000000' . $i,
                        'user_status_id' => LookupValue::where('code', 'ACTIVE')
                            ->whereHas('master', function($q){ $q->where('code', 'USER_STATUS'); })
                            ->first()->lookup_value_id,
                        'user_type_id' => LookupValue::where('code', 'CUSTOMER')
                            ->whereHas('master', function($q){ $q->where('code', 'USER_TYPE'); })
                            ->first()->lookup_value_id,
                        'is_active' => true,
                        'email_verified' => true,
                    ]
                );

                Customer::updateOrCreate(
                    ['user_id' => $customerUser->id],
                    [
                        'studio_id' => $studio->studio_id,
                        'full_name' => "عميل رقم {$i}",
                        'notes' => "عميل تجريبي للاختبار"
                    ]
                );
            }
        }

        // 2. Setup School Owner
        $schoolOwner = User::where('email', 'school@example.com')->first();
        if ($schoolOwner) {
            $school = School::updateOrCreate(
                ['user_id' => $schoolOwner->id],
                [
                    'description' => 'مدارس النخبة الأهلية - القسم الابتدائي',
                    'school_type_id' => $privateType,
                    'school_level_id' => $primaryLevel,
                    'address' => 'حي النزهة، جدة',
                    'city' => 'جدة'
                ]
            );
        }
    }
}
