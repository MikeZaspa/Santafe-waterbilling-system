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
        'name',
        'consumer_type',
        'meter_no',
        'previous_reading',
        'current_reading',
        'consumption',
        'reading_date',
        'reason',
        'disconnection_date',
        'notes',
        'disconnected_by',
        'status',
        'reconnection_date',
        'reconnection_fee'
    ];

    protected $casts = [
        'disconnection_date' => 'date',
        'reading_date' => 'date',
        'reconnection_date' => 'date',
        'reconnection_fee' => 'decimal:2'
    ];

    /**
     * Get the consumer that owns the disconnection.
     */
    public function consumer()
    {
        return $this->belongsTo(AdminConsumer::class);
    }

    /**
     * Get the billing record that was disconnected.
     */
    public function billing()
    {
        return $this->belongsTo(Billing::class);
    }
}