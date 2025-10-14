<?php
// app/Notifications/NewBillingCreated.php

namespace App\Notifications;

use App\Models\Billing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBillingCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public $billing;

    public function __construct(Billing $billing)
    {
        $this->billing = $billing;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Water Bill Generated - Santa Fe Water System')
            ->greeting('Hello ' . $notifiable->first_name . '!')
            ->line('A new water bill has been generated for your account.')
            ->line('Billing Period: ' . $this->billing->reading_date->format('F Y'))
            ->line('Amount Due: ₱' . number_format($this->billing->total_amount, 2))
            ->line('Due Date: ' . $this->billing->due_date->format('F d, Y'))
            ->action('View Bill', url('/consumer-login'))
            ->line('Thank you for using Santa Fe Water System!');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'billing_created',
            'billing_id' => $this->billing->id,
            'amount' => $this->billing->total_amount,
            'due_date' => $this->billing->due_date->format('Y-m-d'),
            'message' => 'New bill generated for ' . $this->billing->reading_date->format('F Y') . '. Amount: ₱' . number_format($this->billing->total_amount, 2),
            'action_url' => '/consumer-login'
        ];
    }
}