<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disconnection extends Model
{
    use HasFactory;

    protected $fillable = [
        'consumer_id',
        'billing_id',
        'reason',
        'notes',
        'disconnection_date',
        'reconnection_date',
        'status'
    ];

    protected $casts = [
        'disconnection_date' => 'date',
        'reconnection_date' => 'date',
    ];

    public function consumer()
    {
        return $this->belongsTo(AdminConsumer::class);
    }

    public function billing()
    {
        return $this->belongsTo(Billing::class);
    }
}