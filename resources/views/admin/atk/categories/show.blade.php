<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Detail Kategori: {{ $atkCategory->name }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.atk-categories.edit', $atkCategory) }}"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                    Edit
                </a>
                <a href="{{ route('admin.atk-categories.index') }}"
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-white text-sm font-medium rounded-lg transition">
                    ← Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Info Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-6 mb-6">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Kode</p>
                        <p class="text-2xl font-bold font-mono text-indigo-600 dark:text-indigo-400">{{ $atkCategory->code }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Nama Kategori</p>
                        <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ $atkCategory->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Jumlah Item</p>
                        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $atkCategory->atks->count() }} item</p>
                    </div>
                </div>
                @if ($atkCategory->description)
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <p class="text-xs text-gray-500 mb-1">Deskripsi</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $atkCategory->description }}</p>
                    </div>
                @endif
            </div>

            <!-- Daftar Item ATK dalam kategori ini -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">Item ATK dalam Kategori Ini</h3>
                    <a href="{{ route('admin.atk-items.create') }}"
                        class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">
                        + Tambah Item
                    </a>
                </div>

                @if ($atkCategory->atks->count())
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700/50">
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Item</th>
                                <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Stok</th>
                                <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga Terakhir</th>
                                <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($atkCategory->atks as $atk)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-5 py-3.5 font-mono text-gray-500 dark:text-gray-400 text-xs">{{ $atk->code }}</td>
                                    <td class="px-5 py-3.5 font-medium text-gray-800 dark:text-gray-200">{{ $atk->name }}</td>
                                    <td class="px-5 py-3.5 text-right">
                                        @if ($atk->stock < 5)
                                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">{{ $atk->stock }}</span>
                                        @else
                                            <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-semibold">{{ $atk->stock }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-right text-amber-600 dark:text-amber-400 font-semibold text-xs">
                                        {{ $atk->last_purchase_price ? 'Rp ' . number_format($atk->last_purchase_price, 0, ',', '.') : '—' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <a href="{{ route('admin.atk-items.show', $atk) }}"
                                            class="text-indigo-600 hover:text-indigo-800 text-xs font-semibold">Lihat</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-12 text-gray-400 text-sm">
                        Belum ada item ATK di kategori ini.
                        <a href="{{ route('admin.atk-items.create') }}" class="text-indigo-600 hover:underline ml-1">Tambah sekarang</a>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
