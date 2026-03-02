@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', \Carbon\Carbon::now()->translatedFormat('l\, d F Y'))

@section('topbar-actions')
    <a href="{{ route('admin.bookings.create') }}" class="btn-gold">+ Booking Baru</a>
@endsection

@section('content')

    {{-- STATS --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="stat-value">{{ $todayBookings }}</div>
            <div class="stat-label">Booking Hari Ini</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="stat-value">{{ number_format($monthRevenue / 1000000, 1) }}jt</div>
            <div class="stat-label">Pendapatan Bulan Ini</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0" />
                </svg>
            </div>
            <div class="stat-value">{{ $totalTherapists }}</div>
            <div class="stat-label">Total Terapis</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div class="stat-value">{{ $totalCustomers }}</div>
            <div class="stat-label">Total Pelanggan</div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- PRIORITAS TERAPIS HARI INI                                        --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="card" style="margin-bottom:22px;">
        <div class="card-header">
            <div style="display:flex;align-items:center;gap:10px;">
                <span class="card-title">🎯 Prioritas Terapis Hari Ini</span>
                <span
                    style="font-size:11px;color:var(--text-muted);font-weight:400;background:rgba(201,168,76,.1);border:1px solid rgba(201,168,76,.2);padding:3px 10px;border-radius:100px;">
                    Berdasarkan sesi kemarin · diurutkan paling sedikit
                </span>
            </div>
            <a href="{{ route('admin.bookings.create') }}" class="btn-gold" style="font-size:12px;padding:7px 14px;">+
                Assign Booking</a>
        </div>
        <div class="card-body" style="padding:16px;">
            @if ($prioritasHariIni->count())
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:12px;">
                    @foreach ($prioritasHariIni as $i => $terapis)
                        @php
                            // Warna rank: emas=1, perak=2, perunggu=3, abu-abu=sisanya
                            $rankColor = match (true) {
                                $i === 0 => [
                                    'bg' => 'rgba(201,168,76,.15)',
                                    'border' => 'rgba(201,168,76,.4)',
                                    'badge' => '#C9A84C',
                                    'label' => '#C9A84C',
                                ],
                                $i === 1 => [
                                    'bg' => 'rgba(180,180,190,.1)',
                                    'border' => 'rgba(180,180,190,.3)',
                                    'badge' => '#A0A0B0',
                                    'label' => '#A0A0B0',
                                ],
                                $i === 2 => [
                                    'bg' => 'rgba(180,110,60,.1)',
                                    'border' => 'rgba(180,110,60,.3)',
                                    'badge' => '#B46E3C',
                                    'label' => '#B46E3C',
                                ],
                                default => [
                                    'bg' => 'rgba(255,255,255,.03)',
                                    'border' => 'rgba(255,255,255,.06)',
                                    'badge' => '#555',
                                    'label' => 'var(--text-muted)',
                                ],
                            };
                            $rankIcon = match (true) {
                                $i === 0 => '🥇',
                                $i === 1 => '🥈',
                                $i === 2 => '🥉',
                                default => '#' . ($i + 1),
                            };
                        @endphp
                        <div
                            style="
                    display:flex;align-items:center;gap:12px;
                    padding:12px 14px;
                    background:{{ $rankColor['bg'] }};
                    border:1px solid {{ $rankColor['border'] }};
                    border-radius:12px;
                    position:relative;
                ">
                            {{-- Rank badge --}}
                            <div
                                style="
                        position:absolute;top:-8px;left:10px;
                        font-size:11px;font-weight:700;
                        color:{{ $rankColor['label'] }};
                        background:var(--dark-2);
                        padding:1px 7px;border-radius:100px;
                        border:1px solid {{ $rankColor['border'] }};
                    ">
                                {{ $rankIcon }} Prioritas {{ $i + 1 }}</div>

                            {{-- Avatar --}}
                            <div
                                style="
                        width:42px;height:42px;border-radius:50%;
                        background:linear-gradient(135deg,#2A2A2A,#3A3A3A);
                        border:2px solid {{ $rankColor['badge'] }};
                        display:flex;align-items:center;justify-content:center;
                        font-family:'Playfair Display',serif;font-size:15px;
                        color:{{ $rankColor['badge'] }};flex-shrink:0;
                    ">
                                {{ strtoupper(substr($terapis->name, 0, 1)) }}</div>

                            {{-- Info --}}
                            <div style="flex:1;min-width:0;">
                                <div
                                    style="font-size:13px;font-weight:600;color:#F0EDE8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ $terapis->name }}
                                </div>
                                <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">
                                    {{ $terapis->specialty ?? 'Terapis' }}
                                </div>
                                {{-- Sesi kemarin & hari ini --}}
                                <div style="display:flex;gap:8px;margin-top:6px;flex-wrap:wrap;">
                                    <span
                                        style="
                                font-size:10px;font-weight:600;
                                padding:2px 8px;border-radius:100px;
                                background:rgba(224,90,90,.12);
                                color:#E05A5A;
                                border:1px solid rgba(224,90,90,.2);
                            ">
                                        Kemarin: {{ $terapis->sesi_kemarin }} sesi
                                    </span>
                                    <span
                                        style="
                                font-size:10px;font-weight:600;
                                padding:2px 8px;border-radius:100px;
                                background:rgba(76,175,138,.12);
                                color:#4CAF8A;
                                border:1px solid rgba(76,175,138,.2);
                            ">
                                        Hari ini: {{ $terapis->sesi_hari_ini }} sesi
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Catatan kaki --}}
                <div
                    style="margin-top:14px;padding:10px 14px;background:rgba(201,168,76,.05);border:1px solid rgba(201,168,76,.1);border-radius:8px;font-size:11px;color:var(--text-muted);display:flex;align-items:center;gap:8px;">
                    <span style="font-size:14px;">💡</span>
                    <span>Terapis dengan sesi paling sedikit kemarin sebaiknya diutamakan untuk booking berikutnya agar
                        beban kerja merata.</span>
                </div>
            @else
                <div class="empty-state">
                    <p>Belum ada data terapis aktif</p>
                </div>
            @endif
        </div>
    </div>

    {{-- TWO COLUMN --}}
    <div style="display:grid; grid-template-columns: 1.5fr 1fr; gap:22px; margin-bottom:22px;">

        {{-- RECENT BOOKINGS --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Booking Terbaru</span>
                <a href="{{ route('admin.bookings.index') }}" class="btn-outline"
                    style="padding:6px 14px; font-size:12px;">Lihat Semua</a>
            </div>
            <div class="card-body">
                @if ($recentBookings->count())
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Jadwal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentBookings as $booking)
                                <tr>
                                    <td class="text-main">{{ $booking->customer->name ?? '-' }}</td>
                                    <td>{{ $booking->service->name ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($booking->scheduled_at)->format('d M, H:i') }}</td>
                                    <td>
                                        @php
                                            $statusClass = match ($booking->status) {
                                                'scheduled' => 'badge-gold',
                                                'completed' => 'badge-green',
                                                'cancelled' => 'badge-red',
                                                'ongoing' => 'badge-blue',
                                                default => 'badge-gray',
                                            };
                                            $statusLabel = match ($booking->status) {
                                                'scheduled' => 'Terjadwal',
                                                'completed' => 'Selesai',
                                                'cancelled' => 'Batal',
                                                'ongoing' => 'Berlangsung',
                                                default => $booking->status,
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p>Belum ada booking</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- THERAPIST STATUS --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Status Terapis</span>
                <a href="{{ route('admin.therapists.index') }}" class="btn-outline"
                    style="padding:6px 14px; font-size:12px;">Kelola</a>
            </div>
            <div class="card-body" style="padding:16px;">
                <div style="display:flex; flex-direction:column; gap:10px;">
                    @forelse($therapists as $terapis)
                        <div
                            style="display:flex; align-items:center; gap:12px; padding:10px 12px; background:var(--dark-4); border-radius:10px;">
                            <div
                                style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#2A2A2A,#3A3A3A);border:2px solid rgba(201,168,76,0.3);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:14px;color:var(--gold);flex-shrink:0;">
                                {{ strtoupper(substr($terapis->name, 0, 1)) }}
                            </div>
                            <div style="flex:1;">
                                <div style="font-size:13px;font-weight:600;color:#F0EDE8;">{{ $terapis->name }}</div>
                                <div style="font-size:11px;color:var(--text-muted);">{{ $terapis->specialty ?? 'Terapis' }}
                                </div>
                            </div>
                            <span
                                style="font-size:11px;font-weight:600;color:{{ $terapis->is_active ? '#4CAF8A' : '#E05A5A' }};">
                                ● {{ $terapis->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    @empty
                        <div class="empty-state">
                            <p>Belum ada terapis</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- BOTTOM ROW --}}
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:22px;">

        {{-- PENDING PAYMENTS --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Belum Dibayar</span>
                <a href="{{ route('admin.payments.create') }}" class="btn-gold"
                    style="font-size:12px; padding:7px 14px;">+ Catat Bayar</a>
            </div>
            <div class="card-body">
                @if ($unpaidBookings->count())
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($unpaidBookings as $booking)
                                <tr>
                                    <td class="text-main">{{ $booking->customer->name ?? '-' }}</td>
                                    <td>{{ $booking->service->name ?? '-' }}</td>
                                    <td style="color:var(--gold);font-weight:600;">Rp
                                        {{ number_format($booking->final_price, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <p>Semua booking sudah dibayar ✓</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- TOP SERVICES --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Layanan Terpopuler</span>
                <a href="{{ route('admin.services.index') }}" class="btn-outline"
                    style="padding:6px 14px;font-size:12px;">Kelola</a>
            </div>
            <div class="card-body" style="padding:16px;">
                <div style="display:flex;flex-direction:column;gap:10px;">
                    @forelse($topServices as $svc)
                        <div
                            style="display:flex;align-items:center;gap:12px;padding:10px 14px;background:var(--dark-4);border-radius:10px;">
                            <div
                                style="width:32px;height:32px;background:rgba(201,168,76,0.1);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;">
                                💆</div>
                            <div style="flex:1;">
                                <div style="font-size:13px;font-weight:600;color:#F0EDE8;">{{ $svc->name }}</div>
                                <div style="font-size:11px;color:var(--text-muted);">Rp
                                    {{ number_format($svc->price, 0, ',', '.') }}</div>
                            </div>
                            <span class="badge badge-gold">{{ $svc->bookings_count }} sesi</span>
                        </div>
                    @empty
                        <div class="empty-state">
                            <p>Belum ada data layanan</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

@endsection
