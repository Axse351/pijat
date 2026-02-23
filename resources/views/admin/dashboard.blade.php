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
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div class="stat-value">{{ $todayBookings }}</div>
            <div class="stat-label">Booking Hari Ini</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="stat-value">{{ number_format($monthRevenue / 1000000, 1) }}jt</div>
            <div class="stat-label">Pendapatan Bulan Ini</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
            </div>
            <div class="stat-value">{{ $totalTherapists }}</div>
            <div class="stat-label">Total Terapis</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div class="stat-value">{{ $totalCustomers }}</div>
            <div class="stat-label">Total Pelanggan</div>
        </div>
    </div>

    {{-- TWO COLUMN --}}
    <div style="display:grid; grid-template-columns: 1.5fr 1fr; gap:22px; margin-bottom:22px;">

        {{-- RECENT BOOKINGS --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Booking Terbaru</span>
                <a href="{{ route('admin.bookings.index') }}" class="btn-outline" style="padding:6px 14px; font-size:12px;">Lihat Semua</a>
            </div>
            <div class="card-body">
                @if($recentBookings->count())
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
                        @foreach($recentBookings as $booking)
                        <tr>
                            <td class="text-main">{{ $booking->customer->name ?? '-' }}</td>
                            <td>{{ $booking->service->name ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->scheduled_at)->format('d M, H:i') }}</td>
                            <td>
                                @php
                                    $statusClass = match($booking->status) {
                                        'scheduled'  => 'badge-gold',
                                        'completed'  => 'badge-green',
                                        'cancelled'  => 'badge-red',
                                        'ongoing'    => 'badge-blue',
                                        default      => 'badge-gray'
                                    };
                                    $statusLabel = match($booking->status) {
                                        'scheduled'  => 'Terjadwal',
                                        'completed'  => 'Selesai',
                                        'cancelled'  => 'Batal',
                                        'ongoing'    => 'Berlangsung',
                                        default      => $booking->status
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
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p>Belum ada booking</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- THERAPIST STATUS --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Status Terapis</span>
                <a href="{{ route('admin.therapists.index') }}" class="btn-outline" style="padding:6px 14px; font-size:12px;">Kelola</a>
            </div>
            <div class="card-body" style="padding:16px;">
                <div style="display:flex; flex-direction:column; gap:10px;">
                    @forelse($therapists as $terapis)
                    <div style="display:flex; align-items:center; gap:12px; padding:10px 12px; background:var(--dark-4); border-radius:10px;">
                        <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#2A2A2A,#3A3A3A);border:2px solid rgba(201,168,76,0.3);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:14px;color:var(--gold);flex-shrink:0;">
                            {{ strtoupper(substr($terapis->name, 0, 1)) }}
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:13px;font-weight:600;color:#F0EDE8;">{{ $terapis->name }}</div>
                            <div style="font-size:11px;color:var(--text-muted);">{{ $terapis->specialty ?? 'Terapis' }}</div>
                        </div>
                        <span style="font-size:11px;font-weight:600;color:{{ $terapis->is_active ? '#4CAF8A' : '#E05A5A' }};">
                            ● {{ $terapis->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    @empty
                    <div class="empty-state"><p>Belum ada terapis</p></div>
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
                <a href="{{ route('admin.payments.create') }}" class="btn-gold" style="font-size:12px; padding:7px 14px;">+ Catat Bayar</a>
            </div>
            <div class="card-body">
                @if($unpaidBookings->count())
                <table class="data-table">
                    <thead><tr><th>Pelanggan</th><th>Layanan</th><th>Total</th></tr></thead>
                    <tbody>
                        @foreach($unpaidBookings as $booking)
                        <tr>
                            <td class="text-main">{{ $booking->customer->name ?? '-' }}</td>
                            <td>{{ $booking->service->name ?? '-' }}</td>
                            <td style="color:var(--gold);font-weight:600;">Rp {{ number_format($booking->final_price, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                    <div class="empty-state"><p>Semua booking sudah dibayar ✓</p></div>
                @endif
            </div>
        </div>

        {{-- TOP SERVICES --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Layanan Terpopuler</span>
                <a href="{{ route('admin.services.index') }}" class="btn-outline" style="padding:6px 14px;font-size:12px;">Kelola</a>
            </div>
            <div class="card-body" style="padding:16px;">
                <div style="display:flex;flex-direction:column;gap:10px;">
                    @forelse($topServices as $svc)
                    <div style="display:flex;align-items:center;gap:12px;padding:10px 14px;background:var(--dark-4);border-radius:10px;">
                        <div style="width:32px;height:32px;background:rgba(201,168,76,0.1);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;">💆</div>
                        <div style="flex:1;">
                            <div style="font-size:13px;font-weight:600;color:#F0EDE8;">{{ $svc->name }}</div>
                            <div style="font-size:11px;color:var(--text-muted);">Rp {{ number_format($svc->price, 0, ',', '.') }}</div>
                        </div>
                        <span class="badge badge-gold">{{ $svc->bookings_count }} sesi</span>
                    </div>
                    @empty
                        <div class="empty-state"><p>Belum ada data layanan</p></div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

@endsection
