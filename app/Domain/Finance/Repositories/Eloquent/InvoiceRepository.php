<?php

namespace App\Domain\Finance\Repositories\Eloquent;

use App\Domain\Shared\Repositories\Eloquent\BaseRepository;
use App\Domain\Finance\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Domain\Finance\Models\\Invoice;

class InvoiceRepository extends BaseRepository implements InvoiceRepositoryInterface
{
    public function __construct(Invoice \Invoice)
    {
        parent::__construct(\Invoice);
    }
}
