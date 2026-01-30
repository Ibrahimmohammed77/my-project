<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Studio;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\StorageLibrary;
use App\Models\Album;
use App\Models\Photo;
use App\Models\CardGroup;
use App\Models\Card;
use App\Models\Customer;
use App\Models\LookupValue;
use App\Models\Role;
use Illuminate\Support\Str;

class StudioSeeder extends Seeder
{
    public function run()
    {
        $activeSubscriptionStatus = LookupValue::where('code', 'ACTIVE')
            ->whereHas('master', fn($q) => $q->where('code', 'SUBSCRIPTION_STATUS'))
            ->value('lookup_value_id');

        $standardCardType = LookupValue::where('code', 'STANDARD')
            ->whereHas('master', fn($q) => $q->where('code', 'CARD_TYPE'))
            ->value('lookup_value_id');

        $activeCardStatus = LookupValue::where('code', 'ACTIVE')
            ->whereHas('master', fn($q) => $q->where('code', 'CARD_STATUS'))
            ->value('lookup_value_id');

        $proPlan = Plan::where('name', 'الباقة الاحترافية')->first();
        $studioRole = Role::where('name', 'studio_owner')->first();

        // Create 2 Studios
        for ($s = 1; $s <= 2; $s++) {
            $studioUser = User::factory()->studioOwner()->create([
                'email' => "studio{$s}@example.com",
                'name' => "Studio Owner {$s}",
            ]);

            if ($studioRole) {
                $studioUser->roles()->syncWithoutDetaching([$studioRole->role_id]);
            }

            $studio = Studio::create([
                'user_id' => $studioUser->id,
                'description' => "Studio {$s} Description",
                'address' => "Studio {$s} Address",
                'city' => "Riyadh",
            ]);

            // Create Subscription
            if ($proPlan) {
                Subscription::create([
                    'user_id' => $studioUser->id,
                    'plan_id' => $proPlan->plan_id,
                    'start_date' => now(),
                    'end_date' => $endDate = now()->addYear(),
                    'renewal_date' => $endDate->copy()->subDays(7),
                    'auto_renew' => true,
                    'subscription_status_id' => $activeSubscriptionStatus
                ]);
            }

            // Create Storage Library
            $library = StorageLibrary::create([
                'studio_id' => $studio->studio_id,
                'user_id' => $studioUser->id,
                'name' => "Library {$s}",
                'storage_limit' => 100 * 1024 * 1024 * 1024 // 100GB
            ]);

            // Create Albums
            for ($a = 1; $a <= 3; $a++) {
                $album = Album::create([
                    'storage_library_id' => $library->storage_library_id,
                    'name' => "Album {$s}-{$a}",
                    'owner_type' => Studio::class,
                    'owner_id' => $studio->studio_id,
                    'description' => "Album Description",
                    'is_visible' => true
                ]);

                // Add Photos
                for ($p = 1; $p <= 5; $p++) {
                    Photo::create([
                        'album_id' => $album->album_id,
                        'original_name' => "photo_{$p}.jpg",
                        'stored_name' => Str::random(20) . ".jpg",
                        'file_path' => "photos/demo/photo.jpg",
                        'file_size' => 1024,
                        'mime_type' => 'image/jpeg',
                        'review_status' => Photo::STATUS_APPROVED
                    ]);
                }

                // Create Card Group & Cards linked to this Album
                $group = CardGroup::create([
                    'name' => "Group {$s}-{$a}",
                    'sub_card_available' => 50,
                    'sub_card_used' => 0
                ]);

                for ($c = 1; $c <= 10; $c++) {
                    $card = Card::create([
                        'card_number' => "S{$s}A{$a}-" . str_pad($c, 4, '0', STR_PAD_LEFT),
                        'card_uuid' => Str::uuid(),
                        'card_group_id' => $group->group_id,
                        'owner_type' => Studio::class,
                        'owner_id' => $studio->studio_id,
                        'card_type_id' => $standardCardType,
                        'card_status_id' => $activeCardStatus,
                        'expiry_date' => now()->addYear()
                    ]);
                    $card->albums()->syncWithoutDetaching([$album->album_id]);
                }
            }
            
            // Create Customers
             for ($c = 1; $c <= 5; $c++) {
                $customerUser = User::factory()->customer()->create([
                     'email' => "customer_{$s}_{$c}@example.com",
                     'name' => "Customer {$s}-{$c}",
                ]);
                
                Customer::create([
                    'user_id' => $customerUser->id,
                    // 'studio_id' => $studio->studio_id, // Column does not exist
                    'first_name' => explode(' ', $customerUser->name)[0],
                    'last_name' => explode(' ', $customerUser->name)[1] ?? 'Customer',
                ]);
             }
        }
        
        $this->command->info('Studios seeded successfully.');
    }
}
