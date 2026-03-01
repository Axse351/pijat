<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kelola Kehadiran Terapis') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Alert Messages -->
            @if ($errors->any())
                <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <p class="text-red-700 dark:text-red-300 font-semibold mb-2">{{ __('Terjadi Kesalahan:') }}</p>
                    <ul class="list-disc list-inside text-red-600 dark:text-red-400 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <p class="text-green-700 dark:text-green-300">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <p class="text-red-700 dark:text-red-300">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Main Table Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold">{{ __('No') }}</th>
                                    <th class="px-4 py-3 text-left font-semibold">{{ __('Nama Terapis') }}</th>
                                    <th class="px-4 py-3 text-left font-semibold">{{ __('Email') }}</th>
                                    <th class="px-4 py-3 text-center font-semibold">{{ __('Status Wajah') }}</th>
                                    <th class="px-4 py-3 text-center font-semibold">{{ __('Status Hari Ini') }}</th>
                                    <th class="px-4 py-3 text-center font-semibold">{{ __('Aksi') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($therapists as $index => $therapist)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <td class="px-4 py-3">{{ ($therapists->currentPage() - 1) * $therapists->perPage() + $index + 1 }}</td>

                                        <!-- Nama Terapis -->
                                        <td class="px-4 py-3 font-medium">
                                            {{ $therapist->name }}
                                        </td>

                                        <!-- Email -->
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">
                                            {{ $therapist->email ?? '-' }}
                                        </td>

                                        <!-- Status Wajah -->
                                        <td class="px-4 py-3 text-center">
                                            @if ($therapist->faceData?->isVerified())
                                                <span class="inline-flex items-center px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full text-xs font-semibold">
                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    {{ __('Terverifikasi') }}
                                                </span>
                                            @elseif ($therapist->faceData?->isPending())
                                                <span class="inline-flex items-center px-3 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 rounded-full text-xs font-semibold">
                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                    {{ __('Menunggu Verifikasi') }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-xs font-semibold">
                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                    </svg>
                                                    {{ __('Belum Terdaftar') }}
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Status Hari Ini -->
                                        <td class="px-4 py-3 text-center">
                                            @php
                                                $todayAttendance = $therapist->attendances->first();
                                            @endphp

                                            @if ($todayAttendance)
                                                @if ($todayAttendance->isCheckedOut())
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                                                        ✓ {{ $todayAttendance->getStatusLabel() }}
                                                    </span>
                                                    <br>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ __('Keluar:') }} {{ $todayAttendance->getCheckOutTimeFormatted() }}
                                                    </span>
                                                @elseif ($todayAttendance->isCheckedIn())
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                                        ⏱ {{ __('Check-in') }}
                                                    </span>
                                                    <br>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $todayAttendance->getCheckInTimeFormatted() }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">
                                                        ✗ {{ $todayAttendance->getStatusLabel() }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-gray-500 dark:text-gray-400">{{ __('-') }}</span>
                                            @endif
                                        </td>

                                        <!-- Actions -->
                                        <td class="px-4 py-3">
                                            <div class="flex justify-center gap-2 flex-wrap">
                                                <!-- Register/Update Face Button -->
                                                <a href="{{ route('admin.therapist-face.register', $therapist->id) }}"
                                                   class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs font-semibold transition"
                                                   title="{{ __('Daftar/Perbarui Wajah') }}">
                                                    📸 {{ __('Wajah') }}
                                                </a>

                                                <!-- Verify Button (only for pending) -->
                                                @if ($therapist->faceData?->isPending())
                                                    <form action="{{ route('admin.therapist-face.verify', $therapist->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit"
                                                            class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded text-xs font-semibold transition"
                                                            title="{{ __('Verifikasi Wajah') }}">
                                                            ✓ {{ __('Verifikasi') }}
                                                        </button>
                                                    </form>
                                                @endif

                                                <!-- Check-in Button -->
                                                @php
                                                    $canCheckIn = $therapist->hasFaceVerified() && !$therapist->isCheckedInToday();
                                                @endphp

                                                @if ($canCheckIn)
                                                    <a href="{{ route('attendance.check-in-camera', $therapist->id) }}"
                                                       class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-xs font-semibold transition"
                                                       title="{{ __('Check-in dengan Kamera') }}">
                                                        📷 {{ __('Check-in') }}
                                                    </a>
                                                @else
                                                    <button disabled
                                                        class="px-3 py-1 bg-gray-400 cursor-not-allowed text-white rounded text-xs font-semibold"
                                                        title="{{ $therapist->isCheckedInToday() ? __('Sudah check-in hari ini') : __('Wajah belum diverifikasi') }}">
                                                        📷 {{ __('Check-in') }}
                                                    </button>
                                                @endif

                                                <!-- Check-out Button -->
                                                @php
                                                    $todayAttendance = $therapist->attendances->first();
                                                    $canCheckOut = $therapist->hasFaceVerified() && $todayAttendance && $todayAttendance->check_in_at && !$todayAttendance->check_out_at;
                                                @endphp

                                                @if ($canCheckOut)
                                                    <a href="{{ route('attendance.check-out-camera', $therapist->id) }}"
                                                       class="px-3 py-1 bg-orange-500 hover:bg-orange-600 text-white rounded text-xs font-semibold transition"
                                                       title="{{ __('Check-out dengan Kamera') }}">
                                                        📷 {{ __('Check-out') }}
                                                    </a>
                                                @else
                                                    <button disabled
                                                        class="px-3 py-1 bg-gray-400 cursor-not-allowed text-white rounded text-xs font-semibold"
                                                        title="{{ !$todayAttendance || !$todayAttendance->check_in_at ? __('Belum check-in') : __('Sudah check-out') }}">
                                                        📷 {{ __('Check-out') }}
                                                    </button>
                                                @endif

                                                <!-- History Button -->
                                                @if ($therapist->faceData?->isVerified())
                                                    <a href="{{ route('admin.attendance.history', $therapist->id) }}"
                                                       class="px-3 py-1 bg-purple-500 hover:bg-purple-600 text-white rounded text-xs font-semibold transition"
                                                       title="{{ __('Lihat Riwayat Kehadiran') }}">
                                                        📋 {{ __('Riwayat') }}
                                                    </a>
                                                @endif

                                                <!-- Delete Face Button -->
                                                @if ($therapist->faceData)
                                                    <form action="{{ route('admin.therapist-face.destroy', $therapist->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('{{ __('Hapus data wajah ini?') }}');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded text-xs font-semibold transition"
                                                            title="{{ __('Hapus Data Wajah') }}">
                                                            🗑️ {{ __('Hapus') }}
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-16 h-16 mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3.914a.5.5 0 01-.5-.5V5.414a.5.5 0 01.5-.5h2.172a.5.5 0 00.353-.147l2.828-2.828a.5.5 0 01.707 0l2.828 2.828a.5.5 0 00.353.147h2.172a.5.5 0 01.5.5v15.086a.5.5 0 01-.5.5z" />
                                                </svg>
                                                <p class="text-sm">{{ __('Tidak ada data terapis') }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $therapists->links() }}
                    </div>

                </div>
            </div>

            <!-- Legend -->
            <div class="mt-6 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">{{ __('Keterangan Tombol:') }}</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="font-semibold text-blue-600 dark:text-blue-400">📸 {{ __('Wajah') }}</p>
                        <p class="text-gray-600 dark:text-gray-400">{{ __('Registrasi atau perbarui wajah') }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-yellow-600 dark:text-yellow-400">📷 {{ __('Check-in') }}</p>
                        <p class="text-gray-600 dark:text-gray-400">{{ __('Absensi masuk dengan kamera') }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-orange-600 dark:text-orange-400">📷 {{ __('Check-out') }}</p>
                        <p class="text-gray-600 dark:text-gray-400">{{ __('Absensi keluar dengan kamera') }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-purple-600 dark:text-purple-400">📋 {{ __('Riwayat') }}</p>
                        <p class="text-gray-600 dark:text-gray-400">{{ __('Lihat riwayat kehadiran') }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-green-600 dark:text-green-400">✓ {{ __('Verifikasi') }}</p>
                        <p class="text-gray-600 dark:text-gray-400">{{ __('Admin verifikasi wajah') }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-red-600 dark:text-red-400">🗑️ {{ __('Hapus') }}</p>
                        <p class="text-gray-600 dark:text-gray-400">{{ __('Hapus data wajah') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Styling untuk status badges */
        .badge-present {
            background-color: #10b981;
        }
        .badge-late {
            background-color: #f59e0b;
        }
        .badge-absent {
            background-color: #ef4444;
        }
    </style>
</x-app-layout>
