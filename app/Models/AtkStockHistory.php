<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AtkStockHistory extends Model
{
    use HasFactory;

    protected $table = 'atk_stock_histories';

    protected $fillable = [
        'atk_id',
        'branch_id',
        'quantity_before',
        'quantity_after',
        'quantity_change',
        'type',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'quantity_before' => 'integer',
        'quantity_after' => 'integer',
        'quantity_change' => 'integer',
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
     * Get user yang melakukan perubahan
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope untuk jenis perubahan tertentu
     */
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope untuk periode tertentu
     */
    public function scopeInPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope untuk branch tertentu
     */
    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute()
    {
        $labels = [
            'in' => 'Pembelian Masuk',
            'out' => 'Pengeluaran',
            'adjustment' => 'Penyesuaian',
            'return' => 'Pengembalian',
        ];

        return $labels[$this->type] ?? $this->type;
    }
}
