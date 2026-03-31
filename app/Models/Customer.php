<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $table = 'customers';
    protected $guarded = ['id'];
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function memberships()
    {
        return $this->hasMany(CustomerMembership::class);
    }
    protected $casts = [
        'ulang_tahun'        => 'date',
        'points'             => 'integer',
        'total_points_earned' => 'integer',
    ];

    // --- Relations ---

    // --- Point Helpers ---

    /** Tambah poin dari layanan yang baru diselesaikan */
    public function addPoints(int $amount): void
    {
        if ($amount <= 0) return;
        $this->increment('points', $amount);
        $this->increment('total_points_earned', $amount);
    }

    /** Reset poin ke 0 setelah klaim bonus */
    public function redeemBonus(): bool
    {
        if ($this->points < 10) return false;
        $this->update(['points' => 0]);
        return true;
    }

    /** Cek apakah pelanggan berhak dapat bonus */
    public function hasBonus(): bool
    {
        return $this->points >= 10;
    }

    /** Poin saat ini sebagai progress menuju 10 */
    public function pointProgress(): int
    {
        return min($this->points, 10);
    }
}
