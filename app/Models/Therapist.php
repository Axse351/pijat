<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Therapist extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'specialization',
        'status',
        // tambah field lainnya sesuai kebutuhan
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    /**
     * Relationship ke attendances (kehadiran)
     */
    public function attendances()
    {
        return $this->hasMany(TherapistAttendance::class, 'therapist_id');
    }

    /**
     * Relationship ke face data (data wajah)
     */
    public function faceData()
    {
        return $this->hasOne(TherapistFaceData::class, 'therapist_id');
    }

    /**
     * Relationship ke bookings (jika ada)
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'therapist_id');
    }

    /**
     * Relationship ke user (jika therapist terhubung dengan user account)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Cek apakah therapist sudah terdaftar wajahnya
     */
    public function hasFaceRegistered()
    {
        return $this->faceData !== null;
    }

    /**
     * Cek apakah wajah sudah verified
     */
    public function hasFaceVerified()
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
    public function isCheckedInToday()
    {
        $today = $this->getTodayAttendance();
        return $today && $today->check_in_at !== null;
    }

    /**
     * Cek apakah sudah check-out hari ini
     */
    public function isCheckedOutToday()
    {
        $today = $this->getTodayAttendance();
        return $today && $today->check_out_at !== null;
    }
}
