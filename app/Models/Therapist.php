<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Therapist extends Model
{
    use HasFactory;

    /**
     * ⚠️ PENTING: Gunakan $fillable BUKAN $guarded untuk mass assignment
     * Ini agar method update() bisa bekerja dengan baik
     */
    protected $fillable = [
        'user_id',              // ← TAMBAH INI
        'name',
        'specialty',
        'phone',
        'commission_percent',
        'is_active',
        'photo',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'commission_percent' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    /**
     * Relationship ke User (satu terapis terhubung ke satu user)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship ke attendances (kehadiran)
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(TherapistAttendance::class, 'therapist_id');
    }

    /**
     * Relationship ke face data (data wajah)
     */
    public function faceData(): HasOne
    {
        return $this->hasOne(TherapistFaceData::class, 'therapist_id');
    }

    /**
     * Relationship ke bookings
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'therapist_id');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Cek apakah therapist sudah terdaftar wajahnya
     */
    public function hasFaceRegistered(): bool
    {
        return $this->faceData !== null;
    }

    /**
     * Cek apakah wajah sudah verified
     */
    public function hasFaceVerified(): bool
    {
        return $this->faceData && $this->faceData->isVerified();
    }

    /**
     * Ambil attendance hari ini
     */
    public function getTodayAttendance()
    {
        return $this->attendances()
            ->whereDate('attendance_date', now()->toDateString())
            ->first();
    }

    /**
     * Cek apakah sudah check-in hari ini
     */
    public function isCheckedInToday(): bool
    {
        $today = $this->getTodayAttendance();
        return $today && $today->check_in_at !== null;
    }

    /**
     * Cek apakah sudah check-out hari ini
     */
    public function isCheckedOutToday(): bool
    {
        $today = $this->getTodayAttendance();
        return $today && $today->check_out_at !== null;
    }

    /**
     * Get jumlah sesi hari ini
     */
    public function getSessionsTodayCount(): int
    {
        return $this->bookings()
            ->whereDate('scheduled_at', now()->toDateString())
            ->count();
    }

    /**
     * Get revenue hari ini
     */
    public function getTodayRevenue(): float
    {
        return (float) $this->bookings()
            ->whereDate('scheduled_at', now()->toDateString())
            ->sum('final_price');
    }

    /**
     * Get commission hari ini
     */
    public function getTodayCommission(): float
    {
        $revenue = $this->getTodayRevenue();
        return $revenue * ($this->commission_percent / 100);
    }

    /**
     * Get revenue bulan ini
     */
    public function getMonthRevenue(): float
    {
        return (float) $this->bookings()
            ->whereMonth('scheduled_at', now()->month)
            ->whereYear('scheduled_at', now()->year)
            ->sum('final_price');
    }

    /**
     * Get commission bulan ini
     */
    public function getMonthCommission(): float
    {
        $revenue = $this->getMonthRevenue();
        return $revenue * ($this->commission_percent / 100);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope untuk therapist aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk therapist sesuai speciality
     */
    public function scopeBySpecialty($query, $specialty)
    {
        return $query->where('specialty', $specialty);
    }

    /**
     * Scope untuk therapist dengan user terhubung
     */
    public function scopeWithUser($query)
    {
        return $query->whereNotNull('user_id');
    }

    /**
     * Scope untuk therapist tanpa user
     */
    public function scopeWithoutUser($query)
    {
        return $query->whereNull('user_id');
    }
}
