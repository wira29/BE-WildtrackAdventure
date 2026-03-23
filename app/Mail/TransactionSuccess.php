<?php

namespace App\Mail;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionSuccess extends Mailable
{
    use Queueable, SerializesModels;

    public $transaction;
    public $notification;

    public function __construct(Transaction $transaction, $notification)
    {
        $this->transaction = $transaction;
        $this->notification = $notification;
    }

    public function build()
    {

        return $this->subject('Receipt #' . $this->transaction->order_id . ' - Wildtrack Adventure')
                    ->view('emails.transaction-success')
                    ->with([
                        'orderId'       => $this->transaction->order_id,
                        'name'          => $this->transaction->name,
                        'total'         => $this->transaction->total,
                        'subTotal'      => $this->transaction->sub_total ?? $this->transaction->total,
                        'paket'         => $this->transaction->package_name,
                        'campMembers'   => count($this->transaction->participants) ?? 1,
                        'paymentMethod' => $this->transaction->payment_method ?? ($this->notification['payment_type'] ?? '-'),
                        'dueDate'       => Carbon::parse($this->transaction->updated_at)->format('d M Y'),
                    ]);
    }
}
