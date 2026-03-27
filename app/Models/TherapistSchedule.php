<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class TherapistSchedule extends Model
{
    protected $fillable = [
        'therapist_id',
        'schedule_date',
        'day_of_week',
        'status',
        'start_time',
        'end_time',
        'working_hours',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'schedule_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    /**
     * Relasi ke Therapist
     */
    public function therapist(): BelongsTo
    {
        return $this->belongsTo(Therapist::class);
    }

    /**
     * Relasi ke User (admin yang membuat)
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke Attendance
     */
    public function attendance(): BelongsTo
    {
        return $this->belongsTo(TherapistAttendance::class, ['therapist_id', 'schedule_date'], ['therapist_id', 'attendance_date']);
    }

    /**
     * Cek apakah terapis bekerja pada tanggal ini
     */
    public function isWorking(): bool
    {
        return $this->status === 'working';
    }

    /**
     * Cek apakah terapis libur pada tanggal ini
     */
    public function isOff(): bool
    {
        return in_array($this->status, ['off', 'sick', 'vacation', 'cuti_bersama']);
    }

    /**
     * Dapatkan jam masuk
     */
    public function getStartTimeFormatted(): ?string
    {
        return $this->start_time ? $this->start_time->format('H:i') : null;
    }

    /**
     * Dapatkan jam keluar
     */
    public function getEndTimeFormatted(): ?string
    {
        return $this->end_time ? $this->end_time->format('H:i') : null;
    }

    /**
     * Dapatkan label status
     */
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'working' => 'Bekerja',
            'off' => 'Libur',
            'sick' => 'Sakit',
            'vacation' => 'Liburan',
            'cuti_bersama' => 'Cuti Bersama',
            default => 'Unknown'
        };
    }

    /**
     * Dapatkan warna status
     */
    public function getStatusColor(): string
    {
        return match ($this->status) {
            'working' => 'green',
            'off' => 'gray',
            'sick' => 'orange',
            'vacation' => 'purple',
            'cuti_bersama' => 'blue',
            default => 'gray'
        };
    }

    /**
     * Hitung jam kerja otomatis
     */
    public function calculateWorkingHours(): void
    {
        if ($this->isWorking() && $this->start_time && $this->end_time) {
            $start = Carbon::createFromTimeString($this->start_time->format('H:i'));
            $end = Carbon::createFromTimeString($this->end_time->format('H:i'));

            // Handle case dimana end_time < start_time (kerja malam)
            if ($end < $start) {
                $end->addDay();
            }

            $this->working_hours = intval($start->diffInHours($end));
        } else {
            $this->working_hours = null;
        }
    }

    /**
     * Boot model
     */
    protected static function boot()
    {
        parent::boot();

        // Auto calculate working hours sebelum save
        static::saving(function ($model) {
            $model->calculateWorkingHours();
        });
    }

    /**
     * Dapatkan jadwal bulan ini untuk terapis
     */
    public static function getThisMonthSchedule($therapistId)
    {
        $now = Carbon::now();
        return static::where('therapist_id', $therapistId)
            ->whereYear('schedule_date', $now->year)
            ->whereMonth('schedule_date', $now->month)
            ->orderBy('schedule_date')
            ->get();
    }

    /**
     * Dapatkan jadwal bulan tertentu untuk terapis
     */
    public static function getMonthSchedule($therapistId, $year, $month)
    {
        return static::where('therapist_id', $therapistId)
            ->whereYear('schedule_date', $year)
            ->whereMonth('schedule_date', $month)
            ->orderBy('schedule_date')
            ->get();
    }

    /**
     * Dapatkan jadwal range tanggal
     */
    public static function getScheduleRange($therapistId, $startDate, $endDate)
    {
        return static::where('therapist_id', $therapistId)
            ->whereBetween('schedule_date', [$startDate, $endDate])
            ->orderBy('schedule_date')
            ->get();
    }

    /**
     * Dapatkan hari libur terapis dalam bulan ini
     */
    public function scopeOffDaysThisMonth($query, $therapistId)
    {
        $now = Carbon::now();
        return $query->where('therapist_id', $therapistId)
            ->whereYear('schedule_date', $now->year)
            ->whereMonth('schedule_date', $now->month)
            ->where('status', '!=', 'working')
            ->orderBy('schedule_date');
    }

    /**
     * Dapatkan hari kerja terapis dalam bulan ini
     */
    public function scopeWorkingDaysThisMonth($query, $therapistId)
    {
        $now = Carbon::now();
        return $query->where('therapist_id', $therapistId)
            ->whereYear('schedule_date', $now->year)
            ->whereMonth('schedule_date', $now->month)
            ->where('status', 'working')
            ->orderBy('schedule_date');
    }

    /**
     * Cek apakah terapis seharusnya bekerja hari ini
     */
    public static function isTodayWorkingDay($therapistId): bool
    {
        $today = Carbon::today();
        return static::where('therapist_id', $therapistId)
            ->where('schedule_date', $today)
            ->where('status', 'working')
            ->exists();
    }

    /**
     * Dapatkan jadwal hari ini untuk terapis
     */
    public static function getTodaySchedule($therapistId): ?self
    {
        return static::where('therapist_id', $therapistId)
            ->where('schedule_date', Carbon::today())
            ->first();
    }
}
