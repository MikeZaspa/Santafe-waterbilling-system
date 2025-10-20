<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;

    protected $fillable = [
        'consumer_id',
        'notice',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the consumer that owns the notice.
     */
    public function consumer()
    {
        return $this->belongsTo(AdminConsumer::class, 'consumer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
