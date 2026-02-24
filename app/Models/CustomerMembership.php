<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerMembership extends Model
{
    use HasFactory;

    protected $table = 'customer_memberships';
    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_active'  => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relasi ini yang kurang — penyebab error "name on null"
    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }
}
