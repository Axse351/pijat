<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Tambah Program</h2>
            <a href="{{ route('admin.programs.index') }}"
                class="px-4 py-2 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium rounded-lg transition-colors">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            <form method="POST" action="{{ route('admin.programs.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="space-y-5">

                    {{-- Informasi Program --}}
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-200 text-sm">Informasi Program</h3>
                        </div>
                        <div class="p-5 space-y-4">

                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                    Nama Program <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nama_program" value="{{ old('nama_program') }}"
                                    placeholder="cth: Promo Hari Jadi, Diskon Spesial..."
                                    class="w-full px-3.5 py-2.5 text-sm border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('nama_program') border-red-400 @enderror"
                                    required>
                                @error('nama_program')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Deskripsi</label>
                                <textarea name="description" rows="3" placeholder="Jelaskan program ini..."
                                    class="w-full px-3.5 py-2.5 text-sm border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Banner
                                    / Gambar</label>
                                <div onclick="document.getElementById('imageInput').click()"
                                    class="border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg p-6 text-center cursor-pointer hover:border-indigo-400 dark:hover:border-indigo-600 transition-colors">
                                    <input type="file" id="imageInput" name="image" accept="image/*"
                                        onchange="previewImage(this)" class="hidden">
                                    <img id="imagePreview"
                                        class="w-full max-h-36 object-cover rounded-lg mb-3 hidden mx-auto">
                                    <div id="uploadPlaceholder">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            <span class="text-indigo-600 dark:text-indigo-400 font-medium">Klik untuk
                                                upload</span>
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP — maks. 2MB</p>
                                    </div>
                                </div>
                                @error('image')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </div>

                    {{-- Pengaturan Diskon --}}
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-200 text-sm">Pengaturan Diskon</h3>
                        </div>
                        <div class="p-5 space-y-4">

                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                    Jenis Diskon <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="discount_type" value="percent" class="sr-only peer"
                                            {{ old('discount_type', 'percent') === 'percent' ? 'checked' : '' }}>
                                        <div
                                            class="flex items-center gap-3 px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-lg transition-all peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/20">
                                            <span
                                                class="text-lg font-black text-gray-400 peer-checked:text-indigo-600">%</span>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                                    Persentase</p>
                                                <p class="text-xs text-gray-400">cth: 20% off</p>
                                            </div>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="discount_type" value="nominal" class="sr-only peer"
                                            {{ old('discount_type') === 'nominal' ? 'checked' : '' }}>
                                        <div
                                            class="flex items-center gap-3 px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-lg transition-all peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/20">
                                            <span
                                                class="text-sm font-black text-gray-400 peer-checked:text-indigo-600">Rp</span>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                                    Nominal</p>
                                                <p class="text-xs text-gray-400">cth: Rp 50.000</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                @error('discount_type')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                        Nilai Diskon <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" name="discount_value" value="{{ old('discount_value') }}"
                                            placeholder="0" min="0" step="any" oninput="updatePreview()"
                                            class="w-full pl-3.5 pr-10 py-2.5 text-sm border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('discount_value') border-red-400 @enderror"
                                            required>
                                        <span id="discountSuffix"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">%</span>
                                    </div>
                                    @error('discount_value')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label
                                        class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Maks.
                                        Potongan (Rp)</label>
                                    <input type="number" name="max_discount" value="{{ old('max_discount') }}"
                                        placeholder="Tak terbatas" min="0" step="any"
                                        class="w-full px-3.5 py-2.5 text-sm border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    @error('max_discount')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Minimal
                                    Transaksi (Rp)</label>
                                <input type="number" name="min_transaction" value="{{ old('min_transaction') }}"
                                    placeholder="Tanpa minimum" min="0" step="any"
                                    class="w-full px-3.5 py-2.5 text-sm border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <p class="mt-1 text-xs text-gray-400">Kosongkan jika tidak ada syarat minimal
                                    transaksi.</p>
                            </div>

                        </div>
                    </div>

                    {{-- Periode & Status --}}
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-200 text-sm">Periode & Status</h3>
                        </div>
                        <div class="p-5 space-y-4">

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Tanggal
                                        Mulai</label>
                                    <input type="date" name="start_date" value="{{ old('start_date') }}"
                                        class="w-full px-3.5 py-2.5 text-sm border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    @error('start_date')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Tanggal
                                        Berakhir</label>
                                    <input type="date" name="end_date" value="{{ old('end_date') }}"
                                        class="w-full px-3.5 py-2.5 text-sm border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <p class="mt-1 text-xs text-gray-400">Kosongkan jika tidak ada batas waktu.</p>
                                    @error('end_date')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div
                                class="flex items-center justify-between py-3 px-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Program Aktif</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Program langsung tersedia untuk pelanggan.
                                    </p>
                                </div>
                                <label class="relative cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                                        {{ old('is_active', '1') ? 'checked' : '' }}>
                                    <div
                                        class="w-10 h-6 rounded-full transition-colors bg-gray-300 dark:bg-gray-600 peer-checked:bg-emerald-500">
                                    </div>
                                    <div
                                        class="absolute top-1 left-1 w-4 h-4 rounded-full bg-white shadow transition-transform peer-checked:translate-x-4">
                                    </div>
                                </label>
                            </div>

                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.programs.index') }}"
                            class="px-5 py-2.5 text-sm text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors">
                            Simpan Program
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    const img = document.getElementById('imagePreview');
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                    document.getElementById('uploadPlaceholder').classList.add('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function updatePreview() {
            const type = document.querySelector('input[name="discount_type"]:checked')?.value ?? 'percent';
            const suffix = document.getElementById('discountSuffix');
            suffix.textContent = type === 'percent' ? '%' : 'Rp';
        }

        document.querySelectorAll('input[name="discount_type"]').forEach(el => {
            el.addEventListener('change', updatePreview);
        });
        updatePreview();
    </script>
</x-app-layout>
