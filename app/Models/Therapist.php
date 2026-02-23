<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Therapist extends Model
{
    use HasFactory;

    protected $table = 'therapists';
    protected $guarded = ['id'];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }

    public function attendanceBonuses()
    {
        return $this->hasMany(AttendanceBonus::class);
    }
}
