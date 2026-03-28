<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';
    protected $guarded = ['id'];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'original_scheduled_at' => 'datetime',
        'is_rescheduled' => 'boolean',
    ];

    // ───────────────────────────────────────────────
    // RELATIONSHIPS
    // ───────────────────────────────────────────────

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function therapist(): BelongsTo
    {
        return $this->belongsTo(Therapist::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function commission(): HasOne
    {
        return $this->hasOne(Commission::class);
    }

    // ───────────────────────────────────────────────
    // SCOPES
    // ───────────────────────────────────────────────

    /**
     * Scope: Dapatkan booking yang dijadwalkan
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope: Dapatkan booking yang sedang berlangsung
     */
    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }

    /**
     * Scope: Dapatkan booking yang sudah selesai
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: Dapatkan booking yang dibatalkan
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }


    /**
     * Scope: booking dalam rentang waktu berdasarkan scheduled_at
     */
    public function scopeInRange($query, $start, $end)
    {
        return $query->whereBetween('scheduled_at', [$start, $end]);
    }

    // ── Jika belum ada, tambahkan juga relasi berikut ────────────────────────


}
