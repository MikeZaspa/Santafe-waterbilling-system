<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;

class AdminConsumer extends Model
{
    use HasFactory;
   protected $table = 'admin_consumers';
   protected $fillable = [
    'first_name',
    'middle_name',
    'last_name',
    'suffix',
    'consumer_type',
    'meter_no',
    'contact_number',
    'address',
    'address_information', // Add this
    'connection_date', // Add this
    'consumer_type',
    'status'
];

    protected $casts = [
        'connection_date' => 'date',
        'consumer_type' => 'string',
        'status' => 'string'
    ];
  protected $attributes = [
    'status' => 'active'
];
     public function account(): HasOne
    {
        return $this->hasOne(ConsumerAccount::class, 'consumer_id');
    }
        // In Consumer model
    public function billings()
    {
        return $this->hasMany(AccountantBilling::class, 'consumer_id');
    }

     public function unreadNotifications()
    {
        return $this->morphMany(\Illuminate\Notifications\DatabaseNotification::class, 'notifiable')
                    ->whereNull('read_at')
                    ->orderBy('created_at', 'desc');
    }

    public function notifications()
    {
        return $this->morphMany(\Illuminate\Notifications\DatabaseNotification::class, 'notifiable')
                    ->orderBy('created_at', 'desc');
    }
}