<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $notice;
    public $prefix;

    /**
     * Create a new message instance.
     *
     * @param mixed $notice
     * @param string $prefix
     */
    public function __construct($notice, string $prefix)
    {
        $this->notice = $notice;
        $this->prefix = $prefix;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Santa Fe Water Billing System - ' . $this->prefix;

        return $this->subject($subject)
            ->view('emails.notice');
    }
}
