<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ isset($atk) ? 'Edit Item COA' : 'Tambah Item COA' }}
        </h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ isset($atk) ? route('admin.atk-items.update', $atk) : route('admin.atk-items.store') }}"
                method="POST"
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-6">
                @csrf
                @if (isset($atk))
                    @method('PUT')
                @endif

                @if ($errors->any())
                    <div class="mb-4 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-200 rounded-lg text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Kategori -->
                <div class="mb-5">
                    <label for="atk_category_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Kategori COA <span class="text-red-500">*</span>
                    </label>
                    <select name="atk_category_id" id="atk_category_id" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('atk_category_id') border-red-500 @enderror">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ (isset($atk) ? $atk->atk_category_id : old('atk_category_id')) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }} ({{ $category->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('atk_category_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Item -->
                <div class="mb-5">
                    <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Nama Item <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" required
                        value="{{ isset($atk) ? $atk->name : old('name') }}"
                        placeholder="Contoh: Token Listrik, Garam SPA, Tissue"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kode Item -->
                <div class="mb-5">
                    <label for="code" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Kode Item <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" id="code" required
                        value="{{ isset($atk) ? $atk->code : old('code') }}"
                        placeholder="Contoh: 55, 81, 210"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('code') border-red-500 @enderror">
                    @error('code')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stok -->
                @if (isset($atk))
                    {{-- Edit mode: tampilkan stok saat ini, tidak bisa diubah langsung --}}
                    <div class="mb-5 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <h3 class="font-semibold text-blue-900 dark:text-blue-200 mb-3">ℹ️ Informasi Stok</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-blue-700 dark:text-blue-300">Stok Saat Ini:</span>
                                <p class="font-bold text-blue-900 dark:text-blue-100 text-lg">{{ $atk->stock }} unit</p>
                            </div>
                            <div>
                                <span class="text-blue-700 dark:text-blue-300">Harga Terakhir:</span>
                                <p class="font-semibold text-blue-900 dark:text-blue-100">
                                    {{ $atk->last_purchase_price ? 'Rp ' . number_format($atk->last_purchase_price, 0, ',', '.') : '—' }}
                                </p>
                            </div>
                        </div>
                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-2">
                            💡 Untuk mengubah stok, gunakan fitur <strong>Penyesuaian Stok</strong> di halaman detail item.
                        </p>
                    </div>
                @else
                    {{-- Create mode: bisa set stok awal --}}
                    <div class="mb-5">
                        <label for="stock" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Stok Awal
                        </label>
                        <input type="number" name="stock" id="stock" min="0" value="{{ old('stock', 0) }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('stock') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-400">Biarkan 0 jika belum ada stok saat ini.</p>
                        @error('stock')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Harga Awal -->
                    <div class="mb-5">
                        <label for="last_purchase_price" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Harga Satuan Awal (Rp)
                        </label>
                        <input type="number" name="last_purchase_price" id="last_purchase_price" min="0" value="{{ old('last_purchase_price', 0) }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('last_purchase_price') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-400">Biarkan 0 jika harga belum diketahui.</p>
                        @error('last_purchase_price')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <!-- Deskripsi -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Deskripsi (Optional)
                    </label>
                    <textarea name="description" id="description" rows="3"
                        placeholder="Penjelasan tambahan tentang item ini..."
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ isset($atk) ? $atk->description : old('description') }}</textarea>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-lg transition">
                        💾 {{ isset($atk) ? 'Simpan Perubahan' : 'Tambah Item' }}
                    </button>
                    <a href="{{ route('admin.atk-items.index') }}"
                        class="flex-1 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-semibold py-2.5 px-4 rounded-lg transition text-center">
                        ✕ Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
