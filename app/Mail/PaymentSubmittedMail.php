<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;
    public $bill;

    /**
     * Create a new message instance.
     *
     * @param mixed $payment
     * @param mixed $bill
     */
    public function __construct($payment, $bill)
    {
        $this->payment = $payment;
        $this->bill = $bill;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $consumerName = 'Consumer';
        if (!empty($this->bill?->consumer)) {
            $consumerName = trim($this->bill->consumer->first_name . ' ' . $this->bill->consumer->last_name);
        }

        $subject = 'Payment Submitted - ' . $consumerName;

        return $this->subject($subject)
            ->view('emails.payment-submitted');
    }
}
