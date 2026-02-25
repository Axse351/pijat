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
                function daysUntilBirthday($ulang_tahun): int
                {
                    $today = \Carbon\Carbon::today();
                    $bday = \Carbon\Carbon::createFromFormat('Y-m-d', $ulang_tahun)->setYear($today->year);
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

                $inactiveCustomers = $customers->filter(fn($c) => $c->is_inactive);
            @endphp

            {{-- Banner: Ulang Tahun --}}
            @if ($soonBirthdays->count())
                <div class="mb-4 p-4 bg-pink-50 border border-pink-200 rounded-xl">
                    <p class="text-xs font-semibold text-pink-500 uppercase tracking-wider mb-2">🎂 Ulang Tahun Dalam 7
                        Hari</p>
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
                                        class="px-2 py-0.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded-md">WhatsApp</a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Banner: Pelanggan Tidak Aktif --}}
            @if ($inactiveCustomers->count())
                <div class="mb-4 p-4 bg-orange-50 border border-orange-200 rounded-xl">
                    <p class="text-xs font-semibold text-orange-500 uppercase tracking-wider mb-2">
                        😴 Pelanggan Tidak Aktif (Terakhir kunjungan &gt; 30 hari)
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($inactiveCustomers as $c)
                            @php
                                $phone = preg_replace('/[^0-9]/', '', $c->phone ?? '');
                                if (str_starts_with($phone, '0')) {
                                    $phone = '62' . substr($phone, 1);
                                }
                                $lastVisitFormatted = \Carbon\Carbon::parse($c->last_visit)->diffForHumans();
                                $msg = urlencode(
                                    "Halo {$c->name}! 👋 Kami kangen nih sama kamu. Sudah lama nggak ke sini, yuk mampir lagi! Ada promo menarik menanti. 😊",
                                );
                            @endphp
                            <div
                                class="flex items-center gap-2 px-3 py-2 bg-white border border-orange-200 rounded-lg text-sm">
                                <span class="font-medium text-gray-800">{{ $c->name }}</span>
                                <span class="text-xs text-orange-500">{{ $lastVisitFormatted }}</span>
                                @if ($phone)
                                    <a href="https://wa.me/{{ $phone }}?text={{ $msg }}" target="_blank"
                                        class="px-2 py-0.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded-md">
                                        Ajak Kembali
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
                                        Kunjungan</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Terakhir Datang</th>
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

                                        $waBdayMsg = urlencode(
                                            "Halo {$customer->name}! 🎂 Selamat ulang tahun ya! Semoga hari-harimu selalu menyenangkan. Kami di sini senang bisa melayanimu. 🎁",
                                        );

                                        $waInactiveMsg = urlencode(
                                            "Halo {$customer->name}! 👋 Kami kangen nih sama kamu. Sudah lama nggak ke sini, yuk mampir lagi! Ada promo menarik menanti. 😊",
                                        );

                                        // Badge kunjungan
                                        $visitBadge = match (true) {
                                            $customer->visit_count === 0 => [
                                                'bg-gray-100 text-gray-400',
                                                'Belum pernah',
                                            ],
                                            $customer->visit_count < 5 => [
                                                'bg-blue-100 text-blue-600',
                                                $customer->visit_count . 'x',
                                            ],
                                            $customer->visit_count < 10 => [
                                                'bg-indigo-100 text-indigo-600',
                                                $customer->visit_count . 'x',
                                            ],
                                            default => [
                                                'bg-amber-100 text-amber-600',
                                                $customer->visit_count . 'x ⭐',
                                            ],
                                        };
                                    @endphp
                                    <tr
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700/50
                                        {{ $isBirthday ? 'bg-pink-50 dark:bg-pink-900/10' : '' }}
                                        {{ $customer->is_inactive ? 'bg-orange-50/40 dark:bg-orange-900/5' : '' }}">

                                        <td class="px-5 py-3.5 text-gray-400">{{ $i + 1 }}</td>

                                        {{-- Nama --}}
                                        <td class="px-5 py-3.5 font-medium text-gray-800 dark:text-gray-200">
                                            {{ $customer->name }}
                                            @if ($isBirthday)
                                                <span class="ml-1">🎂</span>
                                            @endif
                                            @if ($customer->is_inactive)
                                                <span class="ml-1 text-xs text-orange-400"
                                                    title="Tidak aktif > 30 hari">😴</span>
                                            @endif
                                        </td>

                                        {{-- Telepon --}}
                                        <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">
                                            {{ $customer->phone ?? '—' }}
                                        </td>

                                        {{-- Kunjungan --}}
                                        <td class="px-5 py-3.5">
                                            <span
                                                class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $visitBadge[0] }}">
                                                {{ $visitBadge[1] }}
                                            </span>
                                        </td>

                                        {{-- Terakhir Datang --}}
                                        <td class="px-5 py-3.5">
                                            @if ($customer->last_visit)
                                                <div class="text-gray-600 dark:text-gray-400 text-xs">
                                                    {{ \Carbon\Carbon::parse($customer->last_visit)->format('d M Y') }}
                                                </div>
                                                @if ($customer->is_inactive)
                                                    <div class="flex items-center gap-1.5 mt-1">
                                                        <span class="text-xs text-orange-500 font-medium">
                                                            {{ \Carbon\Carbon::parse($customer->last_visit)->diffForHumans() }}
                                                        </span>
                                                        @if ($phone)
                                                            <a href="https://wa.me/{{ $phone }}?text={{ $waInactiveMsg }}"
                                                                target="_blank"
                                                                class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded-md">
                                                                <svg class="w-3 h-3" viewBox="0 0 24 24"
                                                                    fill="currentColor">
                                                                    <path
                                                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z" />
                                                                    <path
                                                                        d="M12 0C5.373 0 0 5.373 0 12c0 2.124.554 4.118 1.528 5.847L0 24l6.335-1.507A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.891 0-3.659-.5-5.192-1.375l-.371-.22-3.762.895.952-3.67-.242-.38A9.955 9.955 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z" />
                                                                </svg>
                                                                Ajak Kembali
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endif
                                            @else
                                                <span class="text-gray-400 text-xs">—</span>
                                            @endif
                                        </td>

                                        {{-- Ulang Tahun --}}
                                        <td class="px-5 py-3.5">
                                            @if ($customer->ulang_tahun)
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="text-gray-600 dark:text-gray-400">
                                                        {{ \Carbon\Carbon::parse($customer->ulang_tahun)->format('d M Y') }}
                                                    </span>
                                                    @if ($isBirthday)
                                                        <span
                                                            class="px-1.5 py-0.5 bg-pink-100 text-pink-600 text-xs rounded-md font-semibold">🎉
                                                            Hari ini!</span>
                                                        @if ($phone)
                                                            <a href="https://wa.me/{{ $phone }}?text={{ $waBdayMsg }}"
                                                                target="_blank"
                                                                class="px-2 py-0.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded-md">Ucapkan</a>
                                                        @endif
                                                    @elseif ($isSoon)
                                                        <span
                                                            class="px-1.5 py-0.5 bg-amber-100 text-amber-600 text-xs rounded-md font-medium">{{ $daysUntil }}
                                                            hari lagi</span>
                                                        @if ($phone)
                                                            <a href="https://wa.me/{{ $phone }}?text={{ $waBdayMsg }}"
                                                                target="_blank"
                                                                class="px-2 py-0.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded-md">WA</a>
                                                        @endif
                                                    @else
                                                        <span class="text-xs text-gray-400">{{ $daysUntil }} hari
                                                            lagi</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>

                                        {{-- Bergabung --}}
                                        <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">
                                            {{ $customer->created_at->format('d M Y') }}
                                        </td>

                                        {{-- Aksi --}}
                                        <td class="px-5 py-3.5">
                                            <div class="flex gap-2 flex-wrap">
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
