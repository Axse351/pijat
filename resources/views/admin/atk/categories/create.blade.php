<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ isset($atkCategory) ? 'Edit Kategori COA' : 'Tambah Kategori COA' }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <form
                action="{{ isset($atkCategory) ? route('admin.atk-categories.update', $atkCategory) : route('admin.atk-categories.store') }}"
                method="POST"
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-6">
                @csrf
                @if (isset($atkCategory))
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

                <!-- Kode -->
                <div class="mb-5">
                    <label for="code" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Kode Kategori <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" id="code" required
                        value="{{ isset($atkCategory) ? $atkCategory->code : old('code') }}"
                        placeholder="Contoh: 1, 2, 3, 10"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('code') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-400">Sesuaikan dengan kode Chart of Accounts</p>
                    @error('code')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama -->
                <div class="mb-5">
                    <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Nama Kategori <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" required
                        value="{{ isset($atkCategory) ? $atkCategory->name : old('name') }}"
                        placeholder="Contoh: Alat Terapi, Sanitary, Promosi"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Deskripsi (Optional)
                    </label>
                    <textarea name="description" id="description" rows="3"
                        placeholder="Penjelasan tambahan tentang kategori ini..."
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ isset($atkCategory) ? $atkCategory->description : old('description') }}</textarea>
                </div>

                <!-- Referensi COA (info card) -->
                <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                    <p class="text-xs font-semibold text-amber-800 dark:text-amber-200 mb-2">📋 Referensi Chart of Accounts</p>
                    <div class="grid grid-cols-2 gap-1 text-xs text-amber-700 dark:text-amber-300">
                        <span>1 — Alat Terapi</span>
                        <span>2 — AMDK</span>
                        <span>3 — ATK</span>
                        <span>4 — Entertaint</span>
                        <span>5 — Gedung & Komunikasi</span>
                        <span>6 — Komisi & Training</span>
                        <span>7 — Promosi</span>
                        <span>8 — Sanitary</span>
                        <span>9 — Transportasi</span>
                        <span>10 — Others</span>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-lg transition">
                        💾 {{ isset($atkCategory) ? 'Simpan Perubahan' : 'Tambah Kategori' }}
                    </button>
                    <a href="{{ route('admin.atk-categories.index') }}"
                        class="flex-1 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-semibold py-2.5 px-4 rounded-lg transition text-center">
                        ✕ Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
