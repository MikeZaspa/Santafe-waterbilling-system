<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $email;

    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    public function build()
{
    $encodedToken = urlencode($this->token);
    $encodedEmail = urlencode($this->email);
    
    $resetUrl = url("/password/reset/{$encodedToken}/{$encodedEmail}");
    
    return $this->subject('Santa Fe Water - Password Reset Request')
                ->view('emails.password-reset')
                ->with([
                    'resetUrl' => $resetUrl,
                    'email' => $this->email,
                ]);
}
}