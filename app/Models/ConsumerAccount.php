<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsumerAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'consumer_id',
        'username',
        'password',
        'created_by',
        'updated_by'
    ];

    // Relationship with consumer
    public function consumer()
    {
        return $this->belongsTo(AdminConsumer::class, 'consumer_id');
    }

    // Relationship with creator
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relationship with updater
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}