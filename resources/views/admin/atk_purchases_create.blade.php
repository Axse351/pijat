<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Catat Pembelian ATK</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('admin.atk-purchases.store') }}" method="POST"
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-6">
                @csrf

                @if ($errors->any())
                    <div
                        class="mb-4 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-200 rounded-lg text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Kategori ATK -->
                <div class="mb-5">
                    <label for="category_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Kategori ATK <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" id="category_id" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('category_id') border-red-500 @enderror">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }} ({{ $category->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Item ATK -->
                <div class="mb-5">
                    <label for="atk_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Item ATK <span class="text-red-500">*</span>
                    </label>
                    <select name="atk_id" id="atk_id" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('atk_id') border-red-500 @enderror">
                        <option value="">-- Pilih Item (pilih kategori dulu) --</option>
                    </select>
                    @error('atk_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Stok -->
                <div id="stock-info"
                    class="mb-5 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg hidden">
                    <p class="text-sm text-blue-700 dark:text-blue-200">
                        <span class="font-semibold">Stok saat ini:</span>
                        <span id="current-stock" class="font-bold">-</span>
                    </p>
                    <p class="text-sm text-blue-700 dark:text-blue-200 mt-1">
                        <span class="font-semibold">Harga terakhir:</span>
                        <span id="last-price" class="font-bold">-</span>
                    </p>
                </div>

                <!-- Quantity -->
                <div class="mb-5">
                    <label for="quantity" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Jumlah <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="quantity" id="quantity" min="1" required
                        value="{{ old('quantity') }}"
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
                    <input type="number" name="unit_price" id="unit_price" min="0" step="0.01" required
                        value="{{ old('unit_price') }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('unit_price') border-red-500 @enderror">
                    @error('unit_price')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total Price (Read-only) -->
                <div
                    class="mb-5 p-4 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Total Harga (Rp)
                    </label>
                    <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">
                        <span id="total-price">0</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        💡 Akan otomatis dikurangi dari pendapatan (opex)
                    </p>
                </div>

                <!-- Tanggal Pembelian -->
                <div class="mb-5">
                    <label for="purchase_date"
                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Tanggal Pembelian <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="purchase_date" id="purchase_date" required
                        value="{{ old('purchase_date', date('Y-m-d')) }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('purchase_date') border-red-500 @enderror">
                    @error('purchase_date')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor Bukti -->
                <div class="mb-5">
                    <label for="receipt_number"
                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Nomor Bukti (Optional)
                    </label>
                    <input type="text" name="receipt_number" id="receipt_number" placeholder="INV-2024-001"
                        value="{{ old('receipt_number') }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <!-- Catatan -->
                <div class="mb-6">
                    <label for="notes" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Catatan
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('notes') }}</textarea>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-lg transition">
                        💾 Simpan Pembelian
                    </button>
                    <a href="{{ route('admin.atk-purchases.index') }}"
                        class="flex-1 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-semibold py-2.5 px-4 rounded-lg transition text-center">
                        ✕ Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Script langsung di view (tidak bergantung pada @stack di layout) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('category_id');
            const atkSelect = document.getElementById('atk_id');
            const quantityInput = document.getElementById('quantity');
            const unitPriceInput = document.getElementById('unit_price');
            const totalPriceSpan = document.getElementById('total-price');
            const stockInfo = document.getElementById('stock-info');
            const currentStockSpan = document.getElementById('current-stock');
            const lastPriceSpan = document.getElementById('last-price');

            const baseUrl = "{{ url('admin/api') }}";
            const oldAtkId = "{{ old('atk_id') }}";
            const fmt = new Intl.NumberFormat('id-ID');

            function updateTotalPrice() {
                const quantity = parseFloat(quantityInput.value) || 0;
                const unitPrice = parseFloat(unitPriceInput.value) || 0;
                totalPriceSpan.textContent = fmt.format(quantity * unitPrice);
            }

            async function loadItems(categoryId, selectedId = null) {
                atkSelect.innerHTML = '<option value="">-- Pilih Item --</option>';
                stockInfo.classList.add('hidden');
                if (!categoryId) {
                    atkSelect.innerHTML = '<option value="">-- Pilih Item (pilih kategori dulu) --</option>';
                    return;
                }
                try {
                    const response = await fetch(`${baseUrl}/atk-by-category/${categoryId}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    const atks = await response.json();

                    if (atks.length === 0) {
                        atkSelect.innerHTML = '<option value="">-- Kategori ini belum punya item --</option>';
                        return;
                    }

                    atks.forEach(atk => {
                        const option = document.createElement('option');
                        option.value = atk.id;
                        option.textContent = `${atk.name} (${atk.code}) - Stok: ${atk.stock}`;
                        if (selectedId && String(atk.id) === String(selectedId)) option.selected = true;
                        atkSelect.appendChild(option);
                    });

                    if (selectedId) loadDetail(selectedId, false);
                } catch (error) {
                    console.error('Gagal memuat item:', error);
                    atkSelect.innerHTML = '<option value="">-- Gagal memuat item --</option>';
                }
            }

            async function loadDetail(atkId, fillPrice = true) {
                if (!atkId) {
                    stockInfo.classList.add('hidden');
                    return;
                }
                try {
                    const response = await fetch(`${baseUrl}/atk-detail/${atkId}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    const atk = await response.json();

                    currentStockSpan.textContent = atk.stock;
                    lastPriceSpan.textContent = 'Rp ' + fmt.format(atk.last_purchase_price || 0);
                    if (fillPrice) unitPriceInput.value = atk.last_purchase_price || 0;

                    stockInfo.classList.remove('hidden');
                    updateTotalPrice();
                } catch (error) {
                    console.error('Gagal memuat detail:', error);
                }
            }

            categorySelect.addEventListener('change', () => loadItems(categorySelect.value));
            atkSelect.addEventListener('change', () => loadDetail(atkSelect.value));

            ['change', 'keyup', 'input'].forEach(evt => {
                quantityInput.addEventListener(evt, updateTotalPrice);
                unitPriceInput.addEventListener(evt, updateTotalPrice);
            });

            // Muat ulang item jika kategori sudah terpilih (mis. setelah validasi gagal)
            if (categorySelect.value) loadItems(categorySelect.value, oldAtkId);
            updateTotalPrice();
        });
    </script>
</x-app-layout>
