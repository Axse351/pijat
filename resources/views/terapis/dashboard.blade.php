<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Dashboard Terapis
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Alert Status --}}
            @if ($activeLeave)
                <div
                    class="mb-6 px-4 py-4 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700 rounded-lg text-amber-800 dark:text-amber-300">
                    <div class="font-semibold mb-1">🏖️ Anda sedang dalam periode izin</div>
                    <p class="text-sm">
                        {{ TherapistLeaveRequest::getTypeLabel($activeLeave->type) }}
                        dari {{ $activeLeave->start_date->format('d M') }} hingga
                        {{ $activeLeave->end_date->format('d M Y') }}
                    </p>
                </div>
            @endif

            @if ($pendingLeaves->count() > 0)
                <div
                    class="mb-6 px-4 py-4 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg text-blue-800 dark:text-blue-300">
                    <div class="font-semibold mb-1">⏳ Pengajuan izin menunggu persetujuan</div>
                    <p class="text-sm mb-2">
                        Anda memiliki {{ $pendingLeaves->count() }} pengajuan izin yang menunggu persetujuan admin.
                    </p>
                    <a href="{{ route('terapis.leaves.index') }}"
                        class="inline-block text-sm font-semibold hover:underline">
                        Lihat status →
                    </a>
                </div>
            @endif

            {{-- Stats Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                {{-- Booking Hari Ini --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $todayBookingsCount }}</div>
                        <div class="text-2xl">📅</div>
                    </div>
                    <p class="text-sm text-gray-500">Booking Hari Ini</p>
                </div>

                {{-- Pendapatan Hari Ini --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-3xl font-bold text-emerald-600">Rp
                            {{ number_format($todayRevenue / 1000, 0) }}rb</div>
                        <div class="text-2xl">💰</div>
                    </div>
                    <p class="text-sm text-gray-500">Pendapatan Hari Ini</p>
                </div>

                {{-- Pendapatan Bulan Ini --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-3xl font-bold text-indigo-600">Rp
                            {{ number_format($monthRevenue / 1000000, 1) }}jt</div>
                        <div class="text-2xl">📈</div>
                    </div>
                    <p class="text-sm text-gray-500">Pendapatan Bulan Ini</p>
                </div>

                {{-- Komisi --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-3xl font-bold text-amber-600">{{ $therapist->commission_percent }}%</div>
                        <div class="text-2xl">💳</div>
                    </div>
                    <p class="text-sm text-gray-500">Komisi</p>
                </div>
            </div>

            {{-- Main Content Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                {{-- Jadwal Hari Ini --}}
                <div
                    class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-900 dark:text-white">📅 Jadwal Hari Ini</h3>
                    </div>
                    <div class="p-6">
                        @if ($todaySchedule)
                            <div
                                class="p-4 bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-900/30 dark:to-blue-900/30 border border-indigo-200 dark:border-indigo-700 rounded-lg">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <div class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ match ($todaySchedule->status) {
                                                'working' => '🌅 Shift Pagi',
                                                'working_afternoon' => '🌤️ Shift Siang',
                                                'off' => '🎉 Hari Libur',
                                                'sick' => '🏥 Sakit',
                                                'vacation' => '🏖️ Cuti',
                                                default => ucfirst(str_replace('_', ' ', $todaySchedule->status)),
                                            } }}
                                        </div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            Status:
                                            <strong>{{ ucfirst(str_replace('_', ' ', $todaySchedule->status)) }}</strong>
                                        </div>
                                    </div>
                                    @if (in_array($todaySchedule->status, ['working', 'working_afternoon']))
                                        <span
                                            class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 text-sm font-semibold rounded">
                                            {{ \Carbon\Carbon::parse($todaySchedule->start_time)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($todaySchedule->end_time)->format('H:i') }}
                                        </span>
                                    @else
                                        <span
                                            class="px-3 py-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-semibold rounded">
                                            Tidak Bekerja
                                        </span>
                                    @endif
                                </div>
                                @if ($todaySchedule->notes)
                                    <div
                                        class="text-sm text-gray-700 dark:text-gray-300 border-t border-indigo-200 dark:border-indigo-700 pt-3 mt-3">
                                        <strong>Catatan:</strong> {{ $todaySchedule->notes }}
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <div class="text-4xl mb-2">📭</div>
                                <p>Tidak ada jadwal untuk hari ini</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Status Booking --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-900 dark:text-white">📊 Status Booking</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @php
                                $statuses = [
                                    [
                                        'label' => 'Terjadwal',
                                        'value' => $statusSummary['scheduled'],
                                        'icon' => '⏰',
                                        'color' => 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
                                    ],
                                    [
                                        'label' => 'Berlangsung',
                                        'value' => $statusSummary['ongoing'],
                                        'icon' => '▶️',
                                        'color' =>
                                            'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300',
                                    ],
                                    [
                                        'label' => 'Selesai',
                                        'value' => $statusSummary['completed'],
                                        'icon' => '✅',
                                        'color' =>
                                            'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300',
                                    ],
                                    [
                                        'label' => 'Batal',
                                        'value' => $statusSummary['cancelled'],
                                        'icon' => '❌',
                                        'color' => 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300',
                                    ],
                                ];
                            @endphp

                            @foreach ($statuses as $status)
                                <div class="p-3 {{ $status['color'] }} rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg">{{ $status['icon'] }}</span>
                                            <span class="text-sm font-medium">{{ $status['label'] }}</span>
                                        </div>
                                        <span class="font-bold text-lg">{{ $status['value'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Booking & Services Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Booking Mendatang --}}
                <div
                    class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div
                        class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900 dark:text-white">📋 Booking Mendatang</h3>
                        <a href="{{ route('terapis.bookings.index') }}"
                            class="text-sm text-indigo-600 hover:text-indigo-700 font-semibold">
                            Lihat Semua →
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        @php
                            $upcomingBookings = Booking::where('therapist_id', $therapist->id)
                                ->where('scheduled_at', '>=', now())
                                ->orderBy('scheduled_at')
                                ->take(5)
                                ->get();
                        @endphp
                        @if ($upcomingBookings->count())
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                                        <th
                                            class="text-left px-6 py-3 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">
                                            Pelanggan</th>
                                        <th
                                            class="text-left px-6 py-3 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">
                                            Layanan</th>
                                        <th
                                            class="text-left px-6 py-3 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">
                                            Jadwal</th>
                                        <th
                                            class="text-left px-6 py-3 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($upcomingBookings as $booking)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="px-6 py-3 font-medium text-gray-900 dark:text-white">
                                                {{ $booking->customer->name ?? '-' }}
                                            </td>
                                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">
                                                {{ $booking->service->name ?? '-' }}
                                            </td>
                                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">
                                                {{ \Carbon\Carbon::parse($booking->scheduled_at)->format('d M, H:i') }}
                                            </td>
                                            <td class="px-6 py-3">
                                                @php
                                                    $sc = match ($booking->status) {
                                                        'scheduled' => 'bg-blue-100 text-blue-700',
                                                        'completed' => 'bg-emerald-100 text-emerald-700',
                                                        'cancelled' => 'bg-red-100 text-red-700',
                                                        'ongoing' => 'bg-purple-100 text-purple-700',
                                                        default => 'bg-gray-100 text-gray-700',
                                                    };
                                                    $sl = match ($booking->status) {
                                                        'scheduled' => 'Terjadwal',
                                                        'completed' => 'Selesai',
                                                        'cancelled' => 'Batal',
                                                        'ongoing' => 'Berlangsung',
                                                        default => $booking->status,
                                                    };
                                                @endphp
                                                <span
                                                    class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $sc }}">
                                                    {{ $sl }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center py-8 text-gray-500 px-6">
                                <div class="text-3xl mb-2">📭</div>
                                <p>Tidak ada booking mendatang</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Layanan Terpopuler --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-900 dark:text-white">💆 Layanan Terpopuler</h3>
                    </div>
                    <div class="p-6">
                        @if ($topServices->count())
                            <div class="space-y-4">
                                @foreach ($topServices as $svc)
                                    @php
                                        $maxCount = $topServices->first()['count'] ?? 1;
                                        $barPct = $maxCount > 0 ? ($svc['count'] / $maxCount) * 100 : 0;
                                    @endphp
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                {{ $svc['service_name'] }}
                                            </div>
                                            <span
                                                class="px-2.5 py-1 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 text-xs font-semibold rounded">
                                                {{ $svc['count'] }}
                                            </span>
                                        </div>
                                        <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                            <div class="h-full bg-indigo-600" style="width: {{ $barPct }}%;">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <p>Belum ada data</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
