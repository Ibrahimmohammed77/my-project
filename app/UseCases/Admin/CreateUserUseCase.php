<?php

namespace App\UseCases\Admin;

use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Studio;
use App\Models\School;
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

                // Handle Profile Creation based on Role/Type
                $typeCode = $user->type->code ?? null;
                
                if ($typeCode === 'STUDIO_OWNER') {
                    Studio::create([
                        'user_id' => $user->id,
                        'description' => $user->name,
                    ]);
                } elseif ($typeCode === 'SCHOOL_OWNER') {
                    School::create([
                        'user_id' => $user->id,
                        'description' => $user->name,
                        'school_type_id' => $data['school_type_id'] ?? null,
                        'school_level_id' => $data['school_level_id'] ?? null,
                    ]);
                }

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
