<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'consumer_id',
        'message',
        'attachment_path',
    ];

    public function consumer()
    {
        return $this->belongsTo(AdminConsumer::class, 'consumer_id');
    }
}
