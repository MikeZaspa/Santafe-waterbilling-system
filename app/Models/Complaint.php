<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'consumer_id',
        'subject',
        'message',
        'attachment_path',
        'status',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function consumer()
    {
        return $this->belongsTo(AdminConsumer::class, 'consumer_id');
    }

    public function messages()
    {
        return $this->hasMany(ComplaintMessage::class)->orderBy('created_at', 'asc');
    }

    public function unreadMessages()
    {
        return $this->hasMany(ComplaintMessage::class)->where('is_read', false);
    }

    public function markMessagesAsRead()
    {
        $this->unreadMessages()->update(['is_read' => true]);
    }

    public function addMessage($senderType, $senderName, $message, $attachmentPath = null)
    {
        $msg = ComplaintMessage::create([
            'complaint_id' => $this->id,
            'sender_type' => $senderType,
            'sender_name' => $senderName,
            'message' => $message,
            'attachment_path' => $attachmentPath,
        ]);

        // Update last message timestamp
        $this->update(['last_message_at' => now()]);

        return $msg;
    }
}
