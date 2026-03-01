<?php

namespace App\Services;

use App\Models\User;
use App\Models\Invoice;

class BillingService
{
    /**
     * Calculate charge for a user for a billing period.
     */
    public function calculateCharge(User $user, array $options = []): float
    {
        $package = $user->servicePackage;
        if (!$package) return 0.0;

        // Simple calculation: package price (real implementations consider prorate, discounts, taxes)
        return (float) ($package->price ?? 0.0);
    }

    /**
     * Generate an invoice record for a user.
     */
    public function generateInvoiceForUser(User $user, array $meta = []): Invoice
    {
        $amount = $this->calculateCharge($user);

        $invoice = Invoice::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'status' => 'pending',
            'due_date' => now()->addDays(7),
        ] + $meta);

        return $invoice;
    }
}
