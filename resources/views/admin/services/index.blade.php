<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Layanan</h2>
            <a href="{{ route('admin.services.create') }}"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                + Tambah Layanan
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if (session('success'))
                <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">
                    ✓ {{ session('success') }}
                </div>
            @endif

            @php
                $grouped = $services->groupBy('category');
                $categoryColors = [
                    'Refleksi' => [
                        'bg' => 'bg-indigo-50 dark:bg-indigo-900/20',
                        'badge' => 'bg-indigo-100 text-indigo-700',
                        'dot' => 'bg-indigo-400',
                    ],
                    'Minuman' => [
                        'bg' => 'bg-cyan-50 dark:bg-cyan-900/20',
                        'badge' => 'bg-cyan-100 text-cyan-700',
                        'dot' => 'bg-cyan-400',
                    ],
                ];
            @endphp

            @forelse ($grouped as $category => $items)
                @php $color = $categoryColors[$category] ?? ['bg' => 'bg-gray-50', 'badge' => 'bg-gray-100 text-gray-600', 'dot' => 'bg-gray-400']; @endphp

                <div
                    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    {{-- Category header --}}
                    <div
                        class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3 {{ $color['bg'] }}">
                        <span class="w-2.5 h-2.5 rounded-full {{ $color['dot'] }}"></span>
                        <span class="font-bold text-gray-700 dark:text-gray-200">{{ $category }}</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $color['badge'] }}">
                            {{ $items->count() }} layanan
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider w-8">
                                        #</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Nama Layanan</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        SKU</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Durasi</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Harga</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Poin ⭐</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($items as $i => $service)
                                    <tr
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ !$service->is_active ? 'opacity-50' : '' }}">
                                        <td class="px-5 py-3.5 text-gray-400 text-xs">{{ $i + 1 }}</td>
                                        <td class="px-5 py-3.5">
                                            <div class="font-medium text-gray-800 dark:text-gray-200">
                                                {{ $service->name }}</div>
                                            @if ($service->description)
                                                <div class="text-xs text-gray-400 mt-0.5 max-w-xs truncate">
                                                    {{ $service->description }}</div>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5">
                                            @if ($service->sku)
                                                <span
                                                    class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-xs font-mono rounded">
                                                    {{ $service->sku }}
                                                </span>
                                            @else
                                                <span class="text-gray-300">—</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">
                                            {{ $service->duration ? $service->duration . ' menit' : '—' }}
                                        </td>
                                        <td class="px-5 py-3.5 font-semibold text-amber-600 dark:text-amber-400">
                                            Rp {{ number_format($service->price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-5 py-3.5">
                                            @if ($service->reward_points && $service->reward_points > 0)
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                                                    ⭐ +{{ $service->reward_points }}
                                                </span>
                                            @else
                                                <span class="text-gray-300 text-xs">—</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <span
                                                class="px-2 py-0.5 rounded-full text-xs font-semibold
                                                {{ $service->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-400' }}">
                                                {{ $service->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <div class="flex gap-2">
                                                <a href="{{ route('admin.services.edit', $service) }}"
                                                    class="px-3 py-1 bg-amber-50 hover:bg-amber-100 text-amber-600 text-xs font-medium rounded-lg">
                                                    Edit
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('admin.services.destroy', $service) }}"
                                                    onsubmit="return confirm('Hapus layanan ini?')">
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
                </div>
            @empty
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 py-16 text-center">
                    <p class="text-gray-400 text-sm">Belum ada layanan.
                        <a href="{{ route('admin.services.create') }}" class="text-indigo-500 hover:underline">Tambah
                            sekarang</a>
                    </p>
                </div>
            @endforelse

            {{-- Summary --}}
            <div class="text-right text-xs text-gray-400">
                Total: {{ $services->count() }} layanan
                ({{ $services->where('is_active', true)->count() }} aktif)
            </div>

        </div>
    </div>
</x-app-layout>
