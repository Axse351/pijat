<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Pelanggan</h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.wa-templates.index') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-2 bg-green-50 hover:bg-green-100 text-green-600 border border-green-200 text-sm font-medium rounded-lg transition-colors">
                    📱 Template WA
                </a>
                <a href="{{ route('admin.customers.create') }}"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                    + Tambah Pelanggan
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">
                    ✓ {{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">✗
                    {{ session('error') }}</div>
            @endif

            @php
                // ── Guard agar fungsi tidak dideklarasi ulang saat view di-cache ──
                // ── Carbon::parse() digunakan agar aman untuk format datetime maupun date saja ──
                if (!function_exists('daysUntilBirthday')) {
                    function daysUntilBirthday($ulang_tahun): int
                    {
                        $today = \Carbon\Carbon::today();
                        // parse() toleran terhadap '1990-05-15' maupun '1990-05-15 00:00:00'
                        $bday = \Carbon\Carbon::parse($ulang_tahun)->setYear($today->year)->startOfDay();
                        if ($bday->lt($today)) {
                            $bday->addYear();
                        }
                        return (int) $today->diffInDays($bday);
                    }
                }

                $soonBirthdays = $customers->filter(
                    fn($c) => $c->ulang_tahun && ($c->_days_until_bday = daysUntilBirthday($c->ulang_tahun)) <= 7,
                );
                $inactiveCustomers = $customers->filter(fn($c) => $c->is_inactive);
                $bonusReadyCustomers = $customers->filter(fn($c) => $c->hasBonus());

                // ── Ambil template WA sekali saja ──
                $waTemplates = \App\Models\WaMessageTemplate::whereIn('key', [
                    'customer_birthday',
                    'customer_reactivation',
                    'customer_bonus_ready',
                ])
                    ->where('is_active', true)
                    ->pluck('template', 'key');
            @endphp

            {{-- Banner: Bonus siap klaim --}}
            @if ($bonusReadyCustomers->count())
                <div class="mb-4 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                    <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider mb-2">🎁 Pelanggan Siap
                        Klaim Bonus Gratis 1 Jam! (Poin ≥ 10)</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($bonusReadyCustomers as $c)
                            @php
                                $phone = \App\Models\WaMessageTemplate::normalizePhone($c->phone ?? '');
                                $waMsg = \App\Models\WaMessageTemplate::render('customer_bonus_ready', [
                                    'nama_pelanggan' => $c->name,
                                ]);
                                $waLink = $phone && $waMsg ? "https://wa.me/{$phone}?text=" . urlencode($waMsg) : null;
                            @endphp
                            <div
                                class="flex items-center gap-2 px-3 py-2 bg-white border border-amber-200 rounded-lg text-sm shadow-sm">
                                <span class="font-medium text-gray-800">{{ $c->name }}</span>
                                <span
                                    class="px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">{{ $c->points }}
                                    poin</span>
                                @if ($waLink)
                                    <a href="{{ $waLink }}" target="_blank"
                                        class="px-2 py-0.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded-md">📱
                                        WA</a>
                                @endif
                                <form method="POST" action="{{ route('admin.customers.redeem-bonus', $c) }}"
                                    onsubmit="return confirm('Klaim bonus gratis 1 jam untuk {{ $c->name }}?')">
                                    @csrf
                                    <button type="submit"
                                        class="px-2 py-0.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-medium rounded-md">🎁
                                        Klaim</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Banner: Ulang Tahun --}}
            @if ($soonBirthdays->count())
                <div class="mb-4 p-4 bg-pink-50 border border-pink-200 rounded-xl">
                    <p class="text-xs font-semibold text-pink-500 uppercase tracking-wider mb-2">🎂 Ulang Tahun Dalam 7
                        Hari</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($soonBirthdays as $c)
                            @php
                                $phone = \App\Models\WaMessageTemplate::normalizePhone($c->phone ?? '');
                                $waMsg = \App\Models\WaMessageTemplate::render('customer_birthday', [
                                    'nama_pelanggan' => $c->name,
                                ]);
                                $waLink = $phone && $waMsg ? "https://wa.me/{$phone}?text=" . urlencode($waMsg) : null;
                                $label =
                                    $c->_days_until_bday === 0 ? 'Hari ini! 🎉' : "{$c->_days_until_bday} hari lagi";
                            @endphp
                            <div
                                class="flex items-center gap-2 px-3 py-2 bg-white border border-pink-200 rounded-lg text-sm">
                                <span class="font-medium text-gray-800">{{ $c->name }}</span>
                                <span class="text-xs text-pink-500">{{ $label }}</span>
                                @if ($waLink)
                                    <a href="{{ $waLink }}" target="_blank"
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
                    <p class="text-xs font-semibold text-orange-500 uppercase tracking-wider mb-2">😴 Pelanggan Tidak
                        Aktif (Terakhir kunjungan &gt; 30 hari)</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($inactiveCustomers as $c)
                            @php
                                $phone = \App\Models\WaMessageTemplate::normalizePhone($c->phone ?? '');
                                $waMsg = \App\Models\WaMessageTemplate::render('customer_reactivation', [
                                    'nama_pelanggan' => $c->name,
                                ]);
                                $waLink = $phone && $waMsg ? "https://wa.me/{$phone}?text=" . urlencode($waMsg) : null;
                                $lastVisitFormatted = \Carbon\Carbon::parse($c->last_visit)->diffForHumans();
                            @endphp
                            <div
                                class="flex items-center gap-2 px-3 py-2 bg-white border border-orange-200 rounded-lg text-sm">
                                <span class="font-medium text-gray-800">{{ $c->name }}</span>
                                <span class="text-xs text-orange-500">{{ $lastVisitFormatted }}</span>
                                @if ($waLink)
                                    <a href="{{ $waLink }}" target="_blank"
                                        class="px-2 py-0.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded-md">Ajak
                                        Kembali</a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">Daftar Pelanggan
                        ({{ $customers->count() }})</h3>
                </div>

                @if ($customers->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    @foreach (['#', 'Nama', 'Telepon', 'Kunjungan', '🏆 Poin', 'Terakhir Datang', 'Ulang Tahun', 'Bergabung', 'Aksi'] as $th)
                                        <th
                                            class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            {{ $th }}</th>
                                    @endforeach
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

                                        $phone = \App\Models\WaMessageTemplate::normalizePhone($customer->phone ?? '');

                                        $waBdayUrl = $phone
                                            ? "https://wa.me/{$phone}?text=" .
                                                urlencode(
                                                    \App\Models\WaMessageTemplate::render('customer_birthday', [
                                                        'nama_pelanggan' => $customer->name,
                                                    ]),
                                                )
                                            : null;

                                        $waInactiveUrl = $phone
                                            ? "https://wa.me/{$phone}?text=" .
                                                urlencode(
                                                    \App\Models\WaMessageTemplate::render('customer_reactivation', [
                                                        'nama_pelanggan' => $customer->name,
                                                    ]),
                                                )
                                            : null;

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

                                        $pts = $customer->points ?? 0;
                                        $hasBonus = $customer->hasBonus();
                                        $progressPct = $hasBonus ? 100 : ($pts / 10) * 100;
                                    @endphp
                                    <tr
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700/50
                                        {{ $isBirthday ? 'bg-pink-50 dark:bg-pink-900/10' : '' }}
                                        {{ $customer->is_inactive && !$isBirthday ? 'bg-orange-50/40 dark:bg-orange-900/5' : '' }}
                                        {{ $hasBonus && !$isBirthday ? 'bg-amber-50/20' : '' }}">

                                        <td class="px-5 py-3.5 text-gray-400">{{ $i + 1 }}</td>

                                        <td class="px-5 py-3.5 font-medium text-gray-800 dark:text-gray-200">
                                            {{ $customer->name }}
                                            @if ($isBirthday)
                                                <span>🎂</span>
                                            @endif
                                            @if ($customer->is_inactive)
                                                <span class="text-xs text-orange-400"
                                                    title="Tidak aktif > 30 hari">😴</span>
                                            @endif
                                            @if ($hasBonus)
                                                <span title="Bonus siap diklaim!">🎁</span>
                                            @endif
                                        </td>

                                        <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">
                                            {{ $customer->phone ?? '—' }}</td>

                                        <td class="px-5 py-3.5">
                                            <span
                                                class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $visitBadge[0] }}">{{ $visitBadge[1] }}</span>
                                        </td>

                                        <td class="px-5 py-3.5">
                                            <div class="flex flex-col gap-1.5 min-w-[130px]">
                                                <div class="flex items-center justify-between gap-2">
                                                    <span
                                                        class="text-xs font-bold {{ $hasBonus ? 'text-amber-600' : 'text-gray-700 dark:text-gray-300' }}">{{ $pts }}
                                                        poin</span>
                                                    @if ($hasBonus)
                                                        <span
                                                            class="px-1.5 py-0.5 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full whitespace-nowrap">🎁
                                                            Bonus!</span>
                                                    @else
                                                        <span
                                                            class="text-xs text-gray-400">{{ $pts }}/10</span>
                                                    @endif
                                                </div>
                                                <div
                                                    class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                                    <div class="h-2 rounded-full transition-all duration-500 {{ $hasBonus ? 'bg-gradient-to-r from-amber-400 to-amber-500' : 'bg-gradient-to-r from-indigo-400 to-indigo-500' }}"
                                                        style="width: {{ $progressPct }}%"></div>
                                                </div>
                                                @if ($hasBonus)
                                                    <form method="POST"
                                                        action="{{ route('admin.customers.redeem-bonus', $customer) }}"
                                                        onsubmit="return confirm('Klaim bonus untuk {{ addslashes($customer->name) }}?')">
                                                        @csrf
                                                        <button type="submit"
                                                            class="w-full text-xs px-2 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-medium transition-colors text-center">Klaim
                                                            Gratis 1 Jam</button>
                                                    </form>
                                                @endif
                                                @if ($customer->total_points_earned > 0)
                                                    <span class="text-xs text-gray-400">Total:
                                                        {{ $customer->total_points_earned }} poin</span>
                                                @endif
                                            </div>
                                        </td>

                                        <td class="px-5 py-3.5">
                                            @if ($customer->last_visit)
                                                <div class="text-gray-600 dark:text-gray-400 text-xs">
                                                    {{ \Carbon\Carbon::parse($customer->last_visit)->format('d M Y') }}
                                                </div>
                                                @if ($customer->is_inactive)
                                                    <div class="flex items-center gap-1.5 mt-1">
                                                        <span
                                                            class="text-xs text-orange-500 font-medium">{{ \Carbon\Carbon::parse($customer->last_visit)->diffForHumans() }}</span>
                                                        @if ($waInactiveUrl)
                                                            <a href="{{ $waInactiveUrl }}" target="_blank"
                                                                class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded-md">Ajak
                                                                Kembali</a>
                                                        @endif
                                                    </div>
                                                @endif
                                            @else
                                                <span class="text-gray-400 text-xs">—</span>
                                            @endif
                                        </td>

                                        <td class="px-5 py-3.5">
                                            @if ($customer->ulang_tahun)
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span
                                                        class="text-gray-600 dark:text-gray-400">{{ \Carbon\Carbon::parse($customer->ulang_tahun)->format('d M Y') }}</span>
                                                    @if ($isBirthday)
                                                        <span
                                                            class="px-1.5 py-0.5 bg-pink-100 text-pink-600 text-xs rounded-md font-semibold">🎉
                                                            Hari ini!</span>
                                                        @if ($waBdayUrl)
                                                            <a href="{{ $waBdayUrl }}" target="_blank"
                                                                class="px-2 py-0.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded-md">Ucapkan</a>
                                                        @endif
                                                    @elseif ($isSoon)
                                                        <span
                                                            class="px-1.5 py-0.5 bg-amber-100 text-amber-600 text-xs rounded-md font-medium">{{ $daysUntil }}
                                                            hari lagi</span>
                                                        @if ($waBdayUrl)
                                                            <a href="{{ $waBdayUrl }}" target="_blank"
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

                                        <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">
                                            {{ $customer->created_at->format('d M Y') }}</td>

                                        <td class="px-5 py-3.5">
                                            <div class="flex gap-2 flex-wrap">
                                                <a href="{{ route('admin.customers.membership.index', $customer) }}"
                                                    class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-xs font-medium rounded-lg">🎖
                                                    Membership</a>
                                                <a href="{{ route('admin.customers.edit', $customer) }}"
                                                    class="px-3 py-1 bg-amber-50 hover:bg-amber-100 text-amber-600 text-xs font-medium rounded-lg">Edit</a>
                                                <form method="POST"
                                                    action="{{ route('admin.customers.destroy', $customer) }}"
                                                    onsubmit="return confirm('Hapus pelanggan ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="px-3 py-1 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-medium rounded-lg">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-16 text-gray-400 text-sm">Belum ada pelanggan. <a
                            href="{{ route('admin.customers.create') }}"
                            class="text-indigo-500 hover:underline">Tambah sekarang</a></div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
