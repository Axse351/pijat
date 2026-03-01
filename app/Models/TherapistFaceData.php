<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TherapistFaceData extends Model
{
    use HasFactory;

    protected $table = 'therapist_face_data';

    protected $fillable = [
        'therapist_id',
        'face_embeddings',
        'reference_image',
        'samples_count',
        'status',
        'rejection_reason',
    ];

    protected $casts = [
        'face_embeddings' => 'array', // otomatis decode JSON
        'samples_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    /**
     * Relationship ke Therapist (inverse)
     */
    public function therapist()
    {
        return $this->belongsTo(Therapist::class, 'therapist_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Filter hanya yang verified
     */
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    /**
     * Filter hanya yang pending verification
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Filter hanya yang rejected
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Cek apakah sudah verified
     */
    public function isVerified()
    {
        return $this->status === 'verified';
    }

    /**
     * Cek apakah status pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Cek apakah status rejected
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Ambil status label dalam bahasa Indonesia
     */
    public function getStatusLabel()
    {
        return match($this->status) {
            'verified' => 'Terverifikasi',
            'pending' => 'Menunggu Verifikasi',
            'rejected' => 'Ditolak',
            default => 'Tidak Diketahui'
        };
    }

    /**
     * Ambil status badge color
     */
    public function getStatusBadgeColor()
    {
        return match($this->status) {
            'verified' => 'green',
            'pending' => 'yellow',
            'rejected' => 'red',
            default => 'gray'
        };
    }

    /**
     * Ambil status icon
     */
    public function getStatusIcon()
    {
        return match($this->status) {
            'verified' => '✓',
            'pending' => '⏳',
            'rejected' => '✗',
            default => '?'
        };
    }

    /**
     * Cek apakah bisa digunakan untuk face recognition (verified)
     */
    public function canBeUsed()
    {
        return $this->isVerified();
    }

    /**
     * Ambil formatted tanggal registrasi
     */
    public function getRegisteredAtFormatted($format = 'd M Y H:i')
    {
        return $this->created_at->format($format);
    }

    /**
     * Hitung berapa hari sejak registrasi
     */
    public function getDaysSinceRegistration()
    {
        return now()->diffInDays($this->created_at);
    }

    /**
     * Cek apakah data sudah lama (lebih dari 30 hari)
     */
    public function isOldData($days = 30)
    {
        return $this->getDaysSinceRegistration() > $days;
    }

    /**
     * Ambil format info status untuk display
     */
    public function getStatusInfo()
    {
        return [
            'label' => $this->getStatusLabel(),
            'color' => $this->getStatusBadgeColor(),
            'icon' => $this->getStatusIcon(),
            'can_use' => $this->canBeUsed(),
            'is_verified' => $this->isVerified(),
            'is_pending' => $this->isPending(),
            'is_rejected' => $this->isRejected(),
        ];
    }

    /**
     * Ambil array embeddings (dengan fallback jika null)
     */
    public function getEmbeddingsArray()
    {
        return $this->face_embeddings ?? [];
    }

    /**
     * Hitung jumlah embeddings yang ada
     */
    public function countEmbeddings()
    {
        $embeddings = $this->getEmbeddingsArray();
        return is_array($embeddings) ? count($embeddings) : 0;
    }

    /**
     * Ambil reference image URL
     */
    public function getReferenceImageUrl()
    {
        if (!$this->reference_image) {
            return null;
        }

        return asset('storage/' . $this->reference_image);
    }

    /**
     * Ambil reference image path relative
     */
    public function getReferenceImagePath()
    {
        return $this->reference_image;
    }

    /**
     * Cek apakah reference image ada di storage
     */
    public function hasReferenceImage()
    {
        return !is_null($this->reference_image);
    }

    /**
     * Ambil alasan rejection (jika ada)
     */
    public function getRejectionReason()
    {
        return $this->rejection_reason ?? '-';
    }

    /**
     * Ambil info lengkap untuk display
     */
    public function getDisplayInfo()
    {
        return [
            'therapist_name' => $this->therapist->name ?? 'Unknown',
            'status' => $this->getStatusLabel(),
            'status_color' => $this->getStatusBadgeColor(),
            'registered_at' => $this->getRegisteredAtFormatted(),
            'samples_count' => $this->samples_count,
            'embeddings_count' => $this->countEmbeddings(),
            'has_image' => $this->hasReferenceImage(),
            'rejection_reason' => $this->getRejectionReason(),
            'can_use' => $this->canBeUsed(),
        ];
    }
}
