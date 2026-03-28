<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Atk extends Model
{
    use HasFactory;

    protected $table = 'atks';

    protected $fillable = [
        'atk_category_id',
        'name',
        'code',
        'description',
        'stock',
        'last_purchase_price',
    ];

    protected $casts = [
        'last_purchase_price' => 'decimal:2',
        'stock' => 'integer',
    ];

    /**
     * Get kategori ATK
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(AtkCategory::class, 'atk_category_id');
    }

    /**
     * Get semua pembelian ATK ini
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(AtkPurchase::class, 'atk_id');
    }

    /**
     * Get history stok
     */
    public function stockHistories(): HasMany
    {
        return $this->hasMany(AtkStockHistory::class, 'atk_id');
    }

    /**
     * Scope untuk mencari ATK
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                     ->orWhere('code', 'like', "%{$search}%");
    }

    /**
     * Scope untuk kategori tertentu
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('atk_category_id', $categoryId);
    }

    /**
     * Scope untuk ATK yang stoknya rendah (< 5)
     */
    public function scopeLowStock($query)
    {
        return $query->where('stock', '<', 5);
    }

    /**
     * Get total pembelian dalam periode tertentu
     */
    public function getTotalPurchasesInPeriod($startDate, $endDate)
    {
        return $this->purchases()
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('total_price');
    }

    /**
     * Get last purchase price formatted
     */
    public function getFormattedLastPurchasePriceAttribute()
    {
        return 'Rp ' . number_format($this->last_purchase_price ?? 0, 2, ',', '.');
    }
}
