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
        'scheduled_at'          => 'datetime',
        'original_scheduled_at' => 'datetime',
        'cancelled_at'          => 'datetime',
        'is_rescheduled'        => 'boolean',
        'is_specific_therapist' => 'boolean',
    ];

    // ── RELATIONSHIPS ─────────────────────────────────────────────────────

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

    // ── SCOPES ────────────────────────────────────────────────────────────

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeInRange($query, $start, $end)
    {
        return $query->whereBetween('scheduled_at', [$start, $end]);
    }

    // ── HELPERS / ACCESSORS ───────────────────────────────────────────────

    /**
     * Apakah booking ini pakai paket program (komisi 30%)?
     */
    public function isProgramBooking(): bool
    {
        return $this->program_id !== null || $this->commission_type === 'program';
    }

    /**
     * Persentase komisi terapis untuk booking ini
     */
    public function getCommissionRateAttribute(): float
    {
        return $this->isProgramBooking() ? 30.0 : 25.0;
    }

    /**
     * Nominal komisi terapis (kalkulasi langsung dari final_price)
     */
    public function getCommissionAmountAttribute(): float
    {
        return round($this->final_price * ($this->commission_rate / 100), 2);
    }

    /**
     * Income bersih Koichi dari booking ini
     */
    public function getKoichiIncomeAttribute(): float
    {
        return round($this->final_price - $this->commission_amount, 2);
    }

    /**
     * Persentase income Koichi
     */
    public function getKoichiPercentAttribute(): float
    {
        return 100.0 - $this->commission_rate;
    }

    /**
     * Apakah booking ini kena cancel forfeit?
     * (Sudah bayar + terapis spesifik + status cancelled)
     */
    public function isCancelForfeit(): bool
    {
        return $this->status === 'cancelled'
            && $this->is_specific_therapist
            && $this->payment !== null;
    }

    /**
     * Label tipe komisi untuk UI
     */
    public function getCommissionTypeLabelAttribute(): string
    {
        return $this->isProgramBooking() ? 'Program (30%)' : 'Standard (25%)';
    }
}
