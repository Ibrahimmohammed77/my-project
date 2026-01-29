<?php

namespace App\UseCases\Admin;

use App\Models\User;
use App\Models\ActivityLog;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateUserUseCase
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Execute the use case.
     */
    public function execute(array $data): User
    {
        return DB::transaction(function () use ($data) {
            try {
                $user = $this->userRepository->storeByAdmin($data);

                // Log activity
                ActivityLog::logCurrent(
                    'user_created',
                    User::class,
                    $user->id,
                    [
                        'username' => $user->username,
                        'email' => $user->email,
                        'roles' => $user->roles->pluck('name')->toArray(),
                    ]
                );

                return $user;
            } catch (\Exception $e) {
                Log::error('Error in CreateUserUseCase: ' . $e->getMessage());
                throw $e;
            }
        });
    }
}
