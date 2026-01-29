<?php

namespace App\UseCases\Admin;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Models\User;
use App\Models\ActivityLog;

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
