<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{User, Studio, School, Plan, Album, Card, Photo, Subscription, StorageLibrary, StorageAccount, LookupValue, CardGroup, Customer};
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    private $lookups = [];
    private $plans = [];
    
    public function run(): void
    {
        $this->command->info('🚀 Starting comprehensive test data seeding...');
        
        // Load lookups for reference
        $this->loadLookups();
        $this->loadPlans();
        
        // Create test users for each role
        $admin = $this->createAdmin();
        $this->command->info('✅ Created Admin User');
        
        $studios = $this->createStudios();
        $this->command->info('✅ Created 3 Studios');
        
        $schools = $this->createSchools($studios);
        $this->command->info('✅ Created 3 Schools');
        
        $customers = $this->createCustomers();
        $this->command->info('✅ Created 10 Customers');
        
        // Create albums for studios and schools
        $studioAlbums = $this->createStudioAlbums($studios);
        $this->command->info('✅ Created Studio Albums');
        
        $schoolAlbums = $this->createSchoolAlbums($schools);
        $this->command->info('✅ Created School Albums');
        
        // Create cards
        $studioCards = $this->createStudioCards($studios);
        $this->command->info('✅ Created Studio Cards');
        
        $schoolCards = $this->createSchoolCards($schools);
        $this->command->info('✅ Created School Cards');
        
        // Link albums to cards
        $this->linkAlbumsToCards($studioAlbums, $studioCards);
        $this->linkAlbumsToCards($schoolAlbums, $schoolCards);
        $this->command->info('✅ Linked Albums to Cards');
        
        // Create photos
        $this->createPhotos($studioAlbums);
        $this->createPhotos($schoolAlbums);
        $this->command->info('✅ Created Photos');
        
        // Activate some cards for customers
        $this->activateCards($studioCards, $customers);
        $this->activateCards($schoolCards, $customers);
        $this->command->info('✅ Activated Cards for Customers');
        
        $this->command->info('🎉 Test data seeding completed successfully!');
        $this->printCredentials();
    }
    
    private function loadLookups(): void
    {
        $types = ['USER_STATUS', 'USER_TYPE', 'SUBSCRIPTION_STATUS', 'CARD_STATUS', 'CARD_TYPE', 'SCHOOL_TYPE', 'SCHOOL_LEVEL', 'GENDER'];
        foreach ($types as $type) {
            $this->lookups[$type] = LookupValue::whereHas('master', function($q) use ($type) {
                $q->where('code', $type);
            })->get()->keyBy('code');
        }
    }
    
    private function loadPlans(): void
    {
        $this->plans = Plan::active()->get()->keyBy('name');
    }
    
    private function createAdmin(): User
    {
        $activeStatus = $this->lookups['USER_STATUS']['ACTIVE'] ?? null;
        
        return User::updateOrCreate(
            ['email' => 'admin@albums.test'],
            [
                'name' => 'System Administrator',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'phone' => '0000000000',
                'phone_verified_at' => now(),
                'user_status_id' => $activeStatus?->lookup_value_id,
                'phone_verified_at' => now(),
                'user_status_id' => $activeStatus?->lookup_value_id,
                'is_active' => true,
            ]
        );

        $user->assignRole('admin');
        
        return $user;
    }
    
    private function createStudios(): array
    {
        $studios = [];
        $activeStatus = $this->lookups['USER_STATUS']['ACTIVE'] ?? null;
        $plans = ['الباقة الأساسية', 'الباقة الاحترافية', 'باقة المؤسسات'];
        
        foreach (range(1, 3) as $i) {
            // Create studio owner user
            $user = User::updateOrCreate(
                ['email' => "studio{$i}@test.com"],
                [
                    'name' => "Studio Owner {$i}",
                    'username' => "studio_owner_{$i}",
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'phone' => '0771234567' . $i,
                    'user_status_id' => $activeStatus?->lookup_value_id,
                    'is_active' => true,
                ]
            );
            
            $user->assignRole('studio_owner');
            
            // Create studio
            $studio = Studio::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'address' => "Studio Address {$i}, Test City",
                    'description' => "Professional photography studio number {$i}",
                    'city' => 'Test City',
                ]
            );
            

            
            // Create storage library for studio
            $library = StorageLibrary::updateOrCreate(
                ['name' => "Studio {$i} Main Library"],
                [
                    'studio_id' => $studio->studio_id,
                    'user_id' => $user->id,
                    'storage_limit' => ($this->plans[$plans[$i-1]]?->storage_limit ?? 10737418240),
                ]
            );
            
            // Create storage account
            StorageAccount::updateOrCreate(
                [
                    'owner_type' => User::class,
                    'owner_id' => $user->id,
                ],
                [
                    'total_space' => ($this->plans[$plans[$i-1]]?->storage_limit ?? 10737418240),
                    'used_space' => 0,
                    'status' => 'active',
                ]
            );
            
            // Create subscription
            $plan = $this->plans[$plans[$i-1]] ?? null;
            if ($plan) {
                $activeSubStatus = $this->lookups['SUBSCRIPTION_STATUS']['ACTIVE'] ?? null;
                Subscription::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'plan_id' => $plan->plan_id,
                        'start_date' => now()->subDays(30),
                        'end_date' => $i <= 2 ? now()->addMonths(11) : now()->subDays(5), // Last one expired
                        'renewal_date' => $i <= 2 ? now()->addMonths(11)->subDays(7) : null,
                        'auto_renew' => $i <= 2,
                        'subscription_status_id' => $i <= 2 ? $activeSubStatus?->lookup_value_id : ($this->lookups['SUBSCRIPTION_STATUS']['EXPIRED']?->lookup_value_id),
                    ]
                );
            }
            
            $studios[] = [
                'studio' => $studio,
                'user' => $user,
                'library' => $library,
            ];
        }
        
        return $studios;
    }
    
    private function createSchools(array $studios): array
    {
        $schools = [];
        $activeStatus = $this->lookups['USER_STATUS']['ACTIVE'] ?? null;
        $schoolTypes = ['PUBLIC', 'PRIVATE', 'INTERNATIONAL'];
        $levels = ['PRIMARY', 'MIDDLE', 'HIGH'];
        
        foreach (range(1, 3) as $i) {
            // Create school owner user
            $user = User::updateOrCreate(
                ['email' => "school{$i}@test.com"],
                [
                    'name' => "School Admin {$i}",
                    'username' => "school_admin_{$i}",
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'phone' => '0779876543' . $i,
                    'user_status_id' => $activeStatus?->lookup_value_id,
                    'is_active' => true,
                ]
            );

            $user->assignRole('school_owner');
            
            // Create school
            $studioIndex = ($i - 1) % count($studios);
            $school = School::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'address' => "School Address {$i}, Test City",
                    'city' => 'Test City',
                    'description' => "Test School {$i}",
                    'school_type_id' => $this->lookups['SCHOOL_TYPE'][$schoolTypes[$i-1]]?->lookup_value_id,
                    'school_level_id' => $this->lookups['SCHOOL_LEVEL'][$levels[$i-1]]?->lookup_value_id,
                ]
            );
            

            
            // Create storage library for school (allocated from studio)
            $library = StorageLibrary::updateOrCreate(
                ['name' => "School {$i} Library"],
                [
                    'school_id' => $school->school_id,
                    'studio_id' => $studios[$studioIndex]['studio']->studio_id,
                    'storage_limit' => 5 * 1024 * 1024 * 1024, // 5GB per school
                ]
            );
            
            $schools[] = [
                'school' => $school,
                'user' => $user,
                'library' => $library,
                'studio' => $studios[$studioIndex],
            ];
        }
        
        return $schools;
    }
    
    private function createCustomers(): array
    {
        $customers = [];
        $activeStatus = $this->lookups['USER_STATUS']['ACTIVE'] ?? null;
        
        foreach (range(1, 10) as $i) {
            $user = User::updateOrCreate(
                ['email' => "customer{$i}@test.com"],
                [
                    'name' => "Customer {$i}",
                    'username' => "customer{$i}",
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'phone' => '0775555000' . $i,
                    'user_status_id' => $activeStatus?->lookup_value_id,
                    'is_active' => true,
                ]
            );

            $user->assignRole('customer');
            
            // Create customer record
            $customer = Customer::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => "Customer",
                    'last_name' => "{$i}",
                    'date_of_birth' => now()->subYears(rand(18, 50)),
                ]
            );
            
            $customers[] = ['user' => $user, 'customer' => $customer];
        }
        
        return $customers;
    }
    
    private function createStudioAlbums(array $studios): array
    {
        $albums = [];
        
        foreach ($studios as $key => $studioData) {
            $studio = $studioData['studio'];
            $user = $studioData['user'];
            $library = $studioData['library'];
            
            $albumTypes = ['Graduation', 'Events', 'Portraits', 'Sports', 'Annual'];
            
            foreach (range(0, 4) as $i) {
                $album = Album::create([
                    'owner_type' => Studio::class,
                    'owner_id' => $studio->studio_id,
                    'storage_library_id' => $library->storage_library_id,
                    'name' => "Studio {$key} - {$albumTypes[$i]} " . date('Y'),
                    'description' => "Collection of {$albumTypes[$i]} photos",
                    'is_default' => $i === 0,
                    'is_visible' => true,
                    'view_count' => rand(10, 500),
                    'settings' => ['watermark' => true, 'download_enabled' => true],
                ]);
                
                $albums[] = $album;
            }
        }
        
        return $albums;
    }
    
    private function createSchoolAlbums(array $schools): array
    {
        $albums = [];
        
        foreach ($schools as $key => $schoolData) {
            $school = $schoolData['school'];
            $user = $schoolData['user'];
            $library = $schoolData['library'];
            
            $albumTypes = ['Class Photos', 'Graduation', 'School Events'];
            
            foreach ($albumTypes as $i => $type) {
                $album = Album::create([
                    'owner_type' => School::class,
                    'owner_id' => $school->school_id,
                    'storage_library_id' => $library->storage_library_id,
                    'name' => "School {$key} - {$type}",
                    'description' => "{$type} collection for the academic year",
                    'is_default' => $i === 0,
                    'is_visible' => true,
                    'view_count' => rand(50, 1000),
                    'settings' => ['watermark' => true, 'download_enabled' => false],
                ]);
                
                $albums[] = $album;
            }
        }
        
        return $albums;
    }
    
    private function createStudioCards(array $studios): array
    {
        $cards = [];
        $activeCardStatus = $this->lookups['CARD_STATUS']['ACTIVE'] ?? null;
        $standardCardType = $this->lookups['CARD_TYPE']['STANDARD'] ?? null;
        
        foreach ($studios as $studioData) {
            $studio = $studioData['studio'];
            
            // Create card group
            $group = CardGroup::updateOrCreate(
                ['name' => "Studio {$studio->studio_id} - Batch 2026"],
                [
                    'description' => "Main card batch for 2026",
                ]
            );
            
            foreach (range(1, 10) as $i) {
                $card = Card::create([
                    'card_uuid' => Str::uuid(),
                    'card_group_id' => $group->id,
                    'owner_type' => Studio::class,
                    'owner_id' => $studio->studio_id,
                    'card_type_id' => $standardCardType?->lookup_value_id,
                    'card_status_id' => $activeCardStatus?->lookup_value_id,
                    'expiry_date' => now()->addYear(),
                    'notes' => "Studio card #{$i}",
                ]);
                
                $cards[] = $card;
            }
        }
        
        return $cards;
    }
    
    private function createSchoolCards(array $schools): array
    {
        $cards = [];
        $activeCardStatus = $this->lookups['CARD_STATUS']['ACTIVE'] ?? null;
        $standardCardType = $this->lookups['CARD_TYPE']['STANDARD'] ?? null;
        
        foreach ($schools as $schoolData) {
            $school = $schoolData['school'];
            
            // Create card group
            $group = CardGroup::updateOrCreate(
                ['name' => "School {$school->school_id} - Class 2026"],
                [
                    'description' => "Student cards for academic year 2026",
                ]
            );
            
            foreach (range(1, 30) as $i) {
                $card = Card::create([
                    'card_uuid' => Str::uuid(),
                    'card_group_id' => $group->id,
                    'owner_type' => School::class,
                    'owner_id' => $school->school_id,
                    'card_type_id' => $standardCardType?->lookup_value_id,
                    'card_status_id' => $activeCardStatus?->lookup_value_id,
                    'expiry_date' => now()->addMonths(6),
                    'notes' => "Student card #{$i}",
                ]);
                
                $cards[] = $card;
            }
        }
        
        return $cards;
    }
    
    private function linkAlbumsToCards(array $albums, array $cards): void
    {
        // Link random albums to cards
        foreach ($cards as $card) {
            $randomAlbums = collect($albums)->random(min(count($albums), rand(1, 3)));
            foreach ($randomAlbums as $album) {
                $card->albums()->syncWithoutDetaching($album->album_id);
            }
        }
    }
    
    private function createPhotos(array $albums): void
    {
        foreach ($albums as $album) {
            $photoCount = rand(10, 30);
            
            foreach (range(1, $photoCount) as $i) {
                Photo::create([
                    'album_id' => $album->album_id,
                    'original_name' => "photo_{$album->album_id}_{$i}.jpg",
                    'stored_name' => Str::uuid() . ".jpg",
                    'file_path' => "albums/{$album->album_id}/photo_{$i}.jpg",
                    'file_size' => rand(500000, 5000000),
                    'mime_type' => 'image/jpeg',
                    'width' => rand(1920, 4000),
                    'height' => rand(1080, 3000),
                ]);
            }
        }
    }
    
    private function activateCards(array $cards, array $customers): void
    {
        // Activate 50% of cards and assign to random customers
        $cardsToActivate = collect($cards)->random(count($cards) / 2);
        
        foreach ($cardsToActivate as $card) {
            $randomCustomer = collect($customers)->random();
            $card->update([
                'holder_id' => $randomCustomer['user']->id,
                'activation_date' => now()->subDays(rand(1, 60)),
                'last_used' => now()->subDays(rand(0, 30)),
            ]);
        }
    }
    
    private function printCredentials(): void
    {
        $this->command->info("\n" . str_repeat('=', 60));
        $this->command->info("📋 TEST CREDENTIALS");
        $this->command->info(str_repeat('=', 60));
        $this->command->info("Admin:        admin@albums.test / password");
        $this->command->info("Studio 1:     studio1@test.com / password");
        $this->command->info("Studio 2:     studio2@test.com / password");
        $this->command->info("Studio 3:     studio3@test.com / password (expired subscription)");
        $this->command->info("School 1:     school1@test.com / password");
        $this->command->info("School 2:     school2@test.com / password");
        $this->command->info("School 3:     school3@test.com / password");
        $this->command->info("Customers:    customer1@test.com to customer10@test.com / password");
        $this->command->info(str_repeat('=', 60) . "\n");
    }
}
