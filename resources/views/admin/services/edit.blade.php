<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Layanan</p>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Edit — {{ $service->name }}
                </h2>
            </div>
            <a href="{{ route('admin.services.index') }}"
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-6">

                @if ($errors->any())
                    <div class="mb-5 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.services.update', $service) }}">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                        {{-- Nama --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Nama Layanan *
                            </label>
                            <input type="text" name="name" value="{{ old('name', $service->name) }}" required
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                        </div>

                        {{-- Kategori --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Kategori *
                            </label>
                            <select name="category" required
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                @foreach (['Refleksi', 'Minuman'] as $cat)
                                    <option value="{{ $cat }}"
                                        {{ old('category', $service->category) === $cat ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- SKU --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                SKU
                            </label>
                            <input type="text" name="sku" value="{{ old('sku', $service->sku) }}"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                        </div>

                        {{-- Harga --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Harga (Rp) *
                            </label>
                            <input type="number" name="price" value="{{ old('price', $service->price) }}" required
                                min="0"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                        </div>

                        {{-- Durasi --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Durasi (Menit)
                            </label>
                            <input type="number" name="duration" value="{{ old('duration', $service->duration) }}"
                                min="1"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                            <p class="text-xs text-gray-400 mt-1">Kosongkan jika bukan layanan terapi.</p>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Deskripsi
                            </label>
                            <textarea name="description" rows="3"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">{{ old('description', $service->description) }}</textarea>
                        </div>

                        {{-- Status --}}
                        <div class="sm:col-span-2">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1"
                                    {{ old('is_active', $service->is_active) ? 'checked' : '' }}
                                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Layanan aktif (ditampilkan di daftar booking)
                                </span>
                            </label>
                        </div>

                    </div>

                    <div class="flex gap-3 mt-6 pt-5 border-t border-gray-100 dark:border-gray-700">
                        <button type="submit"
                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.services.index') }}"
                            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                            Batal
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
