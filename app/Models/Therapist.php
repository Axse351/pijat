<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Therapist extends Model
{
    use HasFactory;

    /**
     * ⚠️ PENTING: Gunakan $fillable BUKAN $guarded untuk mass assignment
     * Ini agar method update() bisa bekerja dengan baik
     */
    protected $fillable = [
        'user_id',
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

    /**
     * Relationship ke schedules (jadwal kerja)
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(TherapistSchedule::class, 'therapist_id');
    }

    /**
     * ⭐ BARU: Relasi HasOne ke jadwal HARI INI.
     * Dipakai untuk eager-load (with('todaySchedule')) di TherapistAttendanceController@index
     * supaya info shift bisa ditampilkan di tabel Kehadiran tanpa query tambahan per baris (N+1).
     */
    public function todaySchedule(): HasOne
    {
        return $this->hasOne(TherapistSchedule::class, 'therapist_id')
            ->whereDate('schedule_date', Carbon::today());
    }

    /**
     * Relationship ke leaveRequests (pengajuan izin/cuti)
     */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(TherapistLeaveRequest::class, 'therapist_id');
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
     * Catatan: field tanggal di TherapistAttendance adalah 'attendance_date'
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

    /**
     * Check apakah terapis sedang cuti/izin pada tanggal tertentu
     */
    public function isOnLeaveOnDate($date)
    {
        return $this->leaveRequests()
            ->approved()
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->exists();
    }

    /**
     * Dapatkan jadwal untuk bulan tertentu
     */
    public function getSchedulesForMonth($month, $year)
    {
        return $this->schedules()
            ->whereMonth('schedule_date', $month)
            ->whereYear('schedule_date', $year)
            ->orderBy('schedule_date')
            ->get();
    }

    /**
     * Dapatkan izin yang aktif hari ini
     */
    public function getActiveLeaveToday()
    {
        return $this->leaveRequests()
            ->active()
            ->first();
    }

    /**
     * Dapatkan jadwal hari ini.
     * Method lama tetap dipertahankan untuk kompatibilitas di tempat lain.
     * Untuk list/tabel dengan banyak baris (mis. index Kehadiran), pakai relasi
     * todaySchedule() via eager-load supaya tidak N+1 query.
     */
    public function getTodaySchedule()
    {
        return $this->schedules()
            ->whereDate('schedule_date', Carbon::today())
            ->first();
    }

    /**
     * ⭐ BARU: Info shift hari ini siap-tampil (label + warna Tailwind).
     * Otomatis pakai relasi todaySchedule yang sudah di-eager-load kalau ada,
     * kalau belum di-load, fallback ke query langsung (getTodaySchedule()).
     *
     * Return array:
     * [
     *   'status'     => string|null (working, working_afternoon, off, sick, vacation, cuti_bersama, null)
     *   'label'      => string ('Kerja Pagi', 'Libur', dst)
     *   'bg'         => string (class Tailwind background)
     *   'text'       => string (class Tailwind text color)
     *   'start_time' => string|null
     *   'end_time'   => string|null
     *   'is_working' => bool
     * ]
     */
    public function getTodayShiftInfo(): array
    {
        $sched  = $this->relationLoaded('todaySchedule') ? $this->todaySchedule : $this->getTodaySchedule();
        $status = $sched?->status;

        $isNightShift = false;
        if ($status === 'working' && $sched?->start_time) {
            $startHour    = Carbon::parse($sched->start_time)->hour;
            $isNightShift = $startHour >= 18 || $startHour < 6;
        }

        [$bg, $text] = match ($status) {
            'working' => $isNightShift
                ? ['bg-green-800 dark:bg-green-900', 'text-white']
                : ['bg-green-100 dark:bg-green-900/30', 'text-green-700 dark:text-green-300'],
            'working_afternoon' => ['bg-amber-100 dark:bg-amber-900/30', 'text-amber-700 dark:text-amber-300'],
            'off' => ['bg-orange-100 dark:bg-orange-900/30', 'text-orange-700 dark:text-orange-300'],
            'sick' => ['bg-gray-100 dark:bg-gray-700', 'text-gray-600 dark:text-gray-300'],
            'vacation' => ['bg-blue-100 dark:bg-blue-900/30', 'text-blue-700 dark:text-blue-300'],
            'cuti_bersama' => ['bg-red-100 dark:bg-red-900/30', 'text-red-700 dark:text-red-300'],
            default => ['bg-gray-100 dark:bg-gray-700/40 border border-dashed border-gray-300 dark:border-gray-600', 'text-gray-400'],
        };

        $label = match ($status) {
            'working' => $isNightShift ? 'Kerja Malam' : 'Kerja Pagi',
            'working_afternoon' => 'Kerja Siang',
            'off' => 'Libur',
            'sick' => 'Sakit',
            'vacation' => 'Ijin',
            'cuti_bersama' => 'Cuti Bersama',
            default => 'Belum Ada Jadwal',
        };

        return [
            'status'     => $status,
            'label'      => $label,
            'bg'         => $bg,
            'text'       => $text,
            'start_time' => $sched?->start_time,
            'end_time'   => $sched?->end_time,
            'is_working' => in_array($status, ['working', 'working_afternoon']),
        ];
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
