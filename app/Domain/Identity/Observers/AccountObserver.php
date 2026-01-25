<?php

namespace App\Domain\Identity\Observers;

use App\Domain\Identity\Models\Account;
use App\Domain\Shared\Models\ActivityLog;

class AccountObserver
{
    /**
     * Handle the Account "created" event.
     */
    public function created(Account $account): void
    {
        ActivityLog::create([
            'account_id' => $account->account_id,
           'action' => 'ACCOUNT_CREATED',
            'resource_type' => 'accounts',
            'resource_id' => $account->account_id,
            'metadata' => ['email' => $account->email],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}


