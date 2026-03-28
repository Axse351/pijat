<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Pembelian ATK</h2>
            <a href="{{ route('admin.atk-purchases.create') }}"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">+ Catat
                Pembelian</a>
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
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">Rp
                        {{ number_format($purchases->where('status', 'completed')->sum('total_price'), 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-gray-500 mt-1">Total Pembelian</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $purchases->where('status', 'completed')->count() }}</div>
                    <div class="text-xs text-gray-500 mt-1">Transaksi Selesai</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $purchases->where('status', 'pending')->count() }}</div>
                    <div class="text-xs text-gray-500 mt-1">Menunggu Konfirmasi</div>
                </div>
            </div>

            <!-- Filter -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
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
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="">Semua Status</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Batal</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition">
                            Filter
                        </button>
                        <a href="{{ route('admin.atk-purchases.index') }}" class="flex-1 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-white px-3 py-2 rounded-lg text-sm font-medium transition text-center">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">Daftar Pembelian ATK</h3>
                </div>
                @if ($purchases->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Item ATK</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Qty</th>
                                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($purchases as $i => $purchase)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-5 py-3.5 text-gray-400">{{ $i + 1 }}</td>
                                        <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}
                                        </td>
                                        <td class="px-5 py-3.5 font-medium text-gray-800 dark:text-gray-200">
                                            {{ $purchase->atk->name ?? '—' }}
                                        </td>
                                        <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">
                                            <span class="px-2.5 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-xs">
                                                {{ $purchase->atk->category->name ?? '—' }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5 text-right text-gray-800 dark:text-gray-200">{{ $purchase->quantity }}</td>
                                        <td class="px-5 py-3.5 text-right font-semibold text-amber-600 dark:text-amber-400">
                                            Rp {{ number_format($purchase->total_price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-5 py-3.5 text-center">
                                            @php
                                                $statusClass = match($purchase->status) {
                                                    'completed' => 'bg-emerald-100 text-emerald-700',
                                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                                    'cancelled' => 'bg-red-100 text-red-700',
                                                    default => 'bg-gray-100 text-gray-700',
                                                };
                                                $statusLabel = match($purchase->status) {
                                                    'completed' => 'Selesai',
                                                    'pending' => 'Pending',
                                                    'cancelled' => 'Batal',
                                                    default => $purchase->status,
                                                };
                                            @endphp
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5 text-center">
                                            <div class="flex gap-2 justify-center">
                                                <a href="{{ route('admin.atk-purchases.show', $purchase) }}"
                                                    class="text-indigo-600 hover:text-indigo-800 dark:hover:text-indigo-400 text-xs font-semibold">Lihat</a>

                                                @if ($purchase->status === 'pending')
                                                    <a href="{{ route('admin.atk-purchases.edit', $purchase) }}"
                                                        class="text-blue-600 hover:text-blue-800 dark:hover:text-blue-400 text-xs font-semibold">Edit</a>
                                                @endif

                                                @if (auth()->user()->role === 'admin' && $purchase->status === 'pending')
                                                    <form action="{{ route('admin.atk-purchases.confirm', $purchase) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="text-green-600 hover:text-green-800 dark:hover:text-green-400 text-xs font-semibold">
                                                            Konfirmasi
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-16 text-gray-400 text-sm">Belum ada pembelian ATK.</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
