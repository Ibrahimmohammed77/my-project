<?php

namespace App\UseCases\Admin;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ListUsersUseCase
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Execute the use case.
     * 
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function execute(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->userRepository->listByAdmin($filters, $perPage);
    }
}
