<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Admin Dashboard
        </h2>
    </x-slot>

    <style>
        .stat-card {
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
        }
        .stat-card.gold::before  { background: linear-gradient(90deg, #F59E0B, #FCD34D); }
        .stat-card.green::before { background: linear-gradient(90deg, #10B981, #6EE7B7); }
        .stat-card.blue::before  { background: linear-gradient(90deg, #3B82F6, #93C5FD); }
        .stat-card.purple::before{ background: linear-gradient(90deg, #8B5CF6, #C4B5FD); }

        .section-title {
            font-size: 15px;
            font-weight: 700;
            color: #374151;
        }
        .dark .section-title { color: #E5E7EB; }

        .status-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ===== STAT CARDS ===== --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

                <div class="stat-card gold bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2 bg-amber-50 dark:bg-amber-900/30 rounded-lg">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $todayBookings }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Booking Hari Ini</div>
                </div>

                {{-- ✅ DIPERBAIKI: dari "/ 1000000, 1) }}jt" → number_format penuh --}}
                <div class="stat-card green bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        Rp {{ number_format($monthRevenue, 0, ',', '.') }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pendapatan Bulan Ini</div>
                </div>

                <div class="stat-card blue bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalTherapists }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Terapis</div>
                </div>

                <div class="stat-card purple bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2 bg-violet-50 dark:bg-violet-900/30 rounded-lg">
                            <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalCustomers }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Pelanggan</div>
                </div>
            </div>

            {{-- ===== ROW 2: BOOKING + TERAPIS ===== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                {{-- Recent Bookings --}}
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                        <span class="section-title">Booking Terbaru</span>
                        <a href="{{ route('admin.bookings.index') }}"
                           class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                            Lihat Semua →
                        </a>
                    </div>
                    <div class="divide-y divide-gray-50 dark:divide-gray-700">
                        @forelse($recentBookings as $booking)
                        <div class="flex items-center gap-4 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-sm flex-shrink-0">
                                {{ strtoupper(substr($booking->customer->name ?? '?', 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">
                                    {{ $booking->customer->name ?? '—' }}
                                </div>
                                <div class="text-xs text-gray-400 truncate">
                                    {{ $booking->service->name ?? '—' }} • {{ $booking->therapist->name ?? '—' }}
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0 text-right">
                                <div>{{ \Carbon\Carbon::parse($booking->scheduled_at)->format('d M') }}</div>
                                <div>{{ \Carbon\Carbon::parse($booking->scheduled_at)->format('H:i') }}</div>
                            </div>
                            <div class="flex-shrink-0">
                                @php
                                    $cls = match($booking->status) {
                                        'scheduled'  => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400',
                                        'completed'  => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400',
                                        'cancelled'  => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400',
                                        'ongoing'    => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400',
                                        default      => 'bg-gray-100 text-gray-500',
                                    };
                                    $lbl = match($booking->status) {
                                        'scheduled'  => 'Terjadwal',
                                        'completed'  => 'Selesai',
                                        'cancelled'  => 'Batal',
                                        'ongoing'    => 'Berlangsung',
                                        default      => $booking->status
                                    };
                                @endphp
                                <span class="status-badge {{ $cls }}">{{ $lbl }}</span>
                            </div>
                            <a href="{{ route('admin.bookings.edit', $booking) }}"
                               class="flex-shrink-0 text-xs text-gray-400 hover:text-indigo-500 dark:hover:text-indigo-400 transition-colors">
                                Edit
                            </a>
                        </div>
                        @empty
                        <div class="text-center py-12 text-gray-400 text-sm">
                            Belum ada booking.
                            <a href="{{ route('admin.bookings.create') }}" class="text-indigo-500 hover:underline ms-1">Buat sekarang</a>
                        </div>
                        @endforelse
                    </div>
                    @if($recentBookings->count())
                    <div class="px-5 py-3 border-t border-gray-50 dark:border-gray-700 text-center">
                        <a href="{{ route('admin.bookings.create') }}"
                           class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                            + Buat Booking Baru
                        </a>
                    </div>
                    @endif
                </div>

                {{-- Therapist Status --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                        <span class="section-title">Status Terapis</span>
                        <a href="{{ route('admin.therapists.index') }}"
                           class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                            Kelola →
                        </a>
                    </div>
                    <div class="divide-y divide-gray-50 dark:divide-gray-700">
                        @forelse($therapists as $terapis)
                        <div class="flex items-center gap-3 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                {{ strtoupper(substr($terapis->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $terapis->name }}</div>
                                <div class="text-xs text-gray-400 truncate">{{ $terapis->specialty ?? 'Terapis' }}</div>
                            </div>
                            <span class="flex-shrink-0 text-xs font-semibold {{ $terapis->is_active ? 'text-emerald-500' : 'text-red-400' }}">
                                ● {{ $terapis->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        @empty
                        <div class="text-center py-12 text-gray-400 text-sm">
                            Belum ada terapis.
                            <a href="{{ route('admin.therapists.create') }}" class="text-indigo-500 hover:underline">Tambah</a>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ===== ROW 3: BELUM BAYAR + LAYANAN POPULER ===== --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                {{-- Unpaid --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                        <span class="section-title">
                            Menunggu Pembayaran
                            @if($unpaidBookings->count())
                                <span class="ms-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full">
                                    {{ $unpaidBookings->count() }}
                                </span>
                            @endif
                        </span>
                        <a href="{{ route('admin.payments.create') }}"
                           class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                            + Catat →
                        </a>
                    </div>
                    <div class="divide-y divide-gray-50 dark:divide-gray-700">
                        @forelse($unpaidBookings as $booking)
                        <div class="flex items-center gap-3 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $booking->customer->name ?? '—' }}</div>
                                <div class="text-xs text-gray-400">{{ $booking->service->name ?? '—' }}</div>
                            </div>
                            <div class="text-sm font-bold text-amber-600 dark:text-amber-400">
                                Rp {{ number_format($booking->final_price, 0, ',', '.') }}
                            </div>
                            <a href="{{ route('admin.payments.create') }}?booking_id={{ $booking->id }}"
                               class="flex-shrink-0 px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg transition-colors">
                                Bayar
                            </a>
                        </div>
                        @empty
                        <div class="text-center py-12 text-gray-400 text-sm">
                            ✓ Semua booking sudah dibayar
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Top Services --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                        <span class="section-title">Layanan Terpopuler</span>
                        <a href="{{ route('admin.services.index') }}"
                           class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                            Kelola →
                        </a>
                    </div>
                    <div class="divide-y divide-gray-50 dark:divide-gray-700">
                        @forelse($topServices as $i => $svc)
                        <div class="flex items-center gap-4 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-lg flex-shrink-0">
                                {{ ['💆','🌿','🪨','👣','🌸'][$i] ?? '✦' }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $svc->name }}</div>
                                <div class="text-xs text-gray-400">Rp {{ number_format($svc->price, 0, ',', '.') }}</div>
                            </div>
                            <span class="flex-shrink-0 px-2.5 py-1 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 text-xs font-bold rounded-lg">
                                {{ $svc->bookings_count }} sesi
                            </span>
                        </div>
                        @empty
                        <div class="text-center py-12 text-gray-400 text-sm">
                            Belum ada data layanan.
                            <a href="{{ route('admin.services.create') }}" class="text-indigo-500 hover:underline">Tambah</a>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
