<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Models\ActivityLog;

class InvoiceObserver
{
    public function created(Invoice $invoice): void
    {
        ActivityLog::logActivity(
            $invoice->user_id,
            'create',
            'invoice',
            $invoice->id,
            ['amount' => $invoice->total_amount]
        );
    }

    public function updated(Invoice $invoice): void
    {
        if ($invoice->isDirty('status_id')) {
            ActivityLog::logActivity(
                $invoice->user_id,
                'status_change',
                'invoice',
                $invoice->id,
                ['old_status' => $invoice->getOriginal('status_id'), 'new_status' => $invoice->status_id]
            );
        }
    }
}
