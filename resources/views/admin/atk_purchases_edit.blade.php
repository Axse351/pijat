<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit Pembelian ATK</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($purchase->status !== 'pending')
                <div class="mb-4 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-200 rounded-lg text-sm">
                    ⚠️ Hanya pembelian dengan status pending yang dapat diedit.
                </div>
                <div class="text-center py-8">
                    <a href="{{ route('admin.atk-purchases.index') }}" class="text-indigo-600 hover:text-indigo-800 dark:hover:text-indigo-400 font-semibold">
                        ← Kembali ke Daftar
                    </a>
                </div>
            @else
                <form action="{{ route('admin.atk-purchases.update', $purchase) }}" method="POST"
                    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-6">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                        <div class="mb-4 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-200 rounded-lg text-sm">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Item Info (Read-only) -->
                    <div class="mb-5 p-4 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-semibold">Item ATK:</span>
                            <span class="text-gray-800 dark:text-gray-200">{{ $purchase->atk->name }}</span>
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            <span class="font-semibold">Kategori:</span>
                            <span class="text-gray-800 dark:text-gray-200">{{ $purchase->atk->category->name }}</span>
                        </p>
                    </div>

                    <!-- Quantity -->
                    <div class="mb-5">
                        <label for="quantity" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Jumlah <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="quantity" id="quantity" min="1" required value="{{ old('quantity', $purchase->quantity) }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('quantity') border-red-500 @enderror">
                        @error('quantity')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Unit Price -->
                    <div class="mb-5">
                        <label for="unit_price" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Harga Satuan (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="unit_price" id="unit_price" min="0" step="0.01" required value="{{ old('unit_price', $purchase->unit_price) }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('unit_price') border-red-500 @enderror">
                        @error('unit_price')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Total Price (Read-only) -->
                    <div class="mb-5 p-4 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Total Harga (Rp)
                        </label>
                        <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">
                            <span id="total-price">{{ number_format($purchase->total_price, 0) }}</span>
                        </div>
                    </div>

                    <!-- Tanggal Pembelian -->
                    <div class="mb-5">
                        <label for="purchase_date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Tanggal Pembelian <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="purchase_date" id="purchase_date" required value="{{ old('purchase_date', $purchase->purchase_date->format('Y-m-d')) }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('purchase_date') border-red-500 @enderror">
                        @error('purchase_date')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nomor Bukti -->
                    <div class="mb-5">
                        <label for="receipt_number" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Nomor Bukti (Optional)
                        </label>
                        <input type="text" name="receipt_number" id="receipt_number" placeholder="INV-2024-001" value="{{ old('receipt_number', $purchase->receipt_number) }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>

                    <!-- Catatan -->
                    <div class="mb-6">
                        <label for="notes" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Catatan
                        </label>
                        <textarea name="notes" id="notes" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('notes', $purchase->notes) }}</textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-lg transition">
                            💾 Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.atk-purchases.index') }}" class="flex-1 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-semibold py-2.5 px-4 rounded-lg transition text-center">
                            ✕ Batal
                        </a>
                    </div>
                </form>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInput = document.getElementById('quantity');
        const unitPriceInput = document.getElementById('unit_price');
        const totalPriceSpan = document.getElementById('total-price');

        function updateTotalPrice() {
            const quantity = parseFloat(quantityInput.value) || 0;
            const unitPrice = parseFloat(unitPriceInput.value) || 0;
            const total = quantity * unitPrice;

            totalPriceSpan.textContent = new Intl.NumberFormat('id-ID').format(total);
        }

        quantityInput.addEventListener('change', updateTotalPrice);
        quantityInput.addEventListener('keyup', updateTotalPrice);
        unitPriceInput.addEventListener('change', updateTotalPrice);
        unitPriceInput.addEventListener('keyup', updateTotalPrice);
    });
    </script>
    @endpush
</x-app-layout>
