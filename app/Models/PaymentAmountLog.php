<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentAmountLog extends Model
{
    use HasFactory;

    protected $table = 'payment_amount_logs';
    protected $guarded = ['id'];

    protected $casts = [
        'old_amount' => 'decimal:2',
        'new_amount' => 'decimal:2',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function getSelisihAttribute(): float
    {
        return round((float) $this->new_amount - (float) $this->old_amount, 2);
    }
}
