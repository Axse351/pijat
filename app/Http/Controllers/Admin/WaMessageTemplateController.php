<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WaMessageTemplate;
use Illuminate\Http\Request;

class WaMessageTemplateController extends Controller
{
    public function index()
    {
        $templates = WaMessageTemplate::orderBy('category')->orderBy('label')->get()
            ->groupBy('category');

        $categories = WaMessageTemplate::categories();

        return view('admin.wa-templates.index', compact('templates', 'categories'));
    }

    public function edit(WaMessageTemplate $waTemplate)
    {
        $categories = WaMessageTemplate::categories();
        return view('admin.wa-templates.edit', compact('waTemplate', 'categories'));
    }

    public function update(Request $request, WaMessageTemplate $waTemplate)
    {
        $validated = $request->validate([
            'label'     => 'required|string|max:255',
            'category'  => 'required|string|max:50',
            'template'  => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);

        $waTemplate->update([
            'label'     => $validated['label'],
            'category'  => $validated['category'],
            'template'  => $validated['template'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.wa-templates.index')
            ->with('success', 'Template "' . $waTemplate->label . '" berhasil diperbarui.');
    }

    public function reset(WaMessageTemplate $waTemplate)
    {
        // Reset ke konten default berdasarkan key
        $defaults = self::defaultTemplates();

        if (isset($defaults[$waTemplate->key])) {
            $waTemplate->update(['template' => $defaults[$waTemplate->key]]);
            return back()->with('success', 'Template berhasil direset ke default.');
        }

        return back()->with('error', 'Default template tidak ditemukan.');
    }

    /**
     * Preview: render template dengan data dummy
     */
    public function preview(Request $request, WaMessageTemplate $waTemplate)
    {
        $dummyVars = [
            'nama_pelanggan'   => 'Sari Dewi',
            'layanan'          => 'Swedish Massage',
            'terapis'          => 'Anita Putri',
            'jadwal'           => 'Senin, 15 Januari 2025 pukul 10:00',
            'jadwal_lama'      => 'Minggu, 14 Januari 2025 10:00',
            'nama_membership'  => 'Gold Member',
            'tanggal_berakhir' => '31 Desember 2025',
        ];

        $rendered = WaMessageTemplate::render($waTemplate->key, $dummyVars);

        return response()->json(['preview' => $rendered]);
    }

    private static function defaultTemplates(): array
    {
        return [
            'booking_reminder'            => "Halo {{nama_pelanggan}}, kami ingin mengingatkan booking Anda:\n\n📋 Layanan : {{layanan}}\n👤 Terapis : {{terapis}}\n🗓 Jadwal  : {{jadwal}}\n\nMohon hadir tepat waktu. Terima kasih! 🙏",
            'booking_reminder_reschedule' => "Halo {{nama_pelanggan}}, kami ingin mengingatkan booking Anda:\n\n📋 Layanan : {{layanan}}\n👤 Terapis : {{terapis}}\n🗓 Jadwal  : {{jadwal}}\n⚠️ Jadwal diubah dari {{jadwal_lama}}\n\nMohon hadir tepat waktu. Terima kasih! 🙏",
            'membership_welcome'          => "Halo {{nama_pelanggan}}! 👋\n\nSelamat ya, kamu kini resmi menjadi Member *{{nama_membership}}* di Koichi! 🎉👑\n\n✨ Nikmati berbagai keuntungan eksklusif yang sudah kami siapkan untukmu.\n📅 Membership kamu berlaku hingga *{{tanggal_berakhir}}*.\n\nTerima kasih telah mempercayakan perawatan kepada kami. Kami siap memanjakan kamu! 💆‍♀️\n\nSalam hangat,\n_Tim Koichi_ 🌸",
            'customer_birthday'           => "Halo {{nama_pelanggan}}! 🎂 Selamat ulang tahun ya! Semoga hari-harimu selalu menyenangkan. Kami di sini senang bisa melayanimu. 🎁",
            'customer_reactivation'       => "Halo {{nama_pelanggan}}! 👋 Kami kangen nih sama kamu. Sudah lama nggak ke sini, yuk mampir lagi! Ada promo menarik menanti. 😊",
            'customer_bonus_ready'        => "Halo {{nama_pelanggan}}! 🎁 Selamat, poin kamu sudah mencapai 10! Bonus gratis 1 jam siap diklaim. Segera hubungi kami! 🙌",
        ];
    }
}
