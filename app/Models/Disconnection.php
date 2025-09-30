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
        'amount_due',
        'reason',
        'disconnection_date',
        'reconnection_date',
        'notes',
        'status'
    ];

    protected $casts = [
        'disconnection_date' => 'date',
        'reconnection_date' => 'date',
        'amount_due' => 'decimal:2'
    ];

    public function consumer()
    {
        return $this->belongsTo(Consumer::class);
    }

    public function billing()
    {
        return $this->belongsTo(Billing::class);
    }
}