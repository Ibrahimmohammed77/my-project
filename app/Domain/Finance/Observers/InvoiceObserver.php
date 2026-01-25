<?php

namespace App\Domain\Finance\Observers;

use App\Domain\Finance\Models\Invoice;
use Illuminate\Support\Str;

class InvoiceObserver
{
    /**
     * Handle the Invoice "creating" event.
     */
    public function creating(Invoice $invoice): void
    {
        if (empty($invoice->invoice_number)) {
            // Format: INV-YYYYMM-SEQUENCE (using random string for sequence mostly to avoid concurrency issues without locking, or just uniqid)
            // For simplicity in this demo: INV-{Timestamp}-{Random}
            $invoice->invoice_number = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        }
    }
}


