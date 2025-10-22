<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountantBilling extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'accountant_billings';

    protected $fillable = [
        'consumer_id',
        'consumer_type',
        'meter_no',
        'due_date',
        'previous_reading',
        'current_reading',
        'consumption',
        'total_amount',
        'status',
        'is_archived',
        'archived_at',
        'archived_by',
        'archive_reason',
        'archive_notes'
    ];

    protected $casts = [
        'due_date' => 'date',
        'previous_reading' => 'float',
        'current_reading' => 'float',
        'consumption' => 'float',
        'total_amount' => 'float',
        'is_archived' => 'boolean',
        'archived_at' => 'datetime'
    ];

    public function consumer()
    {
        return $this->belongsTo(AdminConsumer::class);
    }

    public function archivedBy()
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    public function onlinePayments()
    {
        return $this->hasMany(OnlinePayment::class, 'bill_id');
    }

    // Add scope for active (non-archived) bills
    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    // Add scope for archived bills
    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    // Add scope for unpaid bills
    public function scopeUnpaid($query)
    {
        return $query->where('status', '!=', 'paid');
    }

    // Add scope for pending bills
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}