<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Daftar Pengajuan Izin
            </h2>
            <a href="{{ route('terapis.leaves.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition">
                + Ajukan Izin Baru
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

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

            {{-- Filter Tabs --}}
            <div class="mb-6 flex gap-2 border-b border-gray-200 dark:border-gray-700">
                <a href="{{ route('terapis.leaves.index') }}"
                    class="px-4 py-2 font-medium transition {{ !request('status') ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                    Semua
                </a>
                <a href="{{ route('terapis.leaves.index', ['status' => 'pending']) }}"
                    class="px-4 py-2 font-medium transition {{ request('status') === 'pending' ? 'border-b-2 border-yellow-600 text-yellow-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                    ⏳ Menunggu
                </a>
                <a href="{{ route('terapis.leaves.index', ['status' => 'approved']) }}"
                    class="px-4 py-2 font-medium transition {{ request('status') === 'approved' ? 'border-b-2 border-emerald-600 text-emerald-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                    ✓ Disetujui
                </a>
                <a href="{{ route('terapis.leaves.index', ['status' => 'rejected']) }}"
                    class="px-4 py-2 font-medium transition {{ request('status') === 'rejected' ? 'border-b-2 border-red-600 text-red-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                    ✗ Ditolak
                </a>
            </div>

            {{-- No Data Message --}}
            @if ($leaveRequests->isEmpty())
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-12 text-center">
                    <div class="text-5xl mb-4">📋</div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Tidak ada data pengajuan izin
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        @if (request('status'))
                            Anda tidak memiliki pengajuan izin dengan status ini.
                        @else
                            Mulai dengan mengajukan izin baru untuk tidak masuk.
                        @endif
                    </p>
                    @if (!request('status') || request('status') === '')
                        <a href="{{ route('terapis.leaves.create') }}"
                            class="inline-flex items-center px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition">
                            + Ajukan Izin Baru
                        </a>
                    @endif
                </div>
            @else
                {{-- Leave Requests Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($leaveRequests as $leave)
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 hover:shadow-lg transition overflow-hidden">
                            {{-- Header with Status Badge --}}
                            <div
                                class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex justify-between items-start">
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">
                                        @switch($leave->type)
                                            @case('sakit')
                                                🏥 Sakit
                                            @break

                                            @case('pribadi')
                                                👤 Pribadi
                                            @break

                                            @case('cuti')
                                                🏖️ Cuti
                                            @break

                                            @case('izin_khusus')
                                                ⭐ Izin Khusus
                                            @break

                                            @default
                                                📋 Izin
                                        @endswitch
                                    </h3>
                                </div>
                                <span
                                    class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full
                                @switch($leave->status)
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
                                    @switch($leave->status)
                                        @case('pending')
                                            ⏳ Menunggu
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

                            {{-- Body --}}
                            <div class="px-6 py-4 space-y-3">
                                {{-- Date Range --}}
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Periode</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        {{ $leave->start_date->format('d M Y') }} -
                                        {{ $leave->end_date->format('d M Y') }}
                                    </p>
                                </div>

                                {{-- Duration --}}
                                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                    <p class="text-sm font-semibold text-blue-900 dark:text-blue-300">
                                        📅 Durasi: {{ $leave->duration }} hari
                                    </p>
                                </div>

                                {{-- Reason --}}
                                @if ($leave->reason)
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Alasan</p>
                                        <p class="text-sm text-gray-900 dark:text-gray-100 line-clamp-2">
                                            {{ $leave->reason }}
                                        </p>
                                    </div>
                                @endif

                                {{-- Approved Info --}}
                                @if ($leave->status === 'approved' && $leave->approved_at)
                                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
                                        <p class="text-xs text-emerald-700 dark:text-emerald-300">
                                            Disetujui pada {{ $leave->approved_at->format('d M Y H:i') }}
                                        </p>
                                    </div>
                                @endif

                                {{-- Rejection Info --}}
                                @if ($leave->status === 'rejected' && $leave->rejection_reason)
                                    <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                        <p class="text-xs font-semibold text-red-700 dark:text-red-300 mb-1">Alasan
                                            Penolakan:</p>
                                        <p class="text-xs text-red-600 dark:text-red-400">
                                            {{ $leave->rejection_reason }}</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Footer with Actions --}}
                            <div
                                class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex gap-2">
                                <a href="{{ route('terapis.leaves.show', $leave) }}"
                                    class="flex-1 px-3 py-2 text-center text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition">
                                    Lihat Detail
                                </a>

                                @if ($leave->status === 'pending')
                                    <form action="{{ route('terapis.leaves.destroy', $leave) }}" method="POST"
                                        class="flex-1" onsubmit="return confirm('Yakin ingin membatalkan izin ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-full px-3 py-2 text-center text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition">
                                            Batalkan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
