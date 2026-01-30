<?php

namespace App\UseCases\Admin;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Studio;
use App\Models\School;

class UpdateUserUseCase
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Execute the use case.
     */
    public function execute(User $user, array $data): User
    {
        // Update user
        $updatedUser = $this->userRepository->updateByAdmin($user, $data);

        // Handle Profile Update/Creation
        $typeCode = $updatedUser->type->code ?? null;

        if ($typeCode === 'STUDIO_OWNER') {
            Studio::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'description' => $data['description'] ?? $user->name,
                    'city' => $data['city'] ?? ($user->studio->city ?? null),
                    'address' => $data['address'] ?? ($user->studio->address ?? null),
                ]
            );
        } elseif ($typeCode === 'SCHOOL_OWNER') {
            School::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'description' => $data['description'] ?? $user->name,
                    'school_type_id' => $data['school_type_id'] ?? ($user->school->school_type_id ?? null),
                    'school_level_id' => $data['school_level_id'] ?? ($user->school->school_level_id ?? null),
                    'city' => $data['city'] ?? ($user->school->city ?? null),
                    'address' => $data['address'] ?? ($user->school->address ?? null),
                ]
            );
        }

        // Log activity
        ActivityLog::logCurrent(
            'user_updated',
            User::class,
            $updatedUser->id,
            [
                'username' => $updatedUser->username,
                'email' => $updatedUser->email,
                'roles' => $updatedUser->roles->pluck('name')->toArray(),
                'changes' => array_keys($data) // Log which fields were changed
            ]
        );

        return $updatedUser;
    }
}
