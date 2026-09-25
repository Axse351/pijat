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

                @if ($errors->any())
                    <div class="mb-5 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

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

                        {{-- Tipe Diskon --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Tipe Diskon *
                            </label>
                            <select name="discount_type" id="discountType" required
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                                <option value="percent"
                                    {{ old('discount_type', 'percent') === 'percent' ? 'selected' : '' }}>Persentase (%)
                                </option>
                                <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>Nominal
                                    Tetap (Rp)</option>
                            </select>
                        </div>

                        {{-- Nilai Diskon --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                <span id="discountLabel">Diskon (%)</span> *
                            </label>
                            <input type="number" name="discount" id="discountInput" value="{{ old('discount') }}"
                                min="0" required
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500"
                                placeholder="0">
                        </div>

                        {{-- Maks. Diskon (hanya untuk persen) --}}
                        <div id="maxDiscountWrap">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Maksimal Diskon (Rp) <span class="normal-case font-normal text-gray-400">—
                                    opsional</span>
                            </label>
                            <input type="number" name="max_discount" value="{{ old('max_discount') }}" min="0"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500"
                                placeholder="Kosongkan jika tidak ada batas">
                            <p class="text-xs text-gray-400 mt-1">Batas maksimal potongan agar diskon persen tidak
                                kebablasan di layanan mahal.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Status
                            </label>
                            <select name="status"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                                <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif
                                </option>
                                <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif
                                </option>
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

    <script>
        const discountType = document.getElementById('discountType');
        const discountLabel = document.getElementById('discountLabel');
        const discountInput = document.getElementById('discountInput');
        const maxDiscountWrap = document.getElementById('maxDiscountWrap');

        function syncDiscountUI() {
            if (discountType.value === 'fixed') {
                discountLabel.textContent = 'Diskon (Rp)';
                discountInput.placeholder = '50000';
                maxDiscountWrap.classList.add('hidden');
            } else {
                discountLabel.textContent = 'Diskon (%)';
                discountInput.placeholder = '10';
                maxDiscountWrap.classList.remove('hidden');
            }
        }

        discountType.addEventListener('change', syncDiscountUI);
        document.addEventListener('DOMContentLoaded', syncDiscountUI);
    </script>
</x-app-layout>
