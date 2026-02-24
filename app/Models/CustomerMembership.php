<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerMembership extends Model
{
    use HasFactory;
    protected $table = 'customer_memberships';
    protected $guarded = ['id'];

    public function customer()
{    return $this->belongsTo(Customer::class);
}
}
