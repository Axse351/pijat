<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wa_message_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();          // identifier unik, e.g. booking_reminder
            $table->string('label');                   // nama ramah admin, e.g. "Pengingat Booking"
            $table->string('category')->default('general'); // booking | membership | customer
            $table->text('template');                  // isi pesan, mendukung {{variabel}}
            $table->text('description')->nullable();   // keterangan variabel yang tersedia
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── Seed template default ────────────────────────────────────────
        $now = now();

        DB::table('wa_message_templates')->insert([
            // ── BOOKING ─────────────────────────────────────────────────
            [
                'key'         => 'booking_reminder',
                'label'       => 'Pengingat Booking',
                'category'    => 'booking',
                'template'    => "Halo {{nama_pelanggan}}, kami ingin mengingatkan booking Anda:\n\n📋 Layanan : {{layanan}}\n👤 Terapis : {{terapis}}\n🗓 Jadwal  : {{jadwal}}\n\nMohon hadir tepat waktu. Terima kasih! 🙏",
                'description' => "Variabel: {{nama_pelanggan}}, {{layanan}}, {{terapis}}, {{jadwal}}",
                'is_active'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'key'         => 'booking_reminder_reschedule',
                'label'       => 'Pengingat Booking (Dijadwal Ulang)',
                'category'    => 'booking',
                'template'    => "Halo {{nama_pelanggan}}, kami ingin mengingatkan booking Anda:\n\n📋 Layanan : {{layanan}}\n👤 Terapis : {{terapis}}\n🗓 Jadwal  : {{jadwal}}\n⚠️ Jadwal diubah dari {{jadwal_lama}}\n\nMohon hadir tepat waktu. Terima kasih! 🙏",
                'description' => "Variabel: {{nama_pelanggan}}, {{layanan}}, {{terapis}}, {{jadwal}}, {{jadwal_lama}}",
                'is_active'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'key'         => 'booking_complete',
                'label'       => 'Ucapan Selesai Booking',
                'category'    => 'booking',
                'template'    => "Halo {{nama_pelanggan}}! 😊\n\nTerima kasih sudah mempercayakan perawatanmu kepada kami hari ini.\n\n✅ Sesi *{{layanan}}* bersama *{{terapis}}* telah selesai.\n\n🎁 Kamu mendapatkan *{{poin}} poin* dari sesi ini!\n\nSemoga kamu merasa lebih segar & relaks. Sampai jumpa lagi! 🌸\n\n_— Tim Koichi_",
                'description' => "Variabel: {{nama_pelanggan}}, {{layanan}}, {{terapis}}, {{poin}}",
                'is_active'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],

            // ── MEMBERSHIP ──────────────────────────────────────────────
            [
                'key'         => 'membership_welcome',
                'label'       => 'Selamat Datang Member Baru',
                'category'    => 'membership',
                'template'    => "Halo {{nama_pelanggan}}! 👋\n\nSelamat ya, kamu kini resmi menjadi Member *{{nama_membership}}* di Koichi! 🎉👑\n\n✨ Nikmati berbagai keuntungan eksklusif yang sudah kami siapkan untukmu.\n📅 Membership kamu berlaku hingga *{{tanggal_berakhir}}*.\n\nTerima kasih telah mempercayakan perawatan kepada kami. Kami siap memanjakan kamu! 💆‍♀️\n\nSalam hangat,\n_Tim Koichi_ 🌸",
                'description' => "Variabel: {{nama_pelanggan}}, {{nama_membership}}, {{tanggal_berakhir}}",
                'is_active'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],

            // ── CUSTOMER ────────────────────────────────────────────────
            [
                'key'         => 'customer_birthday',
                'label'       => 'Ucapan Ulang Tahun',
                'category'    => 'customer',
                'template'    => "Halo {{nama_pelanggan}}! 🎂 Selamat ulang tahun ya! Semoga hari-harimu selalu menyenangkan. Kami di sini senang bisa melayanimu. 🎁",
                'description' => "Variabel: {{nama_pelanggan}}",
                'is_active'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'key'         => 'customer_reactivation',
                'label'       => 'Ajak Pelanggan Kembali',
                'category'    => 'customer',
                'template'    => "Halo {{nama_pelanggan}}! 👋 Kami kangen nih sama kamu. Sudah lama nggak ke sini, yuk mampir lagi! Ada promo menarik menanti. 😊",
                'description' => "Variabel: {{nama_pelanggan}}",
                'is_active'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'key'         => 'customer_bonus_ready',
                'label'       => 'Notifikasi Bonus Siap Klaim',
                'category'    => 'customer',
                'template'    => "Halo {{nama_pelanggan}}! 🎁 Selamat, poin kamu sudah mencapai 10! Bonus gratis 1 jam siap diklaim. Segera hubungi kami! 🙌",
                'description' => "Variabel: {{nama_pelanggan}}",
                'is_active'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('wa_message_templates');
    }
};
