<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TwoFactorCodeMailPlumber extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $name;

    public function __construct($code, $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    public function build()
{
    return $this->subject('Your Plumber Portal Verification Code - Santa Fe Water')
                ->view('emails.two-factor-code-plumber') // Make sure this matches your actual file name
                ->with([
                    'code' => $this->code,
                    'name' => $this->name,
                ]);
}
}