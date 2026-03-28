<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AtkCategory extends Model
{
    use HasFactory;

    protected $table = 'atk_categories';

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    /**
     * Get all ATK items in this category
     */
    public function atks(): HasMany
    {
        return $this->hasMany(Atk::class, 'atk_category_id');
    }

    /**
     * Scope untuk mencari kategori
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                     ->orWhere('code', 'like', "%{$search}%");
    }
}
