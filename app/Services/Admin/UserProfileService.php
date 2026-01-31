<?php

namespace App\Services\Admin;

use App\Models\{User, Studio, School, ActivityLog};
use Illuminate\Support\Facades\{DB, Log};

class UserProfileService
{
    /**
     * Create profile based on user type.
     */
    public function createProfile(User $user, array $data): void
    {
        $typeCode = $user->type->code ?? null;

        switch ($typeCode) {
            case 'STUDIO_OWNER':
                $this->createStudioProfile($user, $data);
                break;

            case 'SCHOOL_OWNER':
                $this->createSchoolProfile($user, $data);
                break;

            default:
                // Regular user - no additional profile needed
                break;
        }
    }

    /**
     * Update or create profile based on user type.
     */
    public function updateOrCreateProfile(User $user, array $data): void
    {
        $typeCode = $user->type->code ?? null;

        switch ($typeCode) {
            case 'STUDIO_OWNER':
                $this->updateOrCreateStudioProfile($user, $data);
                break;

            case 'SCHOOL_OWNER':
                $this->updateOrCreateSchoolProfile($user, $data);
                break;

            default:
                // Regular user - no profile update needed
                break;
        }
    }

    /**
     * Delete profile based on user type.
     */
    public function deleteProfile(User $user): void
    {
        $typeCode = $user->type->code ?? null;

        switch ($typeCode) {
            case 'STUDIO_OWNER':
                Studio::where('user_id', $user->id)->delete();
                break;

            case 'SCHOOL_OWNER':
                School::where('user_id', $user->id)->delete();
                break;

            default:
                // Regular user - no profile to delete
                break;
        }
    }

    /**
     * Get profile data based on user type.
     */
    public function getProfileData(User $user): ?array
    {
        $typeCode = $user->type->code ?? null;

        switch ($typeCode) {
            case 'STUDIO_OWNER':
                return $user->studio ? [
                    'description' => $user->studio->description,
                    'city' => $user->studio->city,
                    'address' => $user->studio->address,
                ] : null;

            case 'SCHOOL_OWNER':
                return $user->school ? [
                    'description' => $user->school->description,
                    'school_type_id' => $user->school->school_type_id,
                    'school_level_id' => $user->school->school_level_id,
                    'city' => $user->school->city,
                    'address' => $user->school->address,
                ] : null;

            default:
                return null;
        }
    }

    /**
     * Validate profile data based on user type.
     */
    public function validateProfileData(array $data, string $typeCode): array
    {
        $rules = [];

        switch ($typeCode) {
            case 'STUDIO_OWNER':
                $rules = [
                    'description' => 'nullable|string|max:255',
                    'city' => 'nullable|string|max:100',
                    'address' => 'nullable|string',
                ];
                break;

            case 'SCHOOL_OWNER':
                $rules = [
                    'description' => 'nullable|string|max:255',
                    'school_type_id' => 'required|exists:lookup_values,lookup_value_id',
                    'school_level_id' => 'required|exists:lookup_values,lookup_value_id',
                    'city' => 'nullable|string|max:100',
                    'address' => 'nullable|string',
                ];
                break;
        }

        return $rules;
    }

    private function createStudioProfile(User $user, array $data): void
    {
        Studio::create([
            'user_id' => $user->id,
            'description' => $data['description'] ?? $user->name,
            'city' => $data['city'] ?? null,
            'address' => $data['address'] ?? null,
        ]);
    }

    private function createSchoolProfile(User $user, array $data): void
    {
        School::create([
            'user_id' => $user->id,
            'description' => $data['description'] ?? $user->name,
            'school_type_id' => $data['school_type_id'] ?? null,
            'school_level_id' => $data['school_level_id'] ?? null,
            'city' => $data['city'] ?? null,
            'address' => $data['address'] ?? null,
        ]);
    }

    private function updateOrCreateStudioProfile(User $user, array $data): void
    {
        Studio::updateOrCreate(
            ['user_id' => $user->id],
            [
                'description' => $data['description'] ?? $user->studio->description ?? $user->name,
                'city' => $data['city'] ?? $user->studio->city ?? null,
                'address' => $data['address'] ?? $user->studio->address ?? null,
            ]
        );
    }

    private function updateOrCreateSchoolProfile(User $user, array $data): void
    {
        School::updateOrCreate(
            ['user_id' => $user->id],
            [
                'description' => $data['description'] ?? $user->school->description ?? $user->name,
                'school_type_id' => $data['school_type_id'] ?? $user->school->school_type_id ?? null,
                'school_level_id' => $data['school_level_id'] ?? $user->school->school_level_id ?? null,
                'city' => $data['city'] ?? $user->school->city ?? null,
                'address' => $data['address'] ?? $user->school->address ?? null,
            ]
        );
    }
}
