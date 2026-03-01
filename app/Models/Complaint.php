<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    public const ADMIN_REPLY_PREFIX = '[ADMIN_REPLY] ';

    protected $fillable = [
        'consumer_id',
        'message',
        'attachment_path',
    ];

    public function consumer()
    {
        return $this->belongsTo(AdminConsumer::class, 'consumer_id');
    }

    public function isAdminReply(): bool
    {
        return str_starts_with((string) $this->message, self::ADMIN_REPLY_PREFIX);
    }

    public function plainMessage(): string
    {
        if ($this->isAdminReply()) {
            return trim(substr((string) $this->message, strlen(self::ADMIN_REPLY_PREFIX)));
        }

        return (string) $this->message;
    }
}
