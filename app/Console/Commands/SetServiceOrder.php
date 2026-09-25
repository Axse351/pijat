<?php

namespace App\Console\Commands;

use App\Models\Service;
use Illuminate\Console\Command;

class SetServiceOrder extends Command
{
    protected $signature = 'services:set-order';
    protected $description = 'Set kolom urutan tampil layanan Koichi sesuai urutan yang ditentukan';

    protected array $order = [
        'KOICHI Authentic Therapy 120'  => 1,
        'KOICHI Authentic Therapy 90'   => 2,
        'KOICHI Authentic Therapy 60'   => 3,
        'KOICHI Massage 120'            => 4,
        'KOICHI Massage 90'             => 5,
        'KOICHI Massage 60'             => 6,
        'KOICHI Face Massage 30'        => 7,
        'Hot Stone Therapy 30'          => 8,
        'Kids Authentic Therapy 60'     => 9,
        'Kids Massage 60'               => 10,
        'KOICHI Extra 30'               => 11,
        'KOICHI Extra 60'               => 12,
        'KOICHI Home Service 120'       => 13,
        'KOICHI Home Service 90'        => 14,
        'KOICHI Home Service 60'        => 15,
        'KOICHI Home Service Extra 30'  => 16,
        'KOICHI Home Service Extra 60'  => 17,
    ];

    public function handle(): int
    {
        $notFound = [];

        foreach ($this->order as $namePart => $urutan) {
            $matches = Service::where('name', 'like', "%{$namePart}%")->get();

            if ($matches->isEmpty()) {
                $notFound[] = $namePart;
                continue;
            }

            if ($matches->count() > 1) {
                $this->warn("⚠ '{$namePart}' cocok dengan {$matches->count()} layanan, semua di-set urutan {$urutan}:");
                foreach ($matches as $m) {
                    $this->line("   - {$m->name} (id: {$m->id})");
                }
            }

            Service::where('name', 'like', "%{$namePart}%")->update(['urutan' => $urutan]);
            $this->info("✓ urutan {$urutan} -> {$namePart}");
        }

        if (!empty($notFound)) {
            $this->error('Tidak ditemukan layanan untuk pola berikut, cek manual:');
            foreach ($notFound as $n) {
                $this->line("   - {$n}");
            }
        }

        $this->newLine();
        $this->info('Hasil akhir urutan:');
        Service::orderBy('urutan')->get(['id', 'name', 'urutan'])->each(function ($s) {
            $this->line("{$s->urutan}. {$s->name} (id: {$s->id})");
        });

        return self::SUCCESS;
    }
}
