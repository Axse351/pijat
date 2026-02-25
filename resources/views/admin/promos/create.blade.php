<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Tambah Promo</h2>
            <a href="{{ route('admin.promos.index') }}"
               class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg">
               ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-6">

                <form method="POST" action="{{ route('admin.promos.store') }}">
                    @csrf

                    <div class="space-y-5">

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Kode Promo *
                            </label>
                            <input type="text" name="code" value="{{ old('code') }}" required
                                   class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500"
                                   placeholder="PROMO10">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Nama Promo *
                            </label>
                            <input type="text" name="nama_promo" value="{{ old('nama_promo') }}" required
                                   class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500"
                                   placeholder="Promo Diskon Awal Tahun">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Diskon (%) *
                            </label>
                            <input type="number" name="discount" value="{{ old('discount') }}" min="0"
                                   class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Status
                            </label>
                            <select name="status"
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>

                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="submit"
                                class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                            Simpan
                        </button>

                        <a href="{{ route('admin.promos.index') }}"
                           class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg">
                            Batal
                        </a>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
