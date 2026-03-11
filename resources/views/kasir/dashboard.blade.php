@extends('layouts.admin')

@section('title', 'Dashboard Kasir')
@section('page-title', 'Dashboard Kasir')
@section('page-subtitle', \Carbon\Carbon::now()->translatedFormat('l\, d F Y'))

@section('topbar-actions')
    <a href="{{ route('kasir.payments.create') }}" class="btn-gold">+ Catat Pembayaran</a>
@endsection

@section('content')

    <style>
        /* ─── Chart Period Tabs ─── */
        .chart-tabs {
            display: flex;
            gap: 4px;
            background: var(--dark-4);
            border-radius: 8px;
            padding: 3px;
        }

        .chart-tab {
            padding: 5px 14px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            color: var(--text-muted);
            background: transparent;
            border: none;
            transition: all .2s;
            font-family: inherit;
        }

        .chart-tab.active {
            background: var(--gold);
            color: #1A1A1A;
        }

        /* ─── Stat Cards ─── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 22px;
        }

        @media (max-width: 1100px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* ─── Chart container ─── */
        .chart-container {
            position: relative;
            height: 220px;
        }

        /* ─── Status Badge Inline ─── */
        .status-dot {
            display: inline-block;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            margin-right: 5px;
        }

        /* ─── Payment Method Pills ─── */
        .method-pill {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            background: var(--dark-4);
            border-radius: 10px;
            margin-bottom: 8px;
        }

        .method-pill:last-child {
            margin-bottom: 0;
        }

        /* ─── Timeline Booking ─── */
        .timeline-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, .04);
            position: relative;
        }

        .timeline-item:last-child {
            border-bottom: none;
        }

        .timeline-time {
            font-size: 11px;
            font-weight: 700;
            color: var(--gold);
            min-width: 42px;
            padding-top: 2px;
        }

        .timeline-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-top: 5px;
            flex-shrink: 0;
        }

        .timeline-content {
            flex: 1;
        }

        .timeline-name {
            font-size: 13px;
            font-weight: 600;
            color: #F0EDE8;
        }

        .timeline-sub {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .timeline-price {
            font-size: 12px;
            font-weight: 700;
            color: var(--gold);
            flex-shrink: 0;
        }
    </style>

    {{-- ── STATS ── --}}
    <div class="stats-grid">

        {{-- Booking Hari Ini --}}
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

        {{-- Pendapatan Hari Ini --}}
        <div class="stat-card">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="stat-value">Rp {{ number_format($todayRevenue / 1000, 0) }}rb</div>
            <div class="stat-label">Pendapatan Hari Ini</div>
        </div>

        {{-- Pendapatan Bulan Ini --}}
        <div class="stat-card">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <div class="stat-value">{{ number_format($monthRevenue / 1000000, 1) }}jt</div>
            <div class="stat-label">Pendapatan Bulan Ini</div>
        </div>

        {{-- Belum Dibayar --}}
        <div class="stat-card" style="border-color: rgba(224,90,90,.2);">
            <div class="stat-icon" style="color: #E05A5A;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="stat-value" style="color: #E05A5A;">{{ $pendingCount }}</div>
            <div class="stat-label">Belum Dibayar</div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- GRAFIK PENDAPATAN                                                  --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="card" style="margin-bottom:22px;">
        <div class="card-header">
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <span class="card-title">📈 Grafik Pendapatan</span>
                <span style="font-size:11px;color:var(--text-muted);" id="chartRangeLabel">
                    {{ now()->format('d M Y') }}
                </span>
            </div>
            <div class="chart-tabs">
                <button class="chart-tab active" onclick="switchPeriod('harian',this)">Harian</button>
                <button class="chart-tab" onclick="switchPeriod('mingguan',this)">Mingguan</button>
                <button class="chart-tab" onclick="switchPeriod('bulanan',this)">Bulanan</button>
            </div>
        </div>

        {{-- Summary row --}}
        <div
            style="display:grid;grid-template-columns:repeat(4,1fr);gap:0;border-bottom:1px solid rgba(255,255,255,.06);padding:16px 20px;">
            <div>
                <div style="font-size:11px;color:var(--text-muted);margin-bottom:4px;">Total Pendapatan</div>
                <div style="font-size:20px;font-weight:700;color:#F0EDE8;" id="summaryTotal">
                    Rp {{ number_format($todayRevenue ?? 0, 0, ',', '.') }}
                </div>
                <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">
                    Akumulasi bulan ini: Rp {{ number_format($monthRevenue, 0, ',', '.') }}
                </div>
            </div>
            <div>
                <div style="font-size:11px;color:var(--text-muted);margin-bottom:4px;">Belum Dibayar</div>
                <div style="font-size:20px;font-weight:700;color:#E05A5A;">
                    Rp {{ number_format($unpaidBookings->sum('final_price') ?? 0, 0, ',', '.') }}
                </div>
            </div>
            <div>
                <div style="font-size:11px;color:var(--text-muted);margin-bottom:4px;">Transaksi</div>
                <div style="font-size:20px;font-weight:700;color:#F0EDE8;" id="summaryTrx">
                    {{ $todayBookings }}
                </div>
            </div>
            <div>
                <div style="font-size:11px;color:var(--text-muted);margin-bottom:4px;">Avg per Transaksi</div>
                <div style="font-size:20px;font-weight:700;color:#F0EDE8;" id="summaryAvg">
                    @php $avg = $todayBookings > 0 ? ($todayRevenue ?? 0) / $todayBookings : 0; @endphp
                    Rp {{ number_format($avg, 0, ',', '.') }}
                </div>
            </div>
        </div>

        {{-- Legend --}}
        <div style="display:flex;gap:20px;padding:12px 20px 0;font-size:11px;color:var(--text-muted);">
            <span style="display:flex;align-items:center;gap:6px;">
                <span style="width:24px;height:2px;background:#C9A84C;display:inline-block;border-radius:2px;"></span>
                Periode Ini
            </span>
            <span style="display:flex;align-items:center;gap:6px;">
                <span
                    style="width:24px;height:2px;background:rgba(255,255,255,.2);display:inline-block;border-radius:2px;"></span>
                Periode Sebelumnya
            </span>
        </div>

        <div class="card-body" style="padding:12px 20px 20px;">
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- ROW: TIMELINE BOOKING HARI INI + RINGKASAN STATUS                 --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div style="display:grid;grid-template-columns:1.6fr 1fr;gap:22px;margin-bottom:22px;">

        {{-- Timeline Booking Hari Ini --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">🗓️ Jadwal Hari Ini</span>
                <a href="{{ route('kasir.bookings.index') }}" class="btn-outline"
                    style="padding:6px 14px;font-size:12px;">Lihat Semua</a>
            </div>
            <div class="card-body" style="padding:16px;max-height:380px;overflow-y:auto;">
                @forelse($todayBookingList as $booking)
                    @php
                        $dotColor = match ($booking->status) {
                            'scheduled' => '#C9A84C',
                            'ongoing' => '#4CA8FF',
                            'completed' => '#4CAF8A',
                            'cancelled' => '#E05A5A',
                            default => '#888',
                        };
                        $sc = match ($booking->status) {
                            'scheduled' => 'badge-gold',
                            'ongoing' => 'badge-blue',
                            'completed' => 'badge-green',
                            'cancelled' => 'badge-red',
                            default => 'badge-gray',
                        };
                        $sl = match ($booking->status) {
                            'scheduled' => 'Terjadwal',
                            'ongoing' => 'Berlangsung',
                            'completed' => 'Selesai',
                            'cancelled' => 'Batal',
                            default => $booking->status,
                        };
                        $isPaid = $booking->payment !== null;
                    @endphp
                    <div class="timeline-item">
                        <div class="timeline-time">
                            {{ \Carbon\Carbon::parse($booking->scheduled_at)->format('H:i') }}
                        </div>
                        <div class="timeline-dot" style="background:{{ $dotColor }};"></div>
                        <div class="timeline-content">
                            <div class="timeline-name">{{ $booking->customer->name ?? '-' }}</div>
                            <div class="timeline-sub">
                                {{ $booking->service->name ?? '-' }}
                                @if ($booking->therapist)
                                    · <span style="color:var(--gold);">{{ $booking->therapist->name }}</span>
                                @endif
                            </div>
                            <div style="display:flex;align-items:center;gap:8px;margin-top:5px;">
                                <span class="badge {{ $sc }}">{{ $sl }}</span>
                                @if ($booking->status !== 'cancelled')
                                    @if ($isPaid)
                                        <span class="badge badge-green" style="font-size:10px;">✓ Lunas</span>
                                    @else
                                        <span class="badge badge-red" style="font-size:10px;">⚠ Belum Bayar</span>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="timeline-price">
                            Rp {{ number_format($booking->final_price, 0, ',', '.') }}
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p>Belum ada booking hari ini</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Ringkasan Status + Metode Pembayaran --}}
        <div style="display:flex;flex-direction:column;gap:16px;">

            {{-- Ringkasan Status --}}
            <div class="card" style="flex:1;">
                <div class="card-header">
                    <span class="card-title">📊 Status Booking</span>
                </div>
                <div class="card-body" style="padding:16px;">
                    @php
                        $statuses = [
                            'scheduled' => [
                                'label' => 'Terjadwal',
                                'color' => '#C9A84C',
                                'bg' => 'rgba(201,168,76,.12)',
                            ],
                            'ongoing' => [
                                'label' => 'Berlangsung',
                                'color' => '#4CA8FF',
                                'bg' => 'rgba(76,168,255,.12)',
                            ],
                            'completed' => ['label' => 'Selesai', 'color' => '#4CAF8A', 'bg' => 'rgba(76,175,138,.12)'],
                            'cancelled' => ['label' => 'Batal', 'color' => '#E05A5A', 'bg' => 'rgba(224,90,90,.12)'],
                        ];
                        $totalToday = array_sum($statuses ? $statusSummary->toArray() : []) ?: 1;
                    @endphp
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        @foreach ($statuses as $key => $meta)
                            @php $count = $statusSummary[$key] ?? 0; @endphp
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div
                                    style="width:10px;height:10px;border-radius:50%;background:{{ $meta['color'] }};flex-shrink:0;">
                                </div>
                                <div style="flex:1;font-size:12px;color:var(--text-muted);">{{ $meta['label'] }}</div>
                                <div style="font-size:13px;font-weight:700;color:#F0EDE8;">{{ $count }}</div>
                            </div>
                            <div
                                style="height:4px;background:rgba(255,255,255,.06);border-radius:2px;overflow:hidden;margin-bottom:2px;">
                                @php $pct = $todayBookings > 0 ? ($count / $todayBookings) * 100 : 0; @endphp
                                <div
                                    style="height:100%;width:{{ $pct }}%;background:{{ $meta['color'] }};border-radius:2px;transition:width .4s;">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Metode Pembayaran Hari Ini --}}
            <div class="card" style="flex:1;">
                <div class="card-header">
                    <span class="card-title">💳 Metode Bayar</span>
                </div>
                <div class="card-body" style="padding:16px;">
                    @forelse($paymentMethods as $method)
                        @php
                            $methodIcon = match (strtolower($method->payment_method ?? '')) {
                                'cash', 'tunai' => '💵',
                                'transfer', 'bank transfer' => '🏦',
                                'qris', 'qr' => '📱',
                                'debit', 'kartu' => '💳',
                                default => '💰',
                            };
                        @endphp
                        <div class="method-pill">
                            <div style="display:flex;align-items:center;gap:8px;">
                                <span style="font-size:16px;">{{ $methodIcon }}</span>
                                <div>
                                    <div style="font-size:12px;font-weight:600;color:#F0EDE8;text-transform:capitalize;">
                                        {{ $method->payment_method ?? 'Lainnya' }}
                                    </div>
                                    <div style="font-size:10px;color:var(--text-muted);">{{ $method->total }} transaksi
                                    </div>
                                </div>
                            </div>
                            <div style="font-size:12px;font-weight:700;color:var(--gold);">
                                Rp {{ number_format($method->jumlah, 0, ',', '.') }}
                            </div>
                        </div>
                    @empty
                        <div class="empty-state" style="padding:20px 0;">
                            <p>Belum ada pembayaran hari ini</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- ROW: BELUM DIBAYAR + PEMBAYARAN TERBARU + TERAPIS HARI INI        --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:22px;margin-bottom:22px;">

        {{-- Belum Dibayar --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">⚠️ Belum Dibayar</span>
                <a href="{{ route('kasir.payments.create') }}" class="btn-gold"
                    style="font-size:12px;padding:7px 14px;">+ Catat Bayar</a>
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
                                    <td style="color:var(--gold);font-weight:600;">
                                        Rp {{ number_format($booking->final_price, 0, ',', '.') }}
                                    </td>
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

        {{-- Pembayaran Terbaru --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">🧾 Pembayaran Terbaru</span>
                <a href="{{ route('kasir.payments.index') }}" class="btn-outline"
                    style="padding:6px 14px;font-size:12px;">Lihat Semua</a>
            </div>
            <div class="card-body">
                @if ($recentPayments->count())
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Pelanggan</th>
                                <th>Metode</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentPayments as $payment)
                                <tr>
                                    <td class="text-main">{{ $payment->booking->customer->name ?? '-' }}</td>
                                    <td>
                                        <span style="font-size:11px;text-transform:capitalize;color:var(--text-muted);">
                                            {{ $payment->payment_method ?? '-' }}
                                        </span>
                                    </td>
                                    <td style="color:#4CAF8A;font-weight:600;">
                                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <p>Belum ada pembayaran tercatat</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Terapis & Sesi Hari Ini --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">👥 Terapis Hari Ini</span>
            </div>
            <div class="card-body" style="padding:16px;">
                <div style="display:flex;flex-direction:column;gap:8px;">
                    @forelse($therapistsToday as $terapis)
                        <div
                            style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:var(--dark-4);border-radius:10px;">
                            <div
                                style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#2A2A2A,#3A3A3A);border:2px solid rgba(201,168,76,0.3);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:13px;color:var(--gold);flex-shrink:0;">
                                {{ strtoupper(substr($terapis->name, 0, 1)) }}
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div
                                    style="font-size:12px;font-weight:600;color:#F0EDE8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ $terapis->name }}
                                </div>
                                <div style="font-size:10px;color:var(--text-muted);">
                                    {{ $terapis->specialty ?? 'Terapis' }}</div>
                            </div>
                            <div style="text-align:right;flex-shrink:0;">
                                <div style="font-size:14px;font-weight:700;color:var(--gold);">
                                    {{ $terapis->sesi_hari_ini }}</div>
                                <div style="font-size:10px;color:var(--text-muted);">sesi</div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <p>Belum ada terapis aktif</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- LAYANAN TERPOPULER HARI INI                                        --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if ($topServicesToday->count())
        <div class="card" style="margin-bottom:22px;">
            <div class="card-header">
                <span class="card-title">💆 Layanan Terpopuler Hari Ini</span>
            </div>
            <div class="card-body" style="padding:16px;">
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;">
                    @foreach ($topServicesToday as $i => $svc)
                        @php
                            $maxCount = $topServicesToday->first()->bookings_count ?: 1;
                            $barPct = ($svc->bookings_count / $maxCount) * 100;
                        @endphp
                        <div
                            style="padding:12px 14px;background:var(--dark-4);border-radius:10px;border:1px solid rgba(255,255,255,.04);">
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                                <div
                                    style="width:26px;height:26px;background:rgba(201,168,76,0.1);border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;">
                                    💆</div>
                                <div style="flex:1;min-width:0;">
                                    <div
                                        style="font-size:12px;font-weight:600;color:#F0EDE8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        {{ $svc->name }}
                                    </div>
                                    <div style="font-size:10px;color:var(--text-muted);">Rp
                                        {{ number_format($svc->price, 0, ',', '.') }}</div>
                                </div>
                                <span class="badge badge-gold" style="flex-shrink:0;">{{ $svc->bookings_count }}
                                    sesi</span>
                            </div>
                            <div style="height:4px;background:rgba(255,255,255,.06);border-radius:2px;overflow:hidden;">
                                <div
                                    style="height:100%;width:{{ $barPct }}%;background:var(--gold);border-radius:2px;transition:width .4s;">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- ── CHART.JS ── --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        @php
            $chartLabelsHarian = $chartLabelsHarian ?? array_map(fn($h) => sprintf('%02d:00', $h), range(0, 23));
            $chartHarian = $chartHarian ?? array_fill(0, 24, 0);
            $chartHarianPrev = $chartHarianPrev ?? array_fill(0, 24, 0);
            $chartLabelsMingguan = $chartLabelsMingguan ?? ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
            $chartMingguan = $chartMingguan ?? array_fill(0, 7, 0);
            $chartMingguanPrev = $chartMingguanPrev ?? array_fill(0, 7, 0);
            $chartLabelsBulanan = $chartLabelsBulanan ?? ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            $chartBulanan = $chartBulanan ?? array_fill(0, 12, 0);
            $chartBulananPrev = $chartBulananPrev ?? array_fill(0, 12, 0);
        @endphp
        const chartData = {
            harian: {
                labels: @json($chartLabelsHarian),
                current: @json($chartHarian),
                previous: @json($chartHarianPrev),
                rangeLabel: '{{ now()->format('d M Y') }}',
            },
            mingguan: {
                labels: @json($chartLabelsMingguan),
                current: @json($chartMingguan),
                previous: @json($chartMingguanPrev),
                rangeLabel: '{{ now()->startOfWeek()->format('d M') }} - {{ now()->endOfWeek()->format('d M Y') }}',
            },
            bulanan: {
                labels: @json($chartLabelsBulanan),
                current: @json($chartBulanan),
                previous: @json($chartBulananPrev),
                rangeLabel: '{{ now()->format('Y') }}',
            },
        };

        const ctx = document.getElementById('revenueChart').getContext('2d');

        const goldGrad = (() => {
            const g = ctx.createLinearGradient(0, 0, 0, 220);
            g.addColorStop(0, 'rgba(201,168,76,0.3)');
            g.addColorStop(1, 'rgba(201,168,76,0)');
            return g;
        })();

        let revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.harian.labels,
                datasets: [{
                    label: 'Periode Ini',
                    data: chartData.harian.current,
                    borderColor: '#C9A84C',
                    borderWidth: 2,
                    backgroundColor: goldGrad,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#C9A84C',
                    fill: true,
                    tension: 0.4,
                }, {
                    label: 'Periode Sebelumnya',
                    data: chartData.harian.previous,
                    borderColor: 'rgba(255,255,255,0.2)',
                    borderWidth: 1.5,
                    backgroundColor: 'transparent',
                    pointRadius: 0,
                    pointHoverRadius: 4,
                    fill: false,
                    tension: 0.4,
                    borderDash: [4, 4],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(26,26,26,0.95)',
                        titleColor: '#C9A84C',
                        bodyColor: '#F0EDE8',
                        borderColor: 'rgba(201,168,76,0.2)',
                        borderWidth: 1,
                        padding: 12,
                        callbacks: {
                            label: ctx => ' Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y)
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(255,255,255,0.04)'
                        },
                        ticks: {
                            color: 'rgba(255,255,255,0.35)',
                            font: {
                                size: 10
                            }
                        },
                    },
                    y: {
                        grid: {
                            color: 'rgba(255,255,255,0.04)'
                        },
                        ticks: {
                            color: 'rgba(255,255,255,0.35)',
                            font: {
                                size: 10
                            },
                            callback: v => v >= 1000000 ? (v / 1000000).toFixed(1) + 'jt' : v >= 1000 ? (v / 1000)
                                .toFixed(0) + 'rb' : v,
                        },
                        beginAtZero: true,
                    }
                }
            }
        });

        function switchPeriod(period, btn) {
            document.querySelectorAll('.chart-tab').forEach(t => t.classList.remove('active'));
            btn.classList.add('active');

            const d = chartData[period];
            revenueChart.data.labels = d.labels;
            revenueChart.data.datasets[0].data = d.current;
            revenueChart.data.datasets[1].data = d.previous;
            revenueChart.update('active');

            document.getElementById('chartRangeLabel').textContent = d.rangeLabel;

            const total = d.current.reduce((a, b) => a + b, 0);
            const trx = d.current.filter(v => v > 0).length;
            document.getElementById('summaryTotal').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
            document.getElementById('summaryTrx').textContent = trx;
            document.getElementById('summaryAvg').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(trx > 0 ? Math
                .round(total / trx) : 0);
        }
    </script>

@endsection
