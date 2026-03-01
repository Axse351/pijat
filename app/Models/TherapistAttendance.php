<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TherapistAttendance extends Model
{
    use HasFactory;

    protected $table = 'therapist_attendances';

    protected $fillable = [
        'therapist_id',
        'check_in_at',
        'check_out_at',
        'check_in_image',
        'check_out_image',
        'check_in_confidence',
        'check_out_confidence',
        'status',
        'notes',
        'attendance_date',
    ];

    protected $casts = [
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
        'attendance_date' => 'date',
        'check_in_confidence' => 'float',
        'check_out_confidence' => 'float',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    /**
     * Relationship ke Therapist (inverse)
     */
    public function therapist()
    {
        return $this->belongsTo(Therapist::class, 'therapist_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Filter attendance untuk hari tertentu
     */
    public function scopeForDate($query, $date = null)
    {
        $date = $date ?? now()->toDateString();
        return $query->whereDate('attendance_date', $date);
    }

    /**
     * Filter attendance untuk bulan tertentu
     */
    public function scopeForMonth($query, $month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        return $query->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month);
    }

    /**
     * Filter berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Filter attendance yang sudah check-in
     */
    public function scopeCheckedIn($query)
    {
        return $query->whereNotNull('check_in_at');
    }

    /**
     * Filter attendance yang sudah check-out
     */
    public function scopeCheckedOut($query)
    {
        return $query->whereNotNull('check_out_at');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Cek apakah sudah check-in
     */
    public function isCheckedIn()
    {
        return !is_null($this->check_in_at);
    }

    /**
     * Cek apakah sudah check-out
     */
    public function isCheckedOut()
    {
        return !is_null($this->check_out_at);
    }

    /**
     * Cek apakah status hadir (tidak terlambat)
     */
    public function isPresent()
    {
        return $this->status === 'present';
    }

    /**
     * Cek apakah status terlambat
     */
    public function isLate()
    {
        return $this->status === 'late';
    }

    /**
     * Cek apakah status absen
     */
    public function isAbsent()
    {
        return $this->status === 'absent';
    }

    /**
     * Hitung durasi kerja dalam jam
     */
    public function getWorkDurationInHours()
    {
        if (!$this->isCheckedIn() || !$this->isCheckedOut()) {
            return null;
        }

        return $this->check_out_at->diffInHours($this->check_in_at);
    }

    /**
     * Hitung durasi kerja dalam menit
     */
    public function getWorkDurationInMinutes()
    {
        if (!$this->isCheckedIn() || !$this->isCheckedOut()) {
            return null;
        }

        return $this->check_out_at->diffInMinutes($this->check_in_at);
    }

    /**
     * Hitung durasi kerja dalam format HH:MM
     */
    public function getWorkDurationFormatted()
    {
        if (!$this->isCheckedIn() || !$this->isCheckedOut()) {
            return '-';
        }

        $minutes = $this->check_out_at->diffInMinutes($this->check_in_at);
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        return sprintf('%02d:%02d', $hours, $mins);
    }

    /**
     * Ambil status label dalam bahasa Indonesia
     */
    public function getStatusLabel()
    {
        return match($this->status) {
            'present' => 'Hadir',
            'late' => 'Terlambat',
            'absent' => 'Alpa',
            default => 'Tidak Diketahui'
        };
    }

    /**
     * Ambil status badge color
     */
    public function getStatusBadgeColor()
    {
        return match($this->status) {
            'present' => 'green',
            'late' => 'yellow',
            'absent' => 'red',
            default => 'gray'
        };
    }

    /**
     * Cek apakah check-in terlambat (setelah jam 09:00)
     */
    public function isCheckInLate()
    {
        if (!$this->isCheckedIn()) {
            return null;
        }

        return $this->check_in_at->format('H:i') > '09:00';
    }

    /**
     * Ambil formatted check-in time
     */
    public function getCheckInTimeFormatted()
    {
        if (!$this->isCheckedIn()) {
            return '-';
        }

        return $this->check_in_at->format('H:i:s');
    }

    /**
     * Ambil formatted check-out time
     */
    public function getCheckOutTimeFormatted()
    {
        if (!$this->isCheckedOut()) {
            return '-';
        }

        return $this->check_out_at->format('H:i:s');
    }

    /**
     * Ambil confidence check-in dalam persen
     */
    public function getCheckInConfidencePercent()
    {
        if (!$this->check_in_confidence) {
            return null;
        }

        return round($this->check_in_confidence * 100, 2);
    }

    /**
     * Ambil confidence check-out dalam persen
     */
    public function getCheckOutConfidencePercent()
    {
        if (!$this->check_out_confidence) {
            return null;
        }

        return round($this->check_out_confidence * 100, 2);
    }
}
