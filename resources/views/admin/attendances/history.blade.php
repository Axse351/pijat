<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Riwayat Kehadiran - ') }} {{ $therapist->name }}
            </h2>
            <a href="{{ route('admin.attendances.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium">
                ← {{ __('Kembali') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <!-- Total Hadir -->
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-green-700 dark:text-green-300 font-semibold">{{ __('Total Hadir') }}</p>
                            <p class="text-2xl font-bold text-green-800 dark:text-green-200 mt-1">{{ $stats['total_hadir'] ?? 0 }}</p>
                        </div>
                        <svg class="w-10 h-10 text-green-200 dark:text-green-700" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

                <!-- Total Terlambat -->
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-yellow-700 dark:text-yellow-300 font-semibold">{{ __('Total Terlambat') }}</p>
                            <p class="text-2xl font-bold text-yellow-800 dark:text-yellow-200 mt-1">{{ $stats['total_terlambat'] ?? 0 }}</p>
                        </div>
                        <svg class="w-10 h-10 text-yellow-200 dark:text-yellow-700" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

                <!-- Total Alpa -->
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-red-700 dark:text-red-300 font-semibold">{{ __('Total Alpa') }}</p>
                            <p class="text-2xl font-bold text-red-800 dark:text-red-200 mt-1">{{ $stats['total_absent'] ?? 0 }}</p>
                        </div>
                        <svg class="w-10 h-10 text-red-200 dark:text-red-700" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

                <!-- Total Kehadiran -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-700 dark:text-blue-300 font-semibold">{{ __('Total Hari') }}</p>
                            <p class="text-2xl font-bold text-blue-800 dark:text-blue-200 mt-1">{{ $attendances->total() ?? 0 }}</p>
                        </div>
                        <svg class="w-10 h-10 text-blue-200 dark:text-blue-700" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6 2a1 1 0 00-1 1v2H4a2 2 0 00-2 2v2h16V7a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v2H7V3a1 1 0 00-1-1zm0 5H4v9a2 2 0 002 2h12a2 2 0 002-2V7h-2v2a1 1 0 11-2 0V7H9v2a1 1 0 11-2 0V7z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Attendance Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold">{{ __('Tanggal') }}</th>
                                    <th class="px-4 py-3 text-center font-semibold">{{ __('Status') }}</th>
                                    <th class="px-4 py-3 text-center font-semibold">{{ __('Check-in') }}</th>
                                    <th class="px-4 py-3 text-center font-semibold">{{ __('Check-out') }}</th>
                                    <th class="px-4 py-3 text-center font-semibold">{{ __('Durasi') }}</th>
                                    <th class="px-4 py-3 text-left font-semibold">{{ __('Confidence') }}</th>
                                    <th class="px-4 py-3 text-left font-semibold">{{ __('Catatan') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($attendances as $attendance)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <!-- Tanggal -->
                                        <td class="px-4 py-3 font-medium">
                                            {{ $attendance->attendance_date->format('d M Y') }}
                                        </td>

                                        <!-- Status Badge -->
                                        <td class="px-4 py-3 text-center">
                                            @php
                                                $statusColor = $attendance->getStatusBadgeColor();
                                                $statusLabel = $attendance->getStatusLabel();
                                            @endphp
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"
                                                style="background-color: {{ $statusColor === 'green' ? '#d1fae5' : ($statusColor === 'yellow' ? '#fef3c7' : '#fee2e2') }};
                                                       color: {{ $statusColor === 'green' ? '#059669' : ($statusColor === 'yellow' ? '#d97706' : '#dc2626') }};">
                                                {{ $statusLabel }}
                                            </span>
                                        </td>

                                        <!-- Check-in -->
                                        <td class="px-4 py-3 text-center text-sm">
                                            @if ($attendance->check_in_at)
                                                <span class="font-mono">{{ $attendance->getCheckInTimeFormatted() }}</span>
                                                <br>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    ({{ $attendance->getCheckInConfidencePercent() }}%)
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>

                                        <!-- Check-out -->
                                        <td class="px-4 py-3 text-center text-sm">
                                            @if ($attendance->check_out_at)
                                                <span class="font-mono">{{ $attendance->getCheckOutTimeFormatted() }}</span>
                                                <br>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    ({{ $attendance->getCheckOutConfidencePercent() }}%)
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>

                                        <!-- Durasi Kerja -->
                                        <td class="px-4 py-3 text-center font-mono text-sm">
                                            @if ($attendance->check_in_at && $attendance->check_out_at)
                                                {{ $attendance->getWorkDurationFormatted() }}
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>

                                        <!-- Confidence Score -->
                                        <td class="px-4 py-3 text-sm">
                                            @php
                                                $avgConfidence = (($attendance->check_in_confidence ?? 0) + ($attendance->check_out_confidence ?? 0)) / 2;
                                            @endphp
                                            @if ($avgConfidence > 0)
                                                <div class="flex items-center gap-2">
                                                    <div class="w-24 bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $avgConfidence * 100 }}%"></div>
                                                    </div>
                                                    <span class="text-xs font-mono">{{ round($avgConfidence * 100) }}%</span>
                                                </div>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>

                                        <!-- Catatan -->
                                        <td class="px-4 py-3 text-sm">
                                            {{ $attendance->notes ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-16 h-16 mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <p class="text-sm">{{ __('Tidak ada data kehadiran') }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $attendances->links() }}
                    </div>

                </div>
            </div>

            <!-- Info Box -->
            <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <h4 class="font-semibold text-blue-900 dark:text-blue-200 mb-2">{{ __('Keterangan:') }}</h4>
                <ul class="text-sm text-blue-800 dark:text-blue-300 space-y-1">
                    <li>{{ __('• Durasi: Waktu kerja dari check-in hingga check-out') }}</li>
                    <li>{{ __('• Confidence: Tingkat akurasi face recognition (minimal 75% untuk diterima)') }}</li>
                    <li>{{ __('• Status Hadir: Check-in sebelum jam 09:00') }}</li>
                    <li>{{ __('• Status Terlambat: Check-in setelah jam 09:00') }}</li>
                </ul>
            </div>

        </div>
    </div>
</x-app-layout>
