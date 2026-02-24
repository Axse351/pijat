<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;
    protected $table = 'memberships';
    protected $guarded = ['id'];

    public function customers()
{
    return $this->hasMany(CustomerMembership::class);
}
}
