<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Detail Pengajuan Izin
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if (session('success'))
                <div
                    class="mb-6 px-4 py-4 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 mr-2" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="text-emerald-800 dark:text-emerald-300">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">

                {{-- Header --}}
                <div
                    class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-gray-700 dark:to-gray-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                @switch($leaveRequest->type)
                                    @case('sakit')
                                        🏥 Pengajuan Izin Sakit
                                    @break

                                    @case('pribadi')
                                        👤 Pengajuan Izin Pribadi
                                    @break

                                    @case('cuti')
                                        🏖️ Pengajuan Cuti
                                    @break

                                    @case('izin_khusus')
                                        ⭐ Pengajuan Izin Khusus
                                    @break

                                    @default
                                        📋 Pengajuan Izin
                                @endswitch
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Dibuat pada {{ $leaveRequest->created_at->format('d M Y H:i') }}
                            </p>
                        </div>

                        <span
                            class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-full
                        @switch($leaveRequest->status)
                            @case('pending')
                                bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300
                            @break

                            @case('approved')
                                bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300
                            @break

                            @case('rejected')
                                bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                            @break
                        @endswitch
                    ">
                            @switch($leaveRequest->status)
                                @case('pending')
                                    ⏳ Menunggu Persetujuan
                                @break

                                @case('approved')
                                    ✓ Disetujui
                                @break

                                @case('rejected')
                                    ✗ Ditolak
                                @break
                            @endswitch
                        </span>
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-6 space-y-6">

                    {{-- Informasi Dasar --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Tanggal Mulai --}}
                        <div>
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">📅 Tanggal Mulai</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">
                                {{ $leaveRequest->start_date->format('d M Y') }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">
                                {{ $leaveRequest->start_date->translatedFormat('l') }}
                            </p>
                        </div>

                        {{-- Tanggal Selesai --}}
                        <div>
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">📅 Tanggal Selesai</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">
                                {{ $leaveRequest->end_date->format('d M Y') }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">
                                {{ $leaveRequest->end_date->translatedFormat('l') }}
                            </p>
                        </div>
                    </div>

                    {{-- Durasi --}}
                    <div
                        class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg">
                        <p class="text-sm font-semibold text-blue-900 dark:text-blue-300">📊 Total Durasi</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2">
                            {{ $leaveRequest->duration }} Hari
                        </p>
                    </div>

                    {{-- Alasan --}}
                    <div>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">📝 Alasan</p>
                        <div
                            class="p-4 bg-gray-100 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-lg">
                            <p class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ $leaveRequest->reason }}
                            </p>
                        </div>
                    </div>

                    {{-- Status Details --}}
                    @if ($leaveRequest->status === 'approved')
                        <div
                            class="p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 rounded-lg">
                            <div class="flex items-start gap-3">
                                <div class="text-2xl">✓</div>
                                <div>
                                    <p class="font-semibold text-emerald-900 dark:text-emerald-300">Izin Disetujui</p>
                                    <p class="text-sm text-emerald-700 dark:text-emerald-400 mt-1">
                                        Disetujui pada {{ $leaveRequest->approved_at->format('d M Y H:i') }}
                                    </p>
                                    @if ($leaveRequest->approvedBy)
                                        <p class="text-sm text-emerald-700 dark:text-emerald-400">
                                            Oleh: <strong>{{ $leaveRequest->approvedBy->name }}</strong>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @elseif ($leaveRequest->status === 'rejected')
                        <div
                            class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg">
                            <div class="flex items-start gap-3">
                                <div class="text-2xl">✗</div>
                                <div>
                                    <p class="font-semibold text-red-900 dark:text-red-300">Izin Ditolak</p>
                                    @if ($leaveRequest->rejection_reason)
                                        <p class="text-sm text-red-700 dark:text-red-400 mt-2">
                                            <strong>Alasan Penolakan:</strong>
                                        </p>
                                        <p class="text-sm text-red-700 dark:text-red-400 mt-1">
                                            {{ $leaveRequest->rejection_reason }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div
                            class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                            <div class="flex items-start gap-3">
                                <div class="text-2xl">⏳</div>
                                <div>
                                    <p class="font-semibold text-yellow-900 dark:text-yellow-300">Menunggu Persetujuan
                                    </p>
                                    <p class="text-sm text-yellow-700 dark:text-yellow-400 mt-1">
                                        Pengajuan Anda sedang dalam proses review oleh admin. Anda akan diberitahu
                                        segera.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>

                {{-- Footer --}}
                <div
                    class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex gap-3 justify-between">
                    <a href="{{ route('terapis.leaves.index') }}"
                        class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 font-medium transition">
                        ← Kembali
                    </a>

                    @if ($leaveRequest->status === 'pending')
                        <form action="{{ route('terapis.leaves.destroy', $leaveRequest) }}" method="POST"
                            onsubmit="return confirm('Yakin ingin membatalkan izin ini? Anda tidak bisa mengembalikan tindakan ini.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition">
                                Batalkan Pengajuan
                            </button>
                        </form>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
