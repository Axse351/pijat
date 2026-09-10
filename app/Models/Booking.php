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

    /**
     * ⭐ Sinkronkan payments.amount dengan final_price booking ini,
     * kalau booking sudah punya payment — DAN catat perubahannya
     * ke payment_amount_logs supaya ada jejak, bukan overwrite diam-diam.
     *
     * Komisi TIDAK ikut disentuh — tetap dihitung dari `price` (harga asli).
     */
    public function syncPaymentAmount(?string $reason = null): void
    {
        $payment = $this->payment;

        if (!$payment) {
            return;
        }

        $oldAmount = round((float) $payment->amount, 2);
        $newAmount = round((float) $this->final_price, 2);

        if ($oldAmount === $newAmount) {
            return;
        }

        \App\Models\PaymentAmountLog::create([
            'payment_id' => $payment->id,
            'booking_id' => $this->id,
            'old_amount' => $oldAmount,
            'new_amount' => $newAmount,
            'reason'     => $reason ?? 'Auto-sync: final_price booking berubah',
            'changed_by' => auth()->id(),
        ]);

        $payment->update(['amount' => $newAmount]);
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
     * Apakah booking ini pakai paket program atau layanan home service?
     * Keduanya mendapat komisi terapis 30%.
     */
    public function isProgramBooking(): bool
    {
        return $this->program_id !== null
            || $this->commission_type === 'program'
            || (bool) ($this->service->is_home_service ?? false);
    }

    /**
     * Persentase komisi terapis untuk booking ini.
     * Home service / program → 30%, reguler → 25%
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
        if ($this->program_id !== null || $this->commission_type === 'program') {
            return 'Program (30%)';
        }

        if ((bool) ($this->service->is_home_service ?? false)) {
            return 'Home Service (30%)';
        }

        return 'Standard (25%)';
    }
}
