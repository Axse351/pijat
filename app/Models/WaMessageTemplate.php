<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaMessageTemplate extends Model
{
    protected $fillable = [
        'key',
        'label',
        'category',
        'template',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Static helper: ambil template & isi variabel ─────────────────────
    /**
     * Ambil template berdasarkan key, lalu isi variabel.
     *
     * @param  string  $key      Key template, e.g. 'booking_reminder'
     * @param  array   $vars     ['nama_pelanggan' => 'Sari', ...]
     * @return string            Pesan yang sudah diisi variabel
     */
    public static function render(string $key, array $vars = []): string
    {
        $tpl = static::where('key', $key)->where('is_active', true)->first();

        if (!$tpl) {
            // Fallback: kembalikan string kosong jika template tidak ditemukan
            return '';
        }

        $message = $tpl->template;

        foreach ($vars as $varName => $value) {
            $message = str_replace('{{' . $varName . '}}', $value, $message);
        }

        return $message;
    }

    /**
     * Build URL WhatsApp dengan pesan yang sudah diisi.
     *
     * @param  string  $phone    Nomor telepon (format bebas, akan dinormalisasi ke 62xxx)
     * @param  string  $key      Key template
     * @param  array   $vars     Variabel pengganti
     * @return string|null       URL WA atau null jika nomor kosong / template tidak aktif
     */
    public static function waUrl(string $phone, string $key, array $vars = []): ?string
    {
        $phone = self::normalizePhone($phone);
        if (!$phone) return null;

        $message = self::render($key, $vars);
        if (!$message) return null;

        return 'https://wa.me/' . $phone . '?text=' . urlencode($message);
    }

    /**
     * Normalisasi nomor telepon ke format 62xxx.
     */
    public static function normalizePhone(string $phone): ?string
    {
        $phone = preg_replace('/\D/', '', $phone);
        if (empty($phone)) return null;
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        return $phone;
    }

    /**
     * Daftar kategori.
     */
    public static function categories(): array
    {
        return [
            'booking'    => 'Booking',
            'membership' => 'Membership',
            'customer'   => 'Pelanggan',
            'general'    => 'Umum',
        ];
    }
}
