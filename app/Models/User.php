<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'face_embedding',
        'face_registered_at',
        'face_liveness_enabled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'face_embedding', // Jangan expose ke API
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'face_registered_at' => 'datetime',
        'face_liveness_enabled' => 'boolean',
    ];

    /**
     * Cek apakah user adalah admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user adalah user biasa
     */
    public function isUser()
    {
        return $this->role === 'user';
    }

    /**
     * Relasi dengan Customer
     */
    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    /**
     * Cek apakah user sudah registrasi wajah
     */
    public function hasFaceRegistered(): bool
    {
        return !is_null($this->face_embedding);
    }

    /**
     * Cek apakah face login aktif
     */
    public function isFaceLoginEnabled(): bool
    {
        return $this->face_liveness_enabled && $this->hasFaceRegistered();
    }

    /**
     * Reset face data - gunakan jika user ingin mendaftar ulang wajahnya
     */
    public function resetFaceData(): void
    {
        $this->update([
            'face_embedding' => null,
            'face_registered_at' => null,
            'face_liveness_enabled' => false,
        ]);
    }
}
