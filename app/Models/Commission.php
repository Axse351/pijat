<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory;

    protected $table = 'commissions';
    protected $guarded = ['id'];

    protected $casts = [
        'is_paid'    => 'boolean',
        'week_start' => 'date',
        'week_end'   => 'date',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function therapist()
    {
        return $this->belongsTo(Therapist::class);
    }
}
