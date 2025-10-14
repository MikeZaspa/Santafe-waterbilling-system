<?php
// app/Notifications/PaymentConfirmed.php

namespace App\Notifications;

use App\Models\Billing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    public $billing;
    public $payment;

    public function __construct($billing, $payment = null)
    {
        $this->billing = $billing;
        $this->payment = $payment;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Payment Confirmed - Santa Fe Water System')
            ->greeting('Hello ' . $notifiable->first_name . '!')
            ->line('Your payment has been confirmed and processed successfully.')
            ->line('Amount Paid: ₱' . number_format($this->billing->total_amount, 2))
            ->line('Billing Period: ' . $this->billing->reading_date->format('F Y'))
            ->action('View Receipt', url('/consumer-login'))
            ->line('Thank you for your payment!');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'payment_confirmed',
            'billing_id' => $this->billing->id,
            'amount' => $this->billing->total_amount,
            'message' => 'Payment confirmed for ' . $this->billing->reading_date->format('F Y') . '. Amount: ₱' . number_format($this->billing->total_amount, 2),
            'action_url' => '/consumer-login'
        ];
    }
}