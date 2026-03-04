<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
        'penalty_amount',
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
        'penalty_amount' => 'float',
        'is_archived' => 'boolean',
        'archived_at' => 'datetime'
    ];

    protected $appends = [
        'amount_due',
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

    public static function applyAutomaticOverduePenalties(?int $consumerId = null): int
    {
        $graceCutoffDate = Carbon::now()->startOfDay()->subDays(3)->toDateString();

        $query = static::query()
            ->where('status', '!=', 'paid')
            ->whereDate('due_date', '<', $graceCutoffDate);

        if (!is_null($consumerId)) {
            $query->where('consumer_id', $consumerId);
        }

        $query->where(function ($q) {
            $q->where('status', '!=', 'overdue')
                ->orWhereNull('penalty_amount')
                ->orWhere('penalty_amount', '<', 10);
        });

        return $query->update([
            'status' => 'overdue',
            'penalty_amount' => DB::raw('CASE WHEN COALESCE(penalty_amount, 0) < 10 THEN 10 ELSE penalty_amount END'),
        ]);
    }

    public function getAmountDueAttribute(): float
    {
        return (float) $this->total_amount + (float) ($this->penalty_amount ?? 0);
    }
}
