<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Membership — {{ $customer->name }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.customers.index') }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg">
                    ← Kembali
                </a>
                <a href="{{ route('admin.customers.membership.create', $customer) }}"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                    + Beri Membership
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">
                    ✓ {{ session('success') }}
                </div>
            @endif

            {{-- Banner WA ucapan membership baru --}}
            @if (session('welcome_membership'))
                @php
                    $wm = session('welcome_membership');
                    $wmPhone = \App\Models\WaMessageTemplate::normalizePhone($wm['phone'] ?? '');
                    $wmText =
                        "Halo {$wm['customer_name']}! 🎉\n\n" .
                        "Selamat, kamu sekarang member *{$wm['membership_name']}* di Koichi.\n" .
                        "Berlaku sampai *{$wm['end_date']}*.\n\n" .
                        "Terima kasih & sampai jumpa! 🌸\n_— Tim Koichi_";
                @endphp
                <div
                    class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center justify-between gap-3">
                    <p class="text-sm text-green-700">Kirim ucapan membership ke {{ $wm['customer_name'] }}?</p>
                    @if ($wmPhone)
                        <a href="https://wa.me/{{ $wmPhone }}?text={{ urlencode($wmText) }}" target="_blank"
                            class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded-lg">
                            📱 Kirim WhatsApp
                        </a>
                    @else
                        <span class="text-xs text-gray-400">Nomor telepon tidak tersedia</span>
                    @endif
                </div>
            @endif

            {{-- Membership aktif --}}
            <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-5">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-3">Membership Aktif</h3>

                @if ($activeMembership)
                    @php
                        $end = \Carbon\Carbon::parse($activeMembership->end_date);
                        $expired = $end->isPast();
                    @endphp
                    <div class="flex flex-wrap items-center gap-4">
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-sm font-semibold rounded-lg">
                            {{ $activeMembership->membership->name ?? '—' }}
                        </span>
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            {{ \Carbon\Carbon::parse($activeMembership->start_date)->format('d M Y') }}
                            s/d {{ $end->format('d M Y') }}
                        </span>
                        @if ($expired)
                            <span
                                class="px-2 py-0.5 bg-red-100 text-red-600 text-xs font-semibold rounded-full">Kadaluarsa</span>
                        @else
                            <span
                                class="px-2 py-0.5 bg-emerald-100 text-emerald-600 text-xs font-semibold rounded-full">
                                {{ (int) now()->startOfDay()->diffInDays($end->startOfDay()) }} hari lagi
                            </span>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-gray-400">Belum ada membership aktif.</p>
                @endif
            </div>

            {{-- Riwayat --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">Riwayat Membership
                        ({{ $histories->count() }})</h3>
                </div>

                @if ($histories->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    @foreach (['#', 'Membership', 'Mulai', 'Berakhir', 'Status', 'Aksi'] as $th)
                                        <th
                                            class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            {{ $th }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($histories as $i => $h)
                                    @php
                                        $hEnd = \Carbon\Carbon::parse($h->end_date);
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-5 py-3 text-gray-400">{{ $i + 1 }}</td>
                                        <td class="px-5 py-3 font-medium text-gray-800 dark:text-gray-200">
                                            {{ $h->membership->name ?? '—' }}</td>
                                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($h->start_date)->format('d M Y') }}</td>
                                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">
                                            {{ $hEnd->format('d M Y') }}</td>
                                        <td class="px-5 py-3">
                                            @if ($h->is_active && !$hEnd->isPast())
                                                <span
                                                    class="px-2 py-0.5 bg-emerald-100 text-emerald-600 text-xs font-semibold rounded-full">Aktif</span>
                                            @elseif ($hEnd->isPast())
                                                <span
                                                    class="px-2 py-0.5 bg-red-100 text-red-600 text-xs font-semibold rounded-full">Kadaluarsa</span>
                                            @else
                                                <span
                                                    class="px-2 py-0.5 bg-gray-100 text-gray-500 text-xs font-semibold rounded-full">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3">
                                            <div class="flex gap-2">
                                                <a href="{{ route('admin.customers.membership.edit', [$customer, $h]) }}"
                                                    class="px-3 py-1 bg-amber-50 hover:bg-amber-100 text-amber-600 text-xs font-medium rounded-lg">Edit</a>
                                                <form method="POST"
                                                    action="{{ route('admin.customers.membership.destroy', [$customer, $h]) }}"
                                                    onsubmit="return confirm('Hapus membership ini?')">
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
                    <div class="text-center py-10 text-gray-400 text-sm">Belum ada riwayat membership.</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout><x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Membership — {{ $customer->name }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.customers.index') }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg">
                    ← Kembali
                </a>
                <a href="{{ route('admin.customers.membership.create', $customer) }}"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                    + Beri Membership
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">
                    ✓ {{ session('success') }}
                </div>
            @endif

            {{-- Banner WA ucapan membership baru --}}
            @if (session('welcome_membership'))
                @php
                    $wm = session('welcome_membership');
                    $wmPhone = \App\Models\WaMessageTemplate::normalizePhone($wm['phone'] ?? '');
                    $wmText =
                        "Halo {$wm['customer_name']}! 🎉\n\n" .
                        "Selamat, kamu sekarang member *{$wm['membership_name']}* di Koichi.\n" .
                        "Berlaku sampai *{$wm['end_date']}*.\n\n" .
                        "Terima kasih & sampai jumpa! 🌸\n_— Tim Koichi_";
                @endphp
                <div
                    class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center justify-between gap-3">
                    <p class="text-sm text-green-700">Kirim ucapan membership ke {{ $wm['customer_name'] }}?</p>
                    @if ($wmPhone)
                        <a href="https://wa.me/{{ $wmPhone }}?text={{ urlencode($wmText) }}" target="_blank"
                            class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded-lg">
                            📱 Kirim WhatsApp
                        </a>
                    @else
                        <span class="text-xs text-gray-400">Nomor telepon tidak tersedia</span>
                    @endif
                </div>
            @endif

            {{-- Membership aktif --}}
            <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-5">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-3">Membership Aktif</h3>

                @if ($activeMembership)
                    @php
                        $end = \Carbon\Carbon::parse($activeMembership->end_date);
                        $expired = $end->isPast();
                    @endphp
                    <div class="flex flex-wrap items-center gap-4">
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-sm font-semibold rounded-lg">
                            {{ $activeMembership->membership->name ?? '—' }}
                        </span>
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            {{ \Carbon\Carbon::parse($activeMembership->start_date)->format('d M Y') }}
                            s/d {{ $end->format('d M Y') }}
                        </span>
                        @if ($expired)
                            <span
                                class="px-2 py-0.5 bg-red-100 text-red-600 text-xs font-semibold rounded-full">Kadaluarsa</span>
                        @else
                            <span
                                class="px-2 py-0.5 bg-emerald-100 text-emerald-600 text-xs font-semibold rounded-full">
                                {{ (int) now()->startOfDay()->diffInDays($end->startOfDay()) }} hari lagi
                            </span>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-gray-400">Belum ada membership aktif.</p>
                @endif
            </div>

            {{-- Riwayat --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">Riwayat Membership
                        ({{ $histories->count() }})</h3>
                </div>

                @if ($histories->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    @foreach (['#', 'Membership', 'Mulai', 'Berakhir', 'Status', 'Aksi'] as $th)
                                        <th
                                            class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            {{ $th }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($histories as $i => $h)
                                    @php
                                        $hEnd = \Carbon\Carbon::parse($h->end_date);
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-5 py-3 text-gray-400">{{ $i + 1 }}</td>
                                        <td class="px-5 py-3 font-medium text-gray-800 dark:text-gray-200">
                                            {{ $h->membership->name ?? '—' }}</td>
                                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($h->start_date)->format('d M Y') }}</td>
                                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">
                                            {{ $hEnd->format('d M Y') }}</td>
                                        <td class="px-5 py-3">
                                            @if ($h->is_active && !$hEnd->isPast())
                                                <span
                                                    class="px-2 py-0.5 bg-emerald-100 text-emerald-600 text-xs font-semibold rounded-full">Aktif</span>
                                            @elseif ($hEnd->isPast())
                                                <span
                                                    class="px-2 py-0.5 bg-red-100 text-red-600 text-xs font-semibold rounded-full">Kadaluarsa</span>
                                            @else
                                                <span
                                                    class="px-2 py-0.5 bg-gray-100 text-gray-500 text-xs font-semibold rounded-full">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3">
                                            <div class="flex gap-2">
                                                <a href="{{ route('admin.customers.membership.edit', [$customer, $h]) }}"
                                                    class="px-3 py-1 bg-amber-50 hover:bg-amber-100 text-amber-600 text-xs font-medium rounded-lg">Edit</a>
                                                <form method="POST"
                                                    action="{{ route('admin.customers.membership.destroy', [$customer, $h]) }}"
                                                    onsubmit="return confirm('Hapus membership ini?')">
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
                    <div class="text-center py-10 text-gray-400 text-sm">Belum ada riwayat membership.</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
