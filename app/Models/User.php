<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'face_embedding',
        'face_registered_at',
        'face_liveness_enabled',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'face_embedding',
    ];

    protected $casts = [
        'email_verified_at'     => 'datetime',
        'password'              => 'hashed',
        'face_registered_at'    => 'datetime',
        'face_liveness_enabled' => 'boolean',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function isKasir(): bool
    {
        return $this->role === 'kasir';
    }

    public function isTherapist(): bool
    {
        return $this->role === 'therapist';
    }

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function therapist()
    {
        return $this->hasOne(\App\Models\Therapist::class);
    }

    public function hasFaceRegistered(): bool
    {
        return !is_null($this->face_embedding);
    }

    public function isFaceLoginEnabled(): bool
    {
        return $this->face_liveness_enabled && $this->hasFaceRegistered();
    }

    public function resetFaceData(): void
    {
        $this->update([
            'face_embedding'        => null,
            'face_registered_at'    => null,
            'face_liveness_enabled' => false,
        ]);
    }
}
