<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TherapistLeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'therapist_id',
        'type',
        'start_date',
        'end_date',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'approval_notes',
        'rejection_reason',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'approved_at' => 'datetime',
    ];

    // ──────────────────────────────────────────────
    // RELATIONSHIPS
    // ──────────────────────────────────────────────

    public function therapist()
    {
        return $this->belongsTo(Therapist::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Alias — dipakai di view & with('approver')
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ──────────────────────────────────────────────
    // SCOPES
    // ──────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'approved')
            ->where('start_date', '<=', now()->toDateString())
            ->where('end_date', '>=', now()->toDateString());
    }

    // ──────────────────────────────────────────────
    // ACCESSORS
    // ──────────────────────────────────────────────

    public function getDurationAttribute(): int
    {
        return $this->end_date->diffInDays($this->start_date) + 1;
    }

    /**
     * Alias day_count — dipakai di view
     */
    public function getDayCountAttribute(): int
    {
        return $this->duration;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            default    => 'gray',
        };
    }
}
