<?php
// app/Mail/TransactionConfirmation.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Transaction;

class TransactionConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $transaction;
    public $snapToken;

    public function __construct(Transaction $transaction, $snapToken)
    {
        $this->transaction = $transaction;
        $this->snapToken = $snapToken;
    }

    public function build()
    {
        $paymentUrl = config('app.midtrans.is_production')
            ? "https://app.midtrans.com/snap/v2/vtweb/{$this->snapToken}"
            : "https://app.sandbox.midtrans.com/snap/v2/vtweb/{$this->snapToken}";

        return $this->subject('Transaction Confirmation - Order #' . $this->transaction->order_id)
                    ->view('emails.transaction-confirmation')
                    ->with([
                        'orderId' => $this->transaction->order_id,
                        'name' => $this->transaction->name,
                        'total' => $this->transaction->total,
                        'paymentUrl' => $paymentUrl
                    ]);
    }
}
