<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\School;
use App\Models\Card;
use App\Models\Role;
use App\Models\LookupValue;
use Illuminate\Support\Str;

class SchoolSeeder extends Seeder
{
    public function run()
    {
        $schoolRole = Role::where('name', 'school_owner')->first();
        $studentRole = Role::where('name', 'student')->first(); // Ensure this role exists!

        $primaryLevel = LookupValue::where('code', 'PRIMARY')
            ->whereHas('master', fn($q) => $q->where('code', 'SCHOOL_LEVEL'))
            ->value('lookup_value_id');
            
        $activeCardStatus = LookupValue::where('code', 'ACTIVE')
            ->whereHas('master', fn($q) => $q->where('code', 'CARD_STATUS'))
            ->value('lookup_value_id');

        // Create 2 Schools
        for ($s = 1; $s <= 2; $s++) {
            $schoolUser = User::factory()->schoolOwner()->create([
                'email' => "school{$s}@example.com",
                'name' => "School Owner {$s}",
            ]);

            if ($schoolRole) {
                $schoolUser->roles()->syncWithoutDetaching([$schoolRole->role_id]);
            }

            $school = School::create([
                'user_id' => $schoolUser->id,
                'description' => "School {$s}",
                'address' => "School Address {$s}",
                'city' => "Jeddah",
                'school_level_id' => $primaryLevel
            ]);

            // Create Students (Users + Cards)
            for ($st = 1; $st <= 20; $st++) {
                $studentUser = User::factory()->create([ // Default user type, maybe change if student type exists
                    'name' => "Student {$s}-{$st}",
                    'email' => "student{$s}_{$st}@school.com"
                ]);
                
                // If we had a student role/type
                // $studentUser->user_type_id = ...
                // $studentUser->save();

                // Create Card linking Student to School
                Card::create([
                    'card_number' => "SCH{$s}-ST{$st}",
                    'card_uuid' => Str::uuid(),
                    'owner_type' => School::class,
                    'owner_id' => $school->school_id,
                    'holder_id' => $studentUser->id,
                    'card_type_id' => $primaryLevel = LookupValue::where('code', 'STANDARD') // Reusing standard card type lookup
                        ->whereHas('master', fn($q) => $q->where('code', 'CARD_TYPE'))
                        ->value('lookup_value_id'),
                    'card_status_id' => $activeCardStatus,
                    'expiry_date' => now()->addYear()
                ]);
            }
        }
        
        $this->command->info('Schools seeded successfully.');
    }
}
