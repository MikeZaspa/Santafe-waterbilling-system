<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'consumer_id',
        'billing_id',
        'title',
        'message',
        'type',
        'is_read'
    ];
    
    protected $casts = [
        'is_read' => 'boolean'
    ];
    
    public function consumer()
    {
        return $this->belongsTo(AdminConsumer::class, 'consumer_id');
    }
    
    public function billing()
    {
        return $this->belongsTo(AccountantBilling::class, 'billing_id');
    }
}