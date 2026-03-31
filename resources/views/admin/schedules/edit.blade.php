<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Jadwal</p>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Edit Jadwal — {{ $schedule?->therapist?->name ?? 'Jadwal' }}
                </h2>
            </div>
            <a href="{{ route('admin.schedules.all', ['month' => $schedule?->schedule_date?->month ?? now()->month, 'year' => $schedule?->schedule_date?->year ?? now()->year]) }}"
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

                <form method="POST" action="{{ route('admin.schedules.update', $schedule) }}">
                    @csrf @method('PUT')
                    <div class="space-y-6">

                        {{-- Tanggal & Terapis Info --}}
                        @if($schedule)
                        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                            <p class="text-sm font-medium text-blue-900 dark:text-blue-200">
                                📅 <strong>{{ $schedule->schedule_date->format('l, d F Y') }}</strong>
                            </p>
                            <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                                Terapis: <strong>{{ $schedule->therapist->name }}</strong>
                            </p>
                        </div>

                        {{-- Hidden inputs --}}
                        <input type="hidden" name="therapist_id" value="{{ $schedule->therapist_id }}">
                        <input type="hidden" name="schedule_date" value="{{ $schedule->schedule_date->format('Y-m-d') }}">
                        @else
                        <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                            <p class="text-sm font-medium text-red-900 dark:text-red-200">
                                ⚠️ Jadwal tidak ditemukan
                            </p>
                        </div>
                        @endif

                        {{-- Status --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                Status Kerja *
                            </label>
                            <div class="space-y-3">
                                @php
                                    $statusOptions = [
                                        'working' => [
                                            'label' => 'Kerja',
                                            'desc' => 'Terapis bekerja normal',
                                            'icon' => '✓',
                                            'color' => 'indigo',
                                        ],
                                        'off' => [
                                            'label' => 'Libur',
                                            'desc' => 'Hari libur / istirahat',
                                            'icon' => '☀',
                                            'color' => 'gray',
                                        ],
                                        'sick' => [
                                            'label' => 'Sakit',
                                            'desc' => 'Terapis sakit / tidak bisa bekerja',
                                            'icon' => '🤒',
                                            'color' => 'orange',
                                        ],
                                        'vacation' => [
                                            'label' => 'Liburan',
                                            'desc' => 'Liburan pribadi / cuti tahunan',
                                            'icon' => '✈',
                                            'color' => 'purple',
                                        ],
                                        'cuti_bersama' => [
                                            'label' => 'Cuti Bersama',
                                            'desc' => 'Cuti bersama nasional',
                                            'icon' => '🎉',
                                            'color' => 'blue',
                                        ],
                                    ];
                                @endphp

                                @foreach ($statusOptions as $value => $option)
                                    <label class="relative flex items-start p-4 border rounded-lg cursor-pointer transition hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ $schedule && $schedule->status === $value ? 'border-' . $option['color'] . '-500 bg-' . $option['color'] . '-50 dark:bg-' . $option['color'] . '-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                                        <input type="radio" name="status" value="{{ $value }}"
                                            {{ $schedule && $schedule->status === $value ? 'checked' : '' }} required
                                            class="w-4 h-4 mt-0.5 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                                            onchange="toggleWorkingHours()">
                                        <span class="ml-3">
                                            <span class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                                {{ $option['icon'] }} {{ $option['label'] }}
                                            </span>
                                            <span class="block text-xs text-gray-500 dark:text-gray-400">
                                                {{ $option['desc'] }}
                                            </span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Jam Kerja (hanya jika status = working) --}}
                        @if($schedule)
                        <div id="workingHoursSection"
                            class="{{ $schedule->status !== 'working' ? 'hidden' : '' }}">
                            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg space-y-4">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">⏰ Jam Kerja</h3>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                            Jam Masuk *
                                        </label>
                                        <input type="time" name="start_time" id="startTimeInput"
                                            value="{{ $schedule->start_time ? $schedule->start_time->format('H:i') : '09:00' }}"
                                            class="w-full px-4 py-2.5 bg-white dark:bg-gray-600 border border-gray-200 dark:border-gray-500 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                            Jam Keluar *
                                        </label>
                                        <input type="time" name="end_time" id="endTimeInput"
                                            value="{{ $schedule->end_time ? $schedule->end_time->format('H:i') : '17:00' }}"
                                            class="w-full px-4 py-2.5 bg-white dark:bg-gray-600 border border-gray-200 dark:border-gray-500 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Catatan --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Catatan
                            </label>
                            <textarea name="notes" rows="3"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200"
                                placeholder="Tambahkan catatan jika diperlukan...">{{ $schedule?->notes ?? '' }}</textarea>
                        </div>

                    </div>

                    <div class="flex gap-3 mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
                        <button type="submit"
                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.schedules.all', ['month' => $schedule?->schedule_date?->month ?? now()->month, 'year' => $schedule?->schedule_date?->year ?? now()->year]) }}"
                            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                            Batal
                        </a>
                        @if($schedule)
                        <form method="POST"
                            action="{{ route('admin.schedules.destroy', $schedule) }}"
                            onsubmit="return confirm('Hapus jadwal ini?')"
                            class="ml-auto">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="px-5 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium rounded-lg transition-colors">
                                🗑 Hapus
                            </button>
                        </form>
                        @endif
                    </div>
                </form>

                {{-- Navigasi Prev/Next --}}
                @if ($schedule && ($prevSchedule || $nextSchedule))
                    <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700 flex gap-3">
                        @if ($prevSchedule)
                            <a href="{{ route('admin.schedules.edit', $prevSchedule) }}"
                                class="flex-1 px-4 py-3 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg text-sm transition">
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">← Sebelumnya</div>
                                <div class="font-medium text-gray-700 dark:text-gray-200">
                                    {{ $prevSchedule->schedule_date->format('d M') }}
                                </div>
                            </a>
                        @endif

                        @if ($nextSchedule)
                            <a href="{{ route('admin.schedules.edit', $nextSchedule) }}"
                                class="flex-1 px-4 py-3 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg text-sm transition text-right">
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Selanjutnya →</div>
                                <div class="font-medium text-gray-700 dark:text-gray-200">
                                    {{ $nextSchedule->schedule_date->format('d M') }}
                                </div>
                            </a>
                        @endif
                    </div>
                @endif

            </div>
        </div>
    </div>

    <script>
        function toggleWorkingHours() {
            const selectedStatus = document.querySelector('input[name="status"]:checked').value;
            const workingHoursSection = document.getElementById('workingHoursSection');
            const startTimeInput = document.getElementById('startTimeInput');
            const endTimeInput = document.getElementById('endTimeInput');

            if (selectedStatus === 'working') {
                workingHoursSection.classList.remove('hidden');
                startTimeInput.setAttribute('required', '');
                endTimeInput.setAttribute('required', '');
            } else {
                workingHoursSection.classList.add('hidden');
                startTimeInput.removeAttribute('required');
                endTimeInput.removeAttribute('required');
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', toggleWorkingHours);
    </script>
</x-app-layout>
