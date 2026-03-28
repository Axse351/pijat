<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AtkOpex extends Model
{
    use HasFactory;

    protected $table = 'atk_opex';

    protected $fillable = [
        'atk_purchase_id',
        'branch_id',
        'amount',
        'opex_category',
        'recorded_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'recorded_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    /**
     * Get pembelian ATK
     */
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(AtkPurchase::class, 'atk_purchase_id');
    }

    /**
     * Get branch
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
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
        return $query->whereBetween('recorded_date', [$startDate, $endDate]);
    }

    /**
     * Scope untuk branch tertentu
     */
    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Get total opex untuk branch dalam periode
     */
    public static function getTotalByBranchAndPeriod($branchId, $startDate, $endDate)
    {
        return self::byBranch($branchId)
            ->status('recorded')
            ->inPeriod($startDate, $endDate)
            ->sum('amount');
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 2, ',', '.');
    }
}
