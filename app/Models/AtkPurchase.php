<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AtkPurchase extends Model
{
    use HasFactory;

    protected $table = 'atk_purchases';

    protected $fillable = [
        'atk_id',
        'branch_id',
        'created_by',
        'quantity',
        'unit_price',
        'total_price',
        'purchase_date',
        'status',
        'notes',
        'receipt_number',
    ];

    protected $casts = [
        'purchase_date' => 'datetime',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    /**
     * Get item ATK
     */
    public function atk(): BelongsTo
    {
        return $this->belongsTo(Atk::class, 'atk_id');
    }

    /**
     * Get branch
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Get user yang membuat
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get pengurang pendapatan (Opex)
     */
    public function opex(): HasOne
    {
        return $this->hasOne(AtkOpex::class, 'atk_purchase_id');
    }

    /**
     * Scope untuk status tertentu
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk periode tertentu
     */
    public function scopeInPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('purchase_date', [$startDate, $endDate]);
    }

    /**
     * Scope untuk branch tertentu
     */
    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Get total pembelian berdasarkan kategori
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->whereHas('atk', function ($q) use ($categoryId) {
            $q->where('atk_category_id', $categoryId);
        });
    }

    /**
     * Calculate dan simpan opex otomatis
     */
    public function recordOpex()
    {
        if ($this->status === 'completed' && !$this->opex()->exists()) {
            $this->opex()->create([
                'branch_id' => $this->branch_id,
                'amount' => $this->total_price,
                'opex_category' => 'atk_purchase',
                'recorded_date' => now(),
                'status' => 'recorded',
                'notes' => "Pembelian ATK: {$this->atk->name}",
            ]);
        }
    }

    /**
     * Reverse opex jika pembelian dibatalkan
     */
    public function reverseOpex()
    {
        if ($this->opex()->exists()) {
            $this->opex()->update(['status' => 'reversed']);
        }
    }

    /**
     * Get formatted total price
     */
    public function getFormattedTotalPriceAttribute()
    {
        return 'Rp ' . number_format($this->total_price, 2, ',', '.');
    }

    /**
     * Get formatted unit price
     */
    public function getFormattedUnitPriceAttribute()
    {
        return 'Rp ' . number_format($this->unit_price, 2, ',', '.');
    }
}
