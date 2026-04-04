<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Ajukan Izin Baru
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            {{-- Error Messages --}}
            @if ($errors->any())
                <div
                    class="mb-6 px-4 py-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-lg">
                    <div class="font-semibold text-red-800 dark:text-red-300 mb-2">❌ Ada kesalahan:</div>
                    <ul class="text-sm text-red-700 dark:text-red-400 space-y-1 ml-4 list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Buat Pengajuan Izin</h3>
                </div>

                <form method="POST" action="{{ route('terapis.leaves.store') }}" class="p-6">
                    @csrf

                    {{-- Jenis Izin --}}
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                            Jenis Izin <span class="text-red-600">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            @php
                                $types = [
                                    'sakit' => ['icon' => '🏥', 'label' => 'Sakit'],
                                    'pribadi' => ['icon' => '👤', 'label' => 'Pribadi'],
                                    'cuti' => ['icon' => '🏖️', 'label' => 'Cuti'],
                                    'izin_khusus' => ['icon' => '⭐', 'label' => 'Izin Khusus'],
                                ];
                            @endphp
                            @foreach ($types as $value => $type)
                                <label class="relative">
                                    <input type="radio" name="type" value="{{ $value }}" required
                                        {{ old('type') === $value || (!old('type') && $value === 'sakit') ? 'checked' : '' }}
                                        class="sr-only peer">
                                    <div
                                        class="p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer text-center transition peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/30 hover:border-gray-300 dark:hover:border-gray-500">
                                        <div class="text-2xl mb-1">{{ $type['icon'] }}</div>
                                        <div class="font-semibold text-gray-900 dark:text-white text-sm">
                                            {{ $type['label'] }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('type')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">⚠️ {{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal Mulai --}}
                    <div class="mb-6">
                        <label for="start_date"
                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Tanggal Mulai <span class="text-red-600">*</span>
                        </label>
                        <input type="date" id="start_date" name="start_date"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            value="{{ old('start_date', date('Y-m-d')) }}" required>
                        <p class="text-sm text-gray-500 mt-1">Tanggal mulai izin Anda (minimal hari ini)</p>
                        @error('start_date')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">⚠️ {{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal Selesai --}}
                    <div class="mb-6">
                        <label for="end_date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Tanggal Selesai <span class="text-red-600">*</span>
                        </label>
                        <input type="date" id="end_date" name="end_date"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            value="{{ old('end_date', date('Y-m-d')) }}" required>
                        <p class="text-sm text-gray-500 mt-1">Tanggal berakhir izin Anda</p>
                        @error('end_date')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">⚠️ {{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Duration Summary --}}
                    <div
                        class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-lg">
                        <div class="text-sm">
                            <strong class="text-emerald-900 dark:text-emerald-300">Total Durasi:</strong>
                            <span id="duration-summary" class="font-semibold text-emerald-700 dark:text-emerald-400">0
                                hari</span>
                        </div>
                    </div>

                    {{-- Alasan --}}
                    <div class="mb-6">
                        <label for="reason" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Alasan <span class="text-red-600">*</span>
                        </label>
                        <textarea id="reason" name="reason" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            rows="4" placeholder="Jelaskan alasan izin Anda secara detail...">{{ old('reason') }}</textarea>
                        <p class="text-sm text-gray-500 mt-1">Minimal 10 karakter. Jelaskan alasan dengan jelas untuk
                            memudahkan persetujuan.</p>
                        @error('reason')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">⚠️ {{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Info Box --}}
                    <div
                        class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg">
                        <div class="text-sm text-blue-900 dark:text-blue-300">
                            <strong class="block mb-2">💡 Informasi Penting:</strong>
                            <ul class="space-y-1 ml-4 list-disc">
                                <li>Pengajuan izin akan dikirim ke admin untuk disetujui</li>
                                <li>Admin akan memberitahu status persetujuan Anda</li>
                                <li>Izin yang tumpang tindih tidak akan disetujui</li>
                                <li>Anda dapat membatalkan izin yang masih pending</li>
                            </ul>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex gap-3 justify-end">
                        <a href="{{ route('terapis.leaves.index') }}"
                            class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition">
                            ✓ Ajukan Izin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        const durationSummary = document.getElementById('duration-summary');

        function updateDuration() {
            const start = new Date(startDate.value);
            const end = new Date(endDate.value);

            if (start && end && end >= start) {
                const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
                durationSummary.textContent = `${days} hari`;
            }
        }

        startDate.addEventListener('change', updateDuration);
        endDate.addEventListener('change', updateDuration);

        // Initial update
        updateDuration();
    </script>
</x-app-layout>
