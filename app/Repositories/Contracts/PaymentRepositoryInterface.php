<?php

namespace App\Repositories\Contracts;

use App\Models\Payment;
use Illuminate\Support\Collection;

interface PaymentRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Payment;
    public function create(array $data): Payment;
    public function update(Payment $payment, array $data): bool;
    public function delete(Payment $payment): bool;
    public function getForUser(int $userId): Collection;
    public function getForInvoice(int $invoiceId): Collection;
}
