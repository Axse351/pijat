<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class TherapistLeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'therapist_id',
        'start_date',
        'end_date',
        'type',
        'reason',
        'status',
        'approved_by',
        'approval_notes',
        'approved_at',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'approved_at' => 'datetime',
    ];

    /**
     * Relasi ke Therapist
     */
    public function therapist(): BelongsTo
    {
        return $this->belongsTo(Therapist::class);
    }

    /**
     * Relasi ke User (yang approve)
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope: Izin yang pending
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Izin yang approved
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope: Izin yang rejected
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope: Izin yang aktif (berdasarkan tanggal sekarang)
     */
    public function scopeActive($query)
    {
        $today = Carbon::today();
        return $query->where('status', 'approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today);
    }

    /**
     * Check apakah izin ini berlaku pada tanggal tertentu
     */
    public function appliesToDate($date)
    {
        $checkDate = Carbon::parse($date)->toDateString();
        $startDate = $this->start_date->toDateString();
        $endDate = $this->end_date->toDateString();

        return $checkDate >= $startDate && $checkDate <= $endDate && $this->status === 'approved';
    }

    /**
     * Jumlah hari izin
     */
    public function getDayCountAttribute()
    {
        return $this->end_date->diffInDays($this->start_date) + 1;
    }

    /**
     * Label tipe izin
     */
    public static function getTypeLabel($type)
    {
        return match ($type) {
            'sakit' => '🏥 Sakit',
            'pribadi' => '👤 Pribadi',
            'cuti' => '🏖️ Cuti',
            'izin_khusus' => '⭐ Izin Khusus',
            default => $type,
        };
    }

    /**
     * Label status
     */
    public static function getStatusLabel($status)
    {
        return match ($status) {
            'pending' => '⏳ Menunggu Persetujuan',
            'approved' => '✅ Disetujui',
            'rejected' => '❌ Ditolak',
            default => $status,
        };
    }
}
