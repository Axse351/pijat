<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceBonus extends Model
{
    use HasFactory;
    protected $table = 'attendance_bonuses';
    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
    ];

    public function therapist()
    {
        return $this->belongsTo(Therapist::class);
    }
}
