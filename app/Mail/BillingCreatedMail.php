<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BillingCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $consumer;
    public $billing;

    /**
     * Create a new message instance.
     *
     * @param mixed $consumer
     * @param mixed $billing
     */
    public function __construct($consumer, $billing)
    {
        $this->consumer = $consumer;
        $this->billing = $billing;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Santa Fe Water Billing System - New Billing')
            ->view('emails.billing-created');
    }
}
