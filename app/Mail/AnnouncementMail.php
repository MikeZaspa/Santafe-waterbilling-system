<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AnnouncementMail extends Mailable
{
    use Queueable, SerializesModels;

    public $announcement;
    public $prefix;

    /**
     * Create a new message instance.
     *
     * @param mixed $announcement
     * @param string $prefix
     */
    public function __construct($announcement, string $prefix)
    {
        $this->announcement = $announcement;
        $this->prefix = $prefix;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Santa Fe Water Billing System - ' . $this->prefix . ': ' . $this->announcement->title;

        return $this->subject($subject)
            ->view('emails.announcement');
    }
}
