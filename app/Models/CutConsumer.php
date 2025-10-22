<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CutConsumer extends Model
{
    use HasFactory;

    protected $fillable = [
        'consumer_id',
        'billing_id',
        'name',
        'consumer_type',
        'meter_no',
        'reason',
        'cut_date',
        'notes',
        'cut_by',
        'billing_data'
    ];

    protected $casts = [
        'cut_date' => 'date',
        'billing_data' => 'array'
    ];

    /**
     * Get the consumer associated with the cut record
     */
    public function consumer()
    {
        return $this->belongsTo(AdminConsumer::class, 'consumer_id');
    }

    /**
     * Get the admin who cut the consumer
     */
    public function cutByUser()
    {
        return $this->belongsTo(User::class, 'cut_by');
    }
}