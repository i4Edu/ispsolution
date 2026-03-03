<?php

namespace App\Services;

class PaymentProcessingService
{
    /**
     * Process a payment for an invoice.
     * @param array $payload
     * @return array
     */
    public function processPayment(array $payload): array
    {
        // Integrate with gateway SDKs / webhooks here.
        return ['status' => 'queued'];
    }
}
<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;

class PaymentProcessingService
{
    /**
     * Process a payment for an invoice.
     */
    public function processInvoicePayment(Invoice $invoice, array $data): Payment
    {
        $payment = Payment::create([
            'user_id' => $invoice->user_id,
            'invoice_id' => $invoice->id,
            'amount' => $data['amount'] ?? $invoice->amount,
            'payment_gateway_id' => $data['gateway_id'] ?? null,
            'transaction_id' => $data['transaction_id'] ?? null,
            'status' => $data['status'] ?? 'completed',
        ]);

        if ($payment->status === 'completed') {
            $invoice->update(['status' => 'paid', 'paid_at' => now()]);
        }

        return $payment;
    }
}
