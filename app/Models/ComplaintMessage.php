<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_id',
        'sender_type',
        'sender_name',
        'message',
        'attachment_path',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }
}
