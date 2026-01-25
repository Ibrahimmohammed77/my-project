<?php

namespace App\Domain\Finance\Repositories\Eloquent;

use App\Domain\Shared\Repositories\Eloquent\BaseRepository;
use App\Domain\Finance\Repositories\Contracts\PaymentRepositoryInterface;
use App\Domain\Finance\Models\Payment;

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    public function __construct(Payment $payment)
    {
        parent::__construct($payment);
    }
}
