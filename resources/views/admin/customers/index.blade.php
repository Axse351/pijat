<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Pelanggan</h2>
            <a href="{{ route('admin.customers.create') }}"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                + Tambah Pelanggan
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">
                    ✓ {{ session('success') }}
                </div>
            @endif

            @php
                /**
                 * Hitung hari sampai ulang tahun berikutnya.
                 * Ambil bulan+hari dari ulang_tahun, set ke tahun ini.
                 * Kalau sudah lewat hari ini → set ke tahun depan.
                 * Return int (0 = hari ini).
                 */
                function daysUntilBirthday($ulang_tahun): int
                {
                    $today = \Carbon\Carbon::today();
                    $bday = \Carbon\Carbon::createFromFormat('Y-m-d', $ulang_tahun)->setYear($today->year);

                    // Kalau hari+bulan sudah lewat hari ini, geser ke tahun depan
                    if ($bday->lt($today)) {
                        $bday->addYear();
                    }

                    return (int) $today->diffInDays($bday);
                }

                $soonBirthdays = $customers->filter(function ($c) {
                    if (!$c->ulang_tahun) {
                        return false;
                    }
                    $days = daysUntilBirthday($c->ulang_tahun);
                    $c->_days_until_bday = $days;
                    return $days <= 7;
                });
            @endphp

            {{-- Banner ulang tahun dalam 7 hari --}}
            @if ($soonBirthdays->count())
                <div class="mb-4 p-4 bg-pink-50 border border-pink-200 rounded-xl">
                    <p class="text-xs font-semibold text-pink-500 uppercase tracking-wider mb-2">
                        🎂 Ulang Tahun Dalam 7 Hari
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($soonBirthdays as $c)
                            @php
                                $phone = preg_replace('/[^0-9]/', '', $c->phone ?? '');
                                if (str_starts_with($phone, '0')) {
                                    $phone = '62' . substr($phone, 1);
                                }
                                $days = $c->_days_until_bday;
                                $label = $days === 0 ? 'Hari ini! 🎉' : "{$days} hari lagi";
                                $msg = urlencode(
                                    "Halo {$c->name}! 🎂 Selamat ulang tahun ya! Semoga hari-harimu selalu menyenangkan. Kami di sini senang bisa melayanimu. 🎁",
                                );
                            @endphp
                            <div
                                class="flex items-center gap-2 px-3 py-2 bg-white border border-pink-200 rounded-lg text-sm">
                                <span class="font-medium text-gray-800">{{ $c->name }}</span>
                                <span class="text-xs text-pink-500">{{ $label }}</span>
                                @if ($phone)
                                    <a href="https://wa.me/{{ $phone }}?text={{ $msg }}" target="_blank"
                                        class="px-2 py-0.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded-md">
                                        WhatsApp
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">

                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">
                        Daftar Pelanggan ({{ $customers->count() }})
                    </h3>
                </div>

                @if ($customers->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        #</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Nama</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Telepon</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Ulang Tahun</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Bergabung</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($customers as $i => $customer)
                                    @php
                                        $daysUntil = null;
                                        $isBirthday = false;
                                        $isSoon = false;

                                        if ($customer->ulang_tahun) {
                                            $daysUntil = daysUntilBirthday($customer->ulang_tahun);
                                            $isBirthday = $daysUntil === 0;
                                            $isSoon = $daysUntil <= 7;
                                        }

                                        $phone = preg_replace('/[^0-9]/', '', $customer->phone ?? '');
                                        if (str_starts_with($phone, '0')) {
                                            $phone = '62' . substr($phone, 1);
                                        }
                                        $waMsg = urlencode(
                                            "Halo {$customer->name}! 🎂 Selamat ulang tahun ya! Semoga hari-harimu selalu menyenangkan. Kami di sini senang bisa melayanimu. 🎁",
                                        );
                                    @endphp
                                    <tr
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ $isBirthday ? 'bg-pink-50 dark:bg-pink-900/10' : '' }}">
                                        <td class="px-5 py-3.5 text-gray-400">{{ $i + 1 }}</td>
                                        <td class="px-5 py-3.5 font-medium text-gray-800 dark:text-gray-200">
                                            {{ $customer->name }}
                                            @if ($isBirthday)
                                                <span class="ml-1">🎂</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">
                                            {{ $customer->phone ?? '—' }}
                                        </td>
                                        <td class="px-5 py-3.5">
                                            @if ($customer->ulang_tahun)
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    {{-- Tanggal lahir lengkap --}}
                                                    <span class="text-gray-600 dark:text-gray-400">
                                                        {{ \Carbon\Carbon::parse($customer->ulang_tahun)->format('d M Y') }}
                                                    </span>

                                                    @if ($isBirthday)
                                                        <span
                                                            class="px-1.5 py-0.5 bg-pink-100 text-pink-600 text-xs rounded-md font-semibold">
                                                            🎉 Hari ini!
                                                        </span>
                                                        @if ($phone)
                                                            <a href="https://wa.me/{{ $phone }}?text={{ $waMsg }}"
                                                                target="_blank"
                                                                class="px-2 py-0.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded-md">
                                                                Ucapkan
                                                            </a>
                                                        @endif
                                                    @elseif ($isSoon)
                                                        <span
                                                            class="px-1.5 py-0.5 bg-amber-100 text-amber-600 text-xs rounded-md font-medium">
                                                            {{ $daysUntil }} hari lagi
                                                        </span>
                                                        @if ($phone)
                                                            <a href="https://wa.me/{{ $phone }}?text={{ $waMsg }}"
                                                                target="_blank"
                                                                class="px-2 py-0.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded-md">
                                                                WA
                                                            </a>
                                                        @endif
                                                    @else
                                                        {{-- Tampilkan sisa hari meski jauh --}}
                                                        <span class="text-xs text-gray-400">
                                                            {{ $daysUntil }} hari lagi
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">
                                            {{ $customer->created_at->format('d M Y') }}
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <div class="flex gap-2">
                                                <a href="{{ route('admin.customers.membership.index', $customer) }}"
                                                    class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-xs font-medium rounded-lg">
                                                    🎖 Membership
                                                </a>
                                                <a href="{{ route('admin.customers.edit', $customer) }}"
                                                    class="px-3 py-1 bg-amber-50 hover:bg-amber-100 text-amber-600 text-xs font-medium rounded-lg">
                                                    Edit
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('admin.customers.destroy', $customer) }}"
                                                    onsubmit="return confirm('Hapus pelanggan ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="px-3 py-1 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-medium rounded-lg">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-16 text-gray-400 text-sm">
                        Belum ada pelanggan.
                        <a href="{{ route('admin.customers.create') }}" class="text-indigo-500 hover:underline">Tambah
                            sekarang</a>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
