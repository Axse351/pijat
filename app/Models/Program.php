<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Program extends Model
{
    use HasFactory;

    protected $table = 'programs';

    protected $fillable = [
        'nama_program',
        'description',
        'image',
        'discount_type',
        'discount_value',
        'max_discount',
        'min_transaction',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'discount_value'  => 'decimal:2',
        'max_discount'    => 'decimal:2',
        'min_transaction' => 'decimal:2',
        'is_active'       => 'boolean',
        'start_date'      => 'date',
        'end_date'        => 'date',
    ];

    // ── ACCESSORS ────────────────────────────────────────────────────────

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    public function getDiscountLabelAttribute(): string
    {
        return $this->discount_type === 'percent'
            ? number_format($this->discount_value, 0) . '%'
            : 'Rp ' . number_format($this->discount_value, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Aktif' : 'Nonaktif';
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->end_date && $this->end_date->isPast();
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->start_date && $this->start_date->isFuture();
    }

    // ── SCOPES ───────────────────────────────────────────────────────────

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBerlaku($query)
    {
        return $query->where('is_active', true)
            ->where(fn($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', now()))
            ->where(fn($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', now()));
    }
}
