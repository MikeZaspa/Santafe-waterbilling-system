<?php
// app/Notifications/PaymentDueReminder.php

namespace App\Notifications;

use App\Models\Billing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentDueReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public $billing;
    public $daysUntilDue;

    public function __construct(Billing $billing, $daysUntilDue = null)
    {
        $this->billing = $billing;
        $this->daysUntilDue = $daysUntilDue;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject('Payment Due Reminder - Santa Fe Water System')
            ->greeting('Hello ' . $notifiable->first_name . '!');
            
        if ($this->daysUntilDue > 0) {
            $message->line('Your water bill payment is due in ' . $this->daysUntilDue . ' days.');
        } else {
            $message->line('Your water bill payment is due today.');
        }
            
        $message->line('Amount Due: ₱' . number_format($this->billing->total_amount, 2))
                ->line('Due Date: ' . $this->billing->due_date->format('F d, Y'))
                ->action('Pay Now', url('/consumer-login'))
                ->line('Thank you for using Santa Fe Water System!');

        return $message;
    }

    public function toArray($notifiable)
    {
        $message = $this->daysUntilDue > 0 
            ? 'Payment due in ' . $this->daysUntilDue . ' days'
            : 'Payment due today';

        return [
            'type' => 'payment_due_reminder',
            'billing_id' => $this->billing->id,
            'amount' => $this->billing->total_amount,
            'due_date' => $this->billing->due_date->format('Y-m-d'),
            'message' => $message . '. Amount: ₱' . number_format($this->billing->total_amount, 2),
            'action_url' => '/consumer-login'
        ];
    }
}