<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Master ATK</h2>
            <a href="{{ route('admin.atk-items.create') }}"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">+ Tambah Item</a>
        </div>
    </x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">✓
                    {{ session('success') }}</div>
            @endif

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $atks->count() }}</div>
                    <div class="text-xs text-gray-500 mt-1">Total Item ATK</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                    <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $atks->where('stock', '<', 5)->count() }}</div>
                    <div class="text-xs text-gray-500 mt-1">Stok Rendah (&lt;5)</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">Rp
                        {{ number_format($atks->sum('stock') * 50000, 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-gray-500 mt-1">Estimasi Nilai Stok</div>
                </div>
            </div>

            <!-- Filter -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kategori</label>
                        <select name="category_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Filter Stok</label>
                        <select name="low_stock" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="">Semua Item</option>
                            <option value="1" {{ request('low_stock') ? 'selected' : '' }}>Hanya Stok Rendah</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cari</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/code..."
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition">
                            Filter
                        </button>
                        <a href="{{ route('admin.atk-items.index') }}" class="flex-1 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-white px-3 py-2 rounded-lg text-sm font-medium transition text-center">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">Daftar Item ATK</h3>
                </div>
                @if ($atks->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Item</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Stok</th>
                                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga Terakhir</th>
                                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($atks as $i => $atk)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-5 py-3.5 text-gray-400">{{ $i + 1 }}</td>
                                        <td class="px-5 py-3.5 font-medium text-gray-800 dark:text-gray-200">
                                            {{ $atk->name }}
                                        </td>
                                        <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400 font-mono">
                                            {{ $atk->code }}
                                        </td>
                                        <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">
                                            <span class="px-2.5 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-xs">
                                                {{ $atk->category->name ?? '—' }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5 text-right">
                                            @if ($atk->stock < 5)
                                                <span class="px-2.5 py-1 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 rounded-full text-xs font-semibold">
                                                    {{ $atk->stock }} unit
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 rounded-full text-xs font-semibold">
                                                    {{ $atk->stock }} unit
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-right font-semibold text-amber-600 dark:text-amber-400">
                                            {{ $atk->last_purchase_price ? 'Rp ' . number_format($atk->last_purchase_price, 0, ',', '.') : '—' }}
                                        </td>
                                        <td class="px-5 py-3.5 text-center">
                                            @if ($atk->stock > 0)
                                                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 rounded-full text-xs font-semibold">
                                                    Tersedia
                                                </span>
                                            @else
                                                <span class="px-2 py-1 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 rounded-full text-xs font-semibold">
                                                    Habis
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-center">
                                            <div class="flex gap-2 justify-center">
                                                <a href="{{ route('admin.atk-items.show', $atk) }}"
                                                    class="text-indigo-600 hover:text-indigo-800 dark:hover:text-indigo-400 text-xs font-semibold">Lihat</a>
                                                <a href="{{ route('admin.atk-items.edit', $atk) }}"
                                                    class="text-blue-600 hover:text-blue-800 dark:hover:text-blue-400 text-xs font-semibold">Edit</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-16 text-gray-400 text-sm">Belum ada item ATK.</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
