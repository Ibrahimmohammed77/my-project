<?php

namespace App\Domain\Core\Repositories\Eloquent;

use App\Domain\Shared\Repositories\Eloquent\BaseRepository;
use App\Domain\Core\Repositories\Contracts\CustomerRepositoryInterface;
use App\Domain\Core\Models\\Customer;

class CustomerRepository extends BaseRepository implements CustomerRepositoryInterface
{
    public function __construct(Customer \Customer)
    {
        parent::__construct(\Customer);
    }
}
