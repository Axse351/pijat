<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Detail Pembelian ATK</h2>
            <div class="flex gap-2">
                @if ($purchase->status === 'pending' && auth()->user()->role === 'admin')
                    <form action="{{ route('admin.atk-purchases.confirm', $purchase) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg">
                            ✓ Konfirmasi
                        </button>
                    </form>
                @endif

                @if ($purchase->status === 'pending')
                    <a href="{{ route('admin.atk-purchases.edit', $purchase) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                        ✎ Edit
                    </a>
                @endif

                <a href="{{ route('admin.atk-purchases.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg">
                    ← Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-200 rounded-lg text-sm">
                    ✓ {{ session('success') }}
                </div>
            @endif

            <!-- Status Badge -->
            <div class="mb-6">
                @php
                    $statusClass = match($purchase->status) {
                        'completed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                        'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
                        'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                        default => 'bg-gray-100 text-gray-700',
                    };
                    $statusLabel = match($purchase->status) {
                        'completed' => 'Selesai',
                        'pending' => 'Menunggu Konfirmasi',
                        'cancelled' => 'Dibatalkan',
                        default => $purchase->status,
                    };
                @endphp
                <span class="inline-block px-3 py-1.5 rounded-full text-sm font-semibold {{ $statusClass }}">
                    {{ $statusLabel }}
                </span>
            </div>

            <!-- Info Card -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Informasi Pembelian -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">Informasi Pembelian</h3>

                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Nomor Transaksi:</span>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">{{ 'PUR-' . str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}</p>
                        </div>

                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Tanggal Pembelian:</span>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">
                                {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y, H:i') }}
                            </p>
                        </div>

                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Nomor Bukti:</span>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $purchase->receipt_number ?? '—' }}</p>
                        </div>

                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Dicatat oleh:</span>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $purchase->createdBy->name ?? '—' }}</p>
                        </div>

                        @if ($purchase->notes)
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Catatan:</span>
                                <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $purchase->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Detail Item -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">Detail Item</h3>

                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Item ATK:</span>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $purchase->atk->name }}</p>
                        </div>

                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Kode:</span>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $purchase->atk->code }}</p>
                        </div>

                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Kategori:</span>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $purchase->atk->category->name }}</p>
                        </div>

                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Stok Saat Ini:</span>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $purchase->atk->stock }} unit</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Harga Details -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-6">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Quantity</div>
                    <div class="text-3xl font-bold text-gray-800 dark:text-gray-200 mt-1">{{ $purchase->quantity }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">unit</div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-6">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Harga Satuan</div>
                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-1">Rp</div>
                    <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ number_format($purchase->unit_price, 0, ',', '.') }}</div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-6">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Total Harga</div>
                    <div class="text-3xl font-bold text-amber-600 dark:text-amber-400 mt-1">Rp</div>
                    <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ number_format($purchase->total_price, 0, ',', '.') }}</div>
                </div>
            </div>

            <!-- Opex Information (jika sudah completed) -->
            @if ($purchase->status === 'completed' && $purchase->opex)
                <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-xl p-6 mb-6">
                    <h3 class="font-semibold text-indigo-900 dark:text-indigo-200 mb-3">📊 Pengurang Pendapatan (Opex)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-indigo-700 dark:text-indigo-300">Status Opex:</span>
                            <p class="font-semibold text-indigo-900 dark:text-indigo-100 mt-1">
                                @if ($purchase->opex->status === 'recorded')
                                    <span class="px-2 py-1 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 rounded text-xs font-semibold">
                                        Tercatat
                                    </span>
                                @elseif ($purchase->opex->status === 'reversed')
                                    <span class="px-2 py-1 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 rounded text-xs font-semibold">
                                        Dibatalkan
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <span class="text-indigo-700 dark:text-indigo-300">Jumlah Opex:</span>
                            <p class="font-semibold text-indigo-900 dark:text-indigo-100 mt-1">Rp {{ number_format($purchase->opex->amount, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <span class="text-indigo-700 dark:text-indigo-300">Tanggal Tercatat:</span>
                            <p class="font-semibold text-indigo-900 dark:text-indigo-100 mt-1">
                                {{ \Carbon\Carbon::parse($purchase->opex->recorded_date)->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Timeline -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">📅 Timeline</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex gap-4">
                        <div class="w-2 h-2 bg-indigo-600 rounded-full mt-2 flex-shrink-0"></div>
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">Dibuat</p>
                            <p class="text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($purchase->created_at)->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    @if ($purchase->updated_at && $purchase->updated_at->ne($purchase->created_at))
                        <div class="flex gap-4">
                            <div class="w-2 h-2 bg-blue-600 rounded-full mt-2 flex-shrink-0"></div>
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-gray-200">Terakhir Diperbarui</p>
                                <p class="text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($purchase->updated_at)->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
