@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', \Carbon\Carbon::now()->translatedFormat('l\, d F Y'))

@section('topbar-actions')
    <a href="{{ route('admin.bookings.create') }}" class="btn-gold">+ Booking Baru</a>
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

        /* ─── Trend badge ─── */
        .trend-up {
            color: #4CAF8A;
            font-size: 11px;
            font-weight: 700;
        }

        .trend-down {
            color: #E05A5A;
            font-size: 11px;
            font-weight: 700;
        }

        /* ─── Stock bar ─── */
        .stock-bar-wrap {
            flex: 1;
        }

        .stock-bar-bg {
            height: 6px;
            border-radius: 3px;
            background: rgba(255, 255, 255, .06);
            margin-top: 5px;
            overflow: hidden;
        }

        .stock-bar-fill {
            height: 100%;
            border-radius: 3px;
            transition: width .4s;
        }

        .stock-critical {
            background: #E05A5A;
        }

        .stock-low {
            background: #E0A25A;
        }

        .stock-ok {
            background: #4CAF8A;
        }

        /* ─── Chart container ─── */
        .chart-container {
            position: relative;
            height: 220px;
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

        {{-- Pendapatan Bulan Ini --}}
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

        {{-- Total Terapis --}}
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

        {{-- Total Pelanggan --}}
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
    {{-- GRAFIK PENJUALAN — ala Majoo                                      --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="card" style="margin-bottom:22px;">
        <div class="card-header">
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <span class="card-title">📈 Grafik Pendapatan</span>
                {{-- Tanggal range --}}
                <span style="font-size:11px;color:var(--text-muted);" id="chartRangeLabel">
                    {{ now()->format('d M Y') }}
                </span>
            </div>
            {{-- Period tabs --}}
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
                <div style="font-size:11px;color:var(--text-muted);margin-top:2px;" id="summaryAcc">
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
    {{-- PRIORITAS TERAPIS HARI INI                                        --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="card" style="margin-bottom:22px;">
        <div class="card-header">
            <div style="display:flex;align-items:center;gap:10px;">
                <span class="card-title">🎯 Prioritas Terapis Hari Ini</span>
                <span
                    style="font-size:11px;color:var(--text-muted);background:rgba(201,168,76,.1);border:1px solid rgba(201,168,76,.2);padding:3px 10px;border-radius:100px;">
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
                            style="display:flex;align-items:center;gap:12px;padding:12px 14px;background:{{ $rankColor['bg'] }};border:1px solid {{ $rankColor['border'] }};border-radius:12px;position:relative;">
                            <div
                                style="position:absolute;top:-8px;left:10px;font-size:11px;font-weight:700;color:{{ $rankColor['label'] }};background:var(--dark-2);padding:1px 7px;border-radius:100px;border:1px solid {{ $rankColor['border'] }};">
                                {{ $rankIcon }} Prioritas {{ $i + 1 }}</div>
                            <div
                                style="width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,#2A2A2A,#3A3A3A);border:2px solid {{ $rankColor['badge'] }};display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:15px;color:{{ $rankColor['badge'] }};flex-shrink:0;">
                                {{ strtoupper(substr($terapis->name, 0, 1)) }}</div>
                            <div style="flex:1;min-width:0;">
                                <div
                                    style="font-size:13px;font-weight:600;color:#F0EDE8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ $terapis->name }}</div>
                                <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">
                                    {{ $terapis->specialty ?? 'Terapis' }}</div>
                                <div style="display:flex;gap:8px;margin-top:6px;flex-wrap:wrap;">
                                    <span
                                        style="font-size:10px;font-weight:600;padding:2px 8px;border-radius:100px;background:rgba(224,90,90,.12);color:#E05A5A;border:1px solid rgba(224,90,90,.2);">
                                        Kemarin: {{ $terapis->sesi_kemarin }} sesi</span>
                                    <span
                                        style="font-size:10px;font-weight:600;padding:2px 8px;border-radius:100px;background:rgba(76,175,138,.12);color:#4CAF8A;border:1px solid rgba(76,175,138,.2);">
                                        Hari ini: {{ $terapis->sesi_hari_ini }} sesi</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
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

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- ROW: RECENT BOOKINGS + THERAPIST STATUS                           --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div style="display:grid;grid-template-columns:1.5fr 1fr;gap:22px;margin-bottom:22px;">

        {{-- Recent Bookings --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Booking Terbaru</span>
                <a href="{{ route('admin.bookings.index') }}" class="btn-outline"
                    style="padding:6px 14px;font-size:12px;">Lihat Semua</a>
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
                                            $sc = match ($booking->status) {
                                                'scheduled' => 'badge-gold',
                                                'completed' => 'badge-green',
                                                'cancelled' => 'badge-red',
                                                'ongoing' => 'badge-blue',
                                                default => 'badge-gray',
                                            };
                                            $sl = match ($booking->status) {
                                                'scheduled' => 'Terjadwal',
                                                'completed' => 'Selesai',
                                                'cancelled' => 'Batal',
                                                'ongoing' => 'Berlangsung',
                                                default => $booking->status,
                                            };
                                        @endphp
                                        <span class="badge {{ $sc }}">{{ $sl }}</span>
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

        {{-- Therapist Status --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Status Terapis</span>
                <a href="{{ route('admin.therapists.index') }}" class="btn-outline"
                    style="padding:6px 14px;font-size:12px;">Kelola</a>
            </div>
            <div class="card-body" style="padding:16px;">
                <div style="display:flex;flex-direction:column;gap:10px;">
                    @forelse($therapists as $terapis)
                        <div
                            style="display:flex;align-items:center;gap:12px;padding:10px 12px;background:var(--dark-4);border-radius:10px;">
                            <div
                                style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#2A2A2A,#3A3A3A);border:2px solid rgba(201,168,76,0.3);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:14px;color:var(--gold);flex-shrink:0;">
                                {{ strtoupper(substr($terapis->name, 0, 1)) }}</div>
                            <div style="flex:1;">
                                <div style="font-size:13px;font-weight:600;color:#F0EDE8;">{{ $terapis->name }}</div>
                                <div style="font-size:11px;color:var(--text-muted);">
                                    {{ $terapis->specialty ?? 'Terapis' }}</div>
                            </div>
                            <span
                                style="font-size:11px;font-weight:600;color:{{ $terapis->is_active ? '#4CAF8A' : '#E05A5A' }};">
                                ● {{ $terapis->is_active ? 'Aktif' : 'Nonaktif' }}</span>
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

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- ROW: STOK TERENDAH + PENDING PAYMENTS + TOP SERVICES              --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:22px;margin-bottom:22px;">

        {{-- ── STOK TERENDAH ── --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">⚠️ Stok Terendah</span>
                <a href="{{ route('admin.barang.index') }}" class="btn-outline"
                    style="padding:6px 14px;font-size:12px;">Kelola Stok</a>
            </div>
            <div class="card-body" style="padding:16px;">
                @php
                    $lowStock = $lowStockProducts ?? collect();
                @endphp
                @if ($lowStock->count())
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        @foreach ($lowStock as $barang)
                            @php
                                // Gunakan accessor & field dari model Barang
                                $stokSistem = $barang->stok_sistem; // accessor: stok_awal + stok_masuk - stok_keluar
                                $stokMin = $barang->stok_minimum ?? 5;
                                $maxStock = max($stokMin * 5, 50);
                                $pct = $maxStock > 0 ? min(100, ($stokSistem / $maxStock) * 100) : 0;

                                $isCritical = $stokSistem <= 0 || $barang->kondisi_stok === 'habis';
                                $isLow = !$isCritical && $barang->kondisi_stok === 'hampir_habis';

                                $barClass = $isCritical ? 'stock-critical' : ($isLow ? 'stock-low' : 'stock-ok');
                                $badgeColor = $isCritical ? '#E05A5A' : ($isLow ? '#E0A25A' : '#4CAF8A');
                                $badgeBg = $isCritical
                                    ? 'rgba(224,90,90,.12)'
                                    : ($isLow
                                        ? 'rgba(224,162,90,.12)'
                                        : 'rgba(76,175,138,.12)');
                                $label = $isCritical ? 'Habis' : ($isLow ? 'Hampir Habis' : 'Aman');
                            @endphp
                            <div
                                style="padding:10px 12px;background:var(--dark-4);border-radius:10px;border:1px solid {{ $isCritical ? 'rgba(224,90,90,.2)' : 'rgba(255,255,255,.04)' }};">
                                <div
                                    style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                                    <div style="font-size:12px;font-weight:600;color:#F0EDE8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:130px;"
                                        title="{{ $barang->nama_barang }}">
                                        {{ $barang->nama_barang }}</div>
                                    <span
                                        style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:100px;color:{{ $badgeColor }};background:{{ $badgeBg }};border:1px solid {{ $badgeColor }}33;flex-shrink:0;margin-left:6px;">
                                        {{ $label }}</span>
                                </div>
                                {{-- Kode & kategori --}}
                                <div style="font-size:10px;color:var(--text-muted);margin-bottom:5px;">
                                    {{ $barang->kode_barang }}
                                    @if ($barang->kategori)
                                        ·
                                        {{ \App\Models\Barang::daftarKategori()[$barang->kategori] ?? $barang->kategori }}
                                    @endif
                                </div>
                                <div class="stock-bar-wrap">
                                    <div style="font-size:10px;color:var(--text-muted);margin-bottom:3px;">
                                        Stok: <strong style="color:{{ $badgeColor }};">{{ $stokSistem }}
                                            {{ $barang->satuan }}</strong>
                                        &nbsp;·&nbsp; Min: {{ $stokMin }} {{ $barang->satuan }}
                                    </div>
                                    <div class="stock-bar-bg">
                                        <div class="stock-bar-fill {{ $barClass }}"
                                            style="width:{{ $pct }}%;"></div>
                                    </div>
                                </div>
                                @if ($isCritical)
                                    <div
                                        style="margin-top:5px;font-size:10px;color:#E05A5A;display:flex;align-items:center;gap:4px;">
                                        ⚠ Stok habis — segera restock!
                                    </div>
                                @elseif($isLow)
                                    <div style="margin-top:5px;font-size:10px;color:#E0A25A;">
                                        ↓ Sisa stok di bawah minimum ({{ $stokMin }})
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <span style="font-size:2rem;">✅</span>
                        <p style="margin-top:8px;">Semua stok dalam kondisi aman</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ── BELUM DIBAYAR ── --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Belum Dibayar</span>
                <a href="{{ route('admin.payments.create') }}" class="btn-gold"
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

        {{-- ── LAYANAN TERPOPULER ── --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Layanan Terpopuler</span>
                <a href="{{ route('admin.services.index') }}" class="btn-outline"
                    style="padding:6px 14px;font-size:12px;">Kelola</a>
            </div>
            <div class="card-body" style="padding:16px;">
                <div style="display:flex;flex-direction:column;gap:10px;">
                    @forelse($topServices as $svc)
                        @php
                            $maxCount = $topServices->first()->bookings_count ?? 1;
                            $barPct = $maxCount > 0 ? ($svc->bookings_count / $maxCount) * 100 : 0;
                        @endphp
                        <div style="padding:10px 14px;background:var(--dark-4);border-radius:10px;">
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                                <div
                                    style="width:28px;height:28px;background:rgba(201,168,76,0.1);border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;">
                                    💆</div>
                                <div style="flex:1;min-width:0;">
                                    <div
                                        style="font-size:12px;font-weight:600;color:#F0EDE8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        {{ $svc->name }}</div>
                                    <div style="font-size:10px;color:var(--text-muted);">Rp
                                        {{ number_format($svc->price, 0, ',', '.') }}</div>
                                </div>
                                <span class="badge badge-gold" style="flex-shrink:0;">{{ $svc->bookings_count }}
                                    sesi</span>
                            </div>
                            {{-- Mini bar --}}
                            <div style="height:4px;background:rgba(255,255,255,.06);border-radius:2px;overflow:hidden;">
                                <div
                                    style="height:100%;width:{{ $barPct }}%;background:var(--gold);border-radius:2px;transition:width .4s;">
                                </div>
                            </div>
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



    {{-- ── CHART.JS ── --}}
    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        @php
            // Fallback semua variabel chart agar @json tidak melempar error
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
                totalLabel: 'Hari Ini',
            },
            mingguan: {
                labels: @json($chartLabelsMingguan),
                current: @json($chartMingguan),
                previous: @json($chartMingguanPrev),
                rangeLabel: '{{ now()->startOfWeek()->format('d M') }} - {{ now()->endOfWeek()->format('d M Y') }}',
                totalLabel: 'Minggu Ini',
            },
            bulanan: {
                labels: @json($chartLabelsBulanan),
                current: @json($chartBulanan),
                previous: @json($chartBulananPrev),
                rangeLabel: '{{ now()->format('Y') }}',
                totalLabel: 'Tahun Ini',
            },
        };

        // ─── Inisialisasi Chart ────────────────────────────────────────────────────────
        const ctx = document.getElementById('revenueChart').getContext('2d');

        // Gradient emas untuk fill
        function makeGradient(ctx, color) {
            const grad = ctx.createLinearGradient(0, 0, 0, 220);
            grad.addColorStop(0, color.replace(')', ', 0.25)').replace('rgb', 'rgba'));
            grad.addColorStop(1, color.replace(')', ', 0)').replace('rgb', 'rgba'));
            return grad;
        }

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
                    },
                    {
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
                    },
                ]
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
                            label: ctx => {
                                const val = ctx.parsed.y;
                                return ' Rp ' + new Intl.NumberFormat('id-ID').format(val);
                            }
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
                            callback: v => v >= 1000000 ?
                                (v / 1000000).toFixed(1) + 'jt' :
                                v >= 1000 ?
                                (v / 1000).toFixed(0) + 'rb' :
                                v,
                        },
                        beginAtZero: true,
                    }
                }
            }
        });

        // ─── Switch period ─────────────────────────────────────────────────────────────
        function switchPeriod(period, btn) {
            // Update tab active state
            document.querySelectorAll('.chart-tab').forEach(t => t.classList.remove('active'));
            btn.classList.add('active');

            const d = chartData[period];

            // Update chart
            revenueChart.data.labels = d.labels;
            revenueChart.data.datasets[0].data = d.current;
            revenueChart.data.datasets[1].data = d.previous;
            revenueChart.update('active');

            // Update range label
            document.getElementById('chartRangeLabel').textContent = d.rangeLabel;

            // Recalculate summary
            const total = d.current.reduce((a, b) => a + b, 0);
            const trx = d.current.filter(v => v > 0).length;
            document.getElementById('summaryTotal').textContent =
                'Rp ' + new Intl.NumberFormat('id-ID').format(total);
            document.getElementById('summaryTrx').textContent = trx;
            document.getElementById('summaryAvg').textContent =
                'Rp ' + new Intl.NumberFormat('id-ID').format(trx > 0 ? Math.round(total / trx) : 0);
        }
    </script>

@endsection
