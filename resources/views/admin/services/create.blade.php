<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Tambah Layanan</h2>
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

                <form method="POST" action="{{ route('admin.services.store') }}">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                        {{-- Nama --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Nama Layanan *
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200"
                                placeholder="cth. Energizing Therapy - 60'">
                        </div>

                        {{-- Kategori --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Kategori *
                            </label>
                            <select name="category" required
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach (['Refleksi', 'Minuman'] as $cat)
                                    <option value="{{ $cat }}"
                                        {{ old('category') === $cat ? 'selected' : '' }}>
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
                            <input type="text" name="sku" value="{{ old('sku') }}"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200"
                                placeholder="cth. R 60">
                        </div>

                        {{-- Harga --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Harga (Rp) *
                            </label>
                            <input type="number" name="price" value="{{ old('price') }}" required min="0"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200"
                                placeholder="110000">
                        </div>

                        {{-- Durasi --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Durasi (Menit)
                            </label>
                            <input type="number" name="duration" value="{{ old('duration') }}" min="1"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200"
                                placeholder="60">
                            <p class="text-xs text-gray-400 mt-1">Kosongkan jika bukan layanan terapi.</p>
                        </div>

                        {{-- Poin Reward --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Poin Reward ⭐
                            </label>
                            <input type="number" name="reward_points" value="{{ old('reward_points', 0) }}"
                                min="0" max="10" id="rewardPointsInput"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200"
                                placeholder="0" oninput="updatePointPreview()">

                            <div id="pointPreview" class="mt-2 hidden">
                                <span id="pointBadge"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                                    ⭐ +<span id="pointBadgeVal">0</span> poin per kunjungan selesai
                                </span>
                            </div>
                            <div id="pointZeroInfo" class="mt-2">
                                <span class="text-xs text-gray-400">
                                    Isi <strong>0</strong> jika layanan ini tidak memberikan poin (contoh: minuman).
                                    Pelanggan perlu <strong>10 poin</strong> untuk mendapat bonus gratis 1 jam.
                                </span>
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Deskripsi
                            </label>
                            <textarea name="description" rows="3"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200"
                                placeholder="Deskripsi layanan...">{{ old('description') }}</textarea>
                        </div>

                        {{-- Divider Toggle --}}
                        <div class="sm:col-span-2">
                            <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Pengaturan
                                </p>
                            </div>
                        </div>

                        {{-- Status Aktif --}}
                        <div class="sm:col-span-2">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1"
                                    {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Layanan aktif
                                    <span class="text-xs text-gray-400 font-normal ml-1">(ditampilkan di daftar
                                        booking)</span>
                                </span>
                            </label>
                        </div>

                        {{-- Home Service --}}
                        <div class="sm:col-span-2">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="hidden" name="is_home_service" value="0">
                                <input type="checkbox" name="is_home_service" value="1"
                                    {{ old('is_home_service') == '1' ? 'checked' : '' }} id="homeServiceCheck"
                                    class="w-4 h-4 mt-0.5 text-orange-500 border-gray-300 rounded focus:ring-orange-400"
                                    onchange="updateCommissionPreview()">
                                <div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Home Service
                                    </span>
                                    <span id="commissionBadge"
                                        class="ml-1.5 px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-400">
                                        Komisi 25%
                                    </span>
                                    <p class="text-xs text-gray-400 mt-1">
                                        Centang jika layanan dikerjakan di lokasi pelanggan.
                                        Reguler di tempat = <strong>25%</strong>, Home Service = <strong
                                            class="text-orange-500">30%</strong>.
                                    </p>
                                </div>
                            </label>
                        </div>

                    </div>

                    <div class="flex gap-3 mt-6 pt-5 border-t border-gray-100 dark:border-gray-700">
                        <button type="submit"
                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                            Simpan Layanan
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

    <script>
        function updatePointPreview() {
            const val = parseInt(document.getElementById('rewardPointsInput').value) || 0;
            const preview = document.getElementById('pointPreview');
            const zeroInfo = document.getElementById('pointZeroInfo');
            const badgeVal = document.getElementById('pointBadgeVal');

            if (val > 0) {
                badgeVal.textContent = val;
                preview.classList.remove('hidden');
                zeroInfo.classList.add('hidden');
            } else {
                preview.classList.add('hidden');
                zeroInfo.classList.remove('hidden');
            }
        }

        function updateCommissionPreview() {
            const isHome = document.getElementById('homeServiceCheck').checked;
            const badge = document.getElementById('commissionBadge');

            if (isHome) {
                badge.textContent = 'Komisi 30%';
                badge.className = 'ml-1.5 px-2 py-0.5 text-xs font-semibold rounded-full bg-orange-100 text-orange-600';
            } else {
                badge.textContent = 'Komisi 25%';
                badge.className = 'ml-1.5 px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-400';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            updatePointPreview();
            updateCommissionPreview();
        });
    </script>
</x-app-layout>
