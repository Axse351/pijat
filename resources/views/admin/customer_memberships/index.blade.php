<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Pelanggan</p>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $customer->user->name }}
                </h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.customers.index') }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                    ← Kembali
                </a>
                <a href="{{ route('admin.customers.membership.create', $customer) }}"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                    + Assign Membership
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if (session('success'))
                <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">
                    ✓ {{ session('success') }}
                </div>
            @endif

            {{-- Card membership aktif --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Membership Aktif</p>

                @if ($activeMembership)
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                                {{ $activeMembership->membership->name }}
                            </span>
                            <p class="text-sm text-gray-500 mt-0.5">
                                {{ $activeMembership->start_date->format('d M Y') }}
                                –
                                {{ $activeMembership->end_date->format('d M Y') }}
                            </p>
                        </div>
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full">
                            Aktif
                        </span>
                    </div>
                @else
                    <p class="text-sm text-gray-400">Tidak ada membership aktif saat ini.</p>
                @endif
            </div>

            {{-- Riwayat --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">Riwayat Membership</h3>
                </div>

                @if ($histories->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Tipe</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Mulai</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Berakhir</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($histories as $item)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-5 py-3.5 font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $item->membership->name }}
                                        </td>
                                        <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">
                                            {{ $item->start_date->format('d M Y') }}
                                        </td>
                                        <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">
                                            {{ $item->end_date->format('d M Y') }}
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <span
                                                class="px-2 py-1 rounded-full text-xs font-semibold
                                                {{ $item->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                                {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <div class="flex gap-2">
                                                <a href="{{ route('admin.customers.membership.edit', [$customer, $item]) }}"
                                                    class="px-3 py-1 bg-amber-50 hover:bg-amber-100 text-amber-600 text-xs font-medium rounded-lg">
                                                    Edit
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('admin.customers.membership.destroy', [$customer, $item]) }}"
                                                    onsubmit="return confirm('Hapus membership ini?')">
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
                    <div class="text-center py-12 text-gray-400 text-sm">
                        Belum ada riwayat membership.
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
