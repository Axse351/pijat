<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'barangs';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori',
        'satuan',
        'stok_awal',
        'stok_masuk',
        'stok_keluar',
        'stok_aktual',
        'harga_beli',
        'harga_jual',
        'stok_minimum',
        'lokasi_simpan',
        'tanggal_kadaluarsa',
        'tanggal_kroscek',
        'petugas_kroscek',
        'catatan',
        'status',
    ];

    protected $casts = [
        'stok_awal'          => 'integer',
        'stok_masuk'         => 'integer',
        'stok_keluar'        => 'integer',
        'stok_aktual'        => 'integer',
        'stok_minimum'       => 'integer',
        'harga_beli'         => 'decimal:2',
        'harga_jual'         => 'decimal:2',
        'tanggal_kadaluarsa' => 'date',
        'tanggal_kroscek'    => 'date',
    ];

    // ========================
    // COMPUTED / ACCESSOR
    // ========================

    /**
     * Stok sistem = stok_awal + stok_masuk - stok_keluar
     */
    public function getStokSistemAttribute(): int
    {
        return $this->stok_awal + $this->stok_masuk - $this->stok_keluar;
    }

    /**
     * Selisih = stok_aktual - stok_sistem
     * Positif => stok lebih dari sistem (surplus)
     * Negatif => stok kurang dari sistem (defisit)
     */
    public function getSelisihAttribute(): int
    {
        return $this->stok_aktual - $this->stok_sistem;
    }

    /**
     * Kondisi stok berdasarkan perbandingan stok sistem vs minimum
     */
    public function getKondisiStokAttribute(): string
    {
        $stok = $this->stok_sistem;

        if ($stok <= 0) {
            return 'habis';
        } elseif ($stok <= $this->stok_minimum) {
            return 'hampir_habis';
        } else {
            return 'aman';
        }
    }

    /**
     * Label warna badge kondisi stok untuk UI
     */
    public function getBadgeKondisiAttribute(): string
    {
        return match ($this->kondisi_stok) {
            'habis'        => 'danger',
            'hampir_habis' => 'warning',
            default        => 'success',
        };
    }

    /**
     * Status kroscek: apakah ada selisih atau tidak
     */
    public function getStatusKroscekAttribute(): string
    {
        if (is_null($this->tanggal_kroscek)) {
            return 'belum_kroscek';
        }
        return $this->selisih === 0 ? 'sesuai' : 'selisih';
    }

    // ========================
    // SCOPES
    // ========================

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeStokHabis($query)
    {
        return $query->whereRaw('(stok_awal + stok_masuk - stok_keluar) <= 0');
    }

    public function scopeStokRendah($query)
    {
        return $query->whereRaw('(stok_awal + stok_masuk - stok_keluar) <= stok_minimum')
                     ->whereRaw('(stok_awal + stok_masuk - stok_keluar) > 0');
    }

    public function scopeAdaSelisih($query)
    {
        return $query->whereRaw('stok_aktual != (stok_awal + stok_masuk - stok_keluar)')
                     ->whereNotNull('tanggal_kroscek');
    }

    public function scopeKategori($query, string $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    // ========================
    // STATIC HELPERS
    // ========================

    public static function daftarKategori(): array
    {
        return [
            'minyak_pijat'  => 'Minyak Pijat',
            'lotion'        => 'Lotion / Cream',
            'handuk'        => 'Handuk',
            'linen'         => 'Linen / Sprei',
            'alat_pijat'    => 'Alat Pijat',
            'aromaterapi'   => 'Aromaterapi / Essential Oil',
            'pembersih'     => 'Sabun / Pembersih',
            'perlengkapan'  => 'Perlengkapan Umum',
            'konsumsi'      => 'Konsumsi / Minuman',
            'lainnya'       => 'Lainnya',
        ];
    }

    public static function daftarSatuan(): array
    {
        return ['pcs', 'botol', 'lembar', 'set', 'ml', 'liter', 'kg', 'gram', 'dus', 'roll', 'pak'];
    }

    /**
     * Generate kode barang otomatis
     */
    public static function generateKode(): string
    {
        $last = static::withTrashed()->orderByDesc('id')->first();
        $next = $last ? ($last->id + 1) : 1;
        return 'BRG-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
