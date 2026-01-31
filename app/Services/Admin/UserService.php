<?php

namespace App\Services\Admin;

use App\Models\{User, ActivityLog};
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\{DB, Log};

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected UserProfileService $profileService
    ) {}

    public function listUsers(array $filters = [], int $perPage = 15)
    {
        return $this->userRepository->listByAdmin($filters, $perPage);
    }

    public function createUser(array $data, bool $isSpecialType = false): User
    {
        return DB::transaction(function () use ($data, $isSpecialType) {
            $user = $this->userRepository->storeByAdmin($data);

            if ($isSpecialType) {
                $this->profileService->createProfile($user, $data);
            }

            $this->logActivity($user, 'user_created');

            return $user;
        });
    }

    public function updateUser(User $user, array $data, bool $isSpecialType = false): User
    {
        $updatedUser = $this->userRepository->updateByAdmin($user, $data);

        if ($isSpecialType) {
            $this->profileService->updateOrCreateProfile($updatedUser, $data);
        }

        $this->logActivity($updatedUser, 'user_updated', ['changes' => array_keys($data)]);

        return $updatedUser;
    }

    public function deleteUser(User $user): void
    {
        DB::transaction(function () use ($user) {
            $this->cleanupUserRelations($user);
            $this->profileService->deleteProfile($user);
            $this->logActivity($user, 'user_deleted');

            $user->delete();
        });
    }

    /**
     * Create a special user (studio or school).
     */
    public function createSpecialUser(array $data, string $type): User
    {
        // Set type based on role
        $role = \App\Models\Role::where('name', $type === 'studio' ? 'studio_owner' : 'school_owner')->first();
        if ($role) {
            $data['role_id'] = $role->role_id;

            // Get user type from role
            $traits = new class { use \App\Traits\MapsRoleToType; };
            $userTypeId = $traits->getUserTypeIdFromRole($role->role_id);
            if ($userTypeId) {
                $data['user_type_id'] = $userTypeId;
            }
        }

        return $this->createUser($data, true);
    }

    /**
     * Update a special user (studio or school).
     */
    public function updateSpecialUser(User $user, array $data): User
    {
        return $this->updateUser($user, $data, true);
    }

    private function cleanupUserRelations(User $user): void
    {
        $user->roles()->detach();

        \App\Models\StorageAccount::where('owner_type', User::class)
            ->where('owner_id', $user->id)
            ->delete();

        \App\Models\Album::where('owner_type', User::class)
            ->where('owner_id', $user->id)
            ->delete();
    }

    private function logActivity(User $user, string $action, array $extra = []): void
    {
        $logData = [
            'username' => $user->username,
            'email' => $user->email,
            ...$extra
        ];

        if (in_array($action, ['user_created', 'user_updated'])) {
            $logData['roles'] = $user->roles->pluck('name')->toArray();
        }

        ActivityLog::logCurrent($action, User::class, $user->id, $logData);
    }
}
