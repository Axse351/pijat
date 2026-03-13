<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Laporan
        </h2>
    </x-slot>

    <style>
        .stat-card {
            position: relative;
            overflow: hidden;
            transition: transform .2s, box-shadow .2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, .12);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }

        .stat-card.emerald::before {
            background: linear-gradient(90deg, #10B981, #6EE7B7);
        }

        .stat-card.blue::before {
            background: linear-gradient(90deg, #3B82F6, #93C5FD);
        }

        .stat-card.amber::before {
            background: linear-gradient(90deg, #F59E0B, #FCD34D);
        }

        .stat-card.rose::before {
            background: linear-gradient(90deg, #F43F5E, #FDA4AF);
        }

        .stat-card.violet::before {
            background: linear-gradient(90deg, #8B5CF6, #C4B5FD);
        }

        .stat-card.cyan::before {
            background: linear-gradient(90deg, #06B6D4, #67E8F9);
        }

        .section-title {
            font-size: 15px;
            font-weight: 700;
            color: #374151;
        }

        .dark .section-title {
            color: #E5E7EB;
        }

        .tab-btn {
            padding: 8px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s;
            border: none;
            background: transparent;
            color: #6B7280;
        }

        .tab-btn.active {
            background: #4F46E5;
            color: white;
            box-shadow: 0 2px 8px rgba(79, 70, 229, .3);
        }

        .tab-btn:hover:not(.active) {
            background: #F3F4F6;
            color: #374151;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .progress-bar {
            height: 6px;
            border-radius: 3px;
            background: #E5E7EB;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 3px;
            background: linear-gradient(90deg, #4F46E5, #818CF8);
            transition: width .6s ease;
        }

        /* Widget toggle chips */
        .widget-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all .15s;
            user-select: none;
        }

        .widget-chip.chip-on {
            opacity: 1;
        }

        .widget-chip.chip-off {
            opacity: .35;
            filter: grayscale(1);
        }

        .section-block {
            transition: opacity .2s;
        }

        .section-block.sec-hidden {
            display: none;
        }
    </style>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ===== FILTER BAR ===== --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4">
                <form method="GET" action="{{ route('admin.laporan.index') }}" id="filterForm">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="text-sm font-semibold text-gray-500 me-2">Periode:</span>
                        <button type="button" onclick="setMode('harian')"
                            class="tab-btn {{ $mode === 'harian' ? 'active' : '' }}" id="tab-harian">📅 Harian</button>
                        <button type="button" onclick="setMode('mingguan')"
                            class="tab-btn {{ $mode === 'mingguan' ? 'active' : '' }}" id="tab-mingguan">📆 Mingguan</button>
                        <button type="button" onclick="setMode('bulanan')"
                            class="tab-btn {{ $mode === 'bulanan' ? 'active' : '' }}" id="tab-bulanan">🗓️ Bulanan</button>
                    </div>
                    <input type="hidden" name="mode" id="modeInput" value="{{ $mode }}">
                    <div class="flex flex-wrap items-end gap-3">
                        <div id="input-harian" class="{{ $mode !== 'harian' ? 'hidden' : '' }}">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal</label>
                            <input type="date" name="tanggal" value="{{ $tanggal }}"
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-white"
                                onchange="this.form.submit()">
                        </div>
                        <div id="input-mingguan" class="{{ $mode !== 'mingguan' ? 'hidden' : '' }}">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Minggu</label>
                            <input type="week" name="minggu" value="{{ $minggu }}"
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-white"
                                onchange="this.form.submit()">
                        </div>
                        <div id="input-bulanan" class="{{ $mode !== 'bulanan' ? 'hidden' : '' }}">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Bulan</label>
                            <input type="month" name="bulan" value="{{ $bulan }}"
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-white"
                                onchange="this.form.submit()">
                        </div>
                        <div class="ms-auto text-right">
                            <div class="text-xs text-gray-400">Menampilkan data</div>
                            <div class="text-sm font-bold text-indigo-600 dark:text-indigo-400">{{ $label }}
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- ===== CUSTOMIZE TAMPILAN ===== --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-semibold text-gray-400 uppercase me-1">Tampilkan:</span>

                    <span class="widget-chip chip-on" style="background:#EEF2FF;color:#4F46E5;border-color:#4F46E5"
                        onclick="toggleSec('sec-keuangan',this)" data-sec="sec-keuangan">💰 Keuangan</span>

                    <span class="widget-chip chip-on" style="background:#F0FDF4;color:#10B981;border-color:#10B981"
                        onclick="toggleSec('sec-pengunjung',this)" data-sec="sec-pengunjung">👥 Pengunjung</span>

                    <span class="widget-chip chip-on" style="background:#FFF7ED;color:#F59E0B;border-color:#F59E0B"
                        onclick="toggleSec('sec-grafik',this)" data-sec="sec-grafik">📈 Grafik</span>

                    <span class="widget-chip chip-on" style="background:#FDF2F8;color:#EC4899;border-color:#EC4899"
                        onclick="toggleSec('sec-layanan',this)" data-sec="sec-layanan">💆 Layanan</span>

                    <span class="widget-chip chip-on" style="background:#F0F9FF;color:#0EA5E9;border-color:#0EA5E9"
                        onclick="toggleSec('sec-terapis',this)" data-sec="sec-terapis">🧑‍⚕️ Terapis</span>

                    <span class="widget-chip chip-on" style="background:#F5F3FF;color:#8B5CF6;border-color:#8B5CF6"
                        onclick="toggleSec('sec-transaksi',this)" data-sec="sec-transaksi">🧾 Transaksi</span>

                    <button onclick="resetSecs()"
                        class="ms-auto text-xs text-gray-400 hover:text-indigo-500 font-medium transition-colors">
                        ↺ Reset tampilan
                    </button>
                </div>
            </div>

            {{-- ===== SEC: KEUANGAN ===== --}}
            <div class="section-block" id="sec-keuangan">
                <h3 class="section-title mb-3">💰 Laporan Keuangan</h3>
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                    <div
                        class="stat-card emerald bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                        <div class="text-xs text-gray-400 mb-1">Total Pendapatan (Nett)</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">Rp
                            {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                        <div class="text-xs text-emerald-500 mt-1">Setelah diskon</div>
                    </div>
                    <div
                        class="stat-card blue bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                        <div class="text-xs text-gray-400 mb-1">Pendapatan Bruto</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">Rp
                            {{ number_format($totalBruto, 0, ',', '.') }}</div>
                        <div class="text-xs text-blue-500 mt-1">Sebelum diskon</div>
                    </div>
                    <div
                        class="stat-card rose bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                        <div class="text-xs text-gray-400 mb-1">Total Diskon</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">Rp
                            {{ number_format($totalDiskon, 0, ',', '.') }}</div>
                        <div class="text-xs text-rose-500 mt-1">Potongan diberikan</div>
                    </div>
                    <div
                        class="stat-card cyan bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                        <div class="text-xs text-gray-400 mb-1">Pendapatan QRIS</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">Rp
                            {{ number_format($pendapatanQris, 0, ',', '.') }}</div>
                        <div class="text-xs text-cyan-500 mt-1">Via QRIS</div>
                    </div>
                    <div
                        class="stat-card amber bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                        <div class="text-xs text-gray-400 mb-1">Pendapatan Cash</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">Rp
                            {{ number_format($pendapatanCash, 0, ',', '.') }}</div>
                        <div class="text-xs text-amber-500 mt-1">Via Tunai</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                        <div class="text-xs text-gray-400 mb-2">Komposisi Metode</div>
                        @php
                            $totalPay = $pendapatanQris + $pendapatanCash;
                            $qrisPct = $totalPay > 0 ? round(($pendapatanQris / $totalPay) * 100) : 0;
                            $cashPct = 100 - $qrisPct;
                        @endphp
                        <div class="mb-2">
                            <div class="flex justify-between text-xs mb-1"><span
                                    class="text-cyan-600 font-semibold">QRIS</span><span>{{ $qrisPct }}%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill"
                                    style="width:{{ $qrisPct }}%;background:linear-gradient(90deg,#06B6D4,#67E8F9)">
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs mb-1"><span
                                    class="text-amber-600 font-semibold">Cash</span><span>{{ $cashPct }}%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill"
                                    style="width:{{ $cashPct }}%;background:linear-gradient(90deg,#F59E0B,#FCD34D)">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== SEC: PENGUNJUNG ===== --}}
            <div class="section-block" id="sec-pengunjung">
                <h3 class="section-title mb-3">👥 Laporan Pengunjung / Booking</h3>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div
                        class="stat-card violet bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                        <div class="text-xs text-gray-400 mb-1">Total Booking</div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalBooking }}</div>
                        <div class="text-xs text-violet-500 mt-1">Semua status</div>
                    </div>
                    <div
                        class="stat-card emerald bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                        <div class="text-xs text-gray-400 mb-1">Selesai</div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $bookingSelesai }}</div>
                        @if ($totalBooking > 0)
                            <div class="text-xs text-emerald-500 mt-1">
                                {{ round(($bookingSelesai / $totalBooking) * 100) }}% dari total</div>
                        @endif
                    </div>
                    <div
                        class="stat-card amber bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                        <div class="text-xs text-gray-400 mb-1">Pending / Terjadwal</div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $bookingPending }}</div>
                        <div class="text-xs text-amber-500 mt-1">Menunggu</div>
                    </div>
                    <div
                        class="stat-card rose bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                        <div class="text-xs text-gray-400 mb-1">Dibatalkan</div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $bookingBatal }}</div>
                        @if ($totalBooking > 0)
                            <div class="text-xs text-rose-500 mt-1">{{ round(($bookingBatal / $totalBooking) * 100) }}%
                                cancel rate</div>
                        @endif
                    </div>
                </div>
                <div class="mt-4 bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                    <div class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-3">Sumber Booking</div>
                    <div class="flex flex-wrap gap-4">
                        @foreach (['wa' => ['🟢', 'WhatsApp'], 'walkin' => ['🚶', 'Walk-in'], 'web' => ['🌐', 'Website']] as $src => [$icon, $name])
                            <div class="flex items-center gap-2 px-4 py-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <span class="text-lg">{{ $icon }}</span>
                                <div>
                                    <div class="text-xs text-gray-400">{{ $name }}</div>
                                    <div class="text-lg font-bold text-gray-800 dark:text-white">
                                        {{ $sumberBooking[$src] ?? 0 }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ===== SEC: GRAFIK ===== --}}
            <div class="section-block" id="sec-grafik">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-5">
                        <div class="section-title mb-4">📈 Grafik Pendapatan</div>
                        <canvas id="chartPendapatan" height="200"></canvas>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-5">
                        <div class="section-title mb-4">📊 Grafik Booking</div>
                        <canvas id="chartBooking" height="200"></canvas>
                    </div>
                </div>
            </div>

            {{-- ===== SEC: LAYANAN ===== --}}
            <div class="section-block" id="sec-layanan">
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                        <span class="section-title">💆 Laporan Layanan Terpopuler</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#
                                    </th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                        Layanan</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                                        Total Sesi</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                                        Pendapatan</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                        Proporsi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                @forelse($topLayanan as $i => $svc)
                                    @php $pct = $totalBooking > 0 ? round(($svc->total_sesi / $totalBooking) * 100) : 0; @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-5 py-3 text-gray-400 font-mono">{{ $i + 1 }}</td>
                                        <td class="px-5 py-3 font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $svc->name }}</td>
                                        <td class="px-5 py-3 text-right font-bold text-indigo-600">
                                            {{ $svc->total_sesi }}</td>
                                        <td class="px-5 py-3 text-right text-gray-700 dark:text-gray-300">Rp
                                            {{ number_format($svc->total_pendapatan ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-5 py-3 min-w-[120px]">
                                            <div class="flex items-center gap-2">
                                                <div class="progress-bar flex-1">
                                                    <div class="progress-fill" style="width:{{ $pct }}%">
                                                    </div>
                                                </div>
                                                <span class="text-xs text-gray-400 w-8">{{ $pct }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-10 text-gray-400">Belum ada data.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ===== SEC: TERAPIS ===== --}}
            <div class="section-block" id="sec-terapis">
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                        <span class="section-title">🧑‍⚕️ Laporan Kinerja Terapis</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                        Terapis</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                                        Total Sesi</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Sesi
                                        Selesai</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                                        Pendapatan</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                                        Completion Rate</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                @forelse($laporanTerapis as $terapis)
                                    @php $rate = $terapis->total_sesi > 0 ? round(($terapis->sesi_selesai/$terapis->total_sesi)*100) : 0; @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-5 py-3">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white font-bold text-sm">
                                                    {{ strtoupper(substr($terapis->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-gray-800 dark:text-gray-200">
                                                        {{ $terapis->name }}</div>
                                                    <div class="text-xs text-gray-400">
                                                        {{ $terapis->specialty ?? 'Terapis' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3 text-right font-bold text-gray-700 dark:text-gray-300">
                                            {{ $terapis->total_sesi }}</td>
                                        <td class="px-5 py-3 text-right text-emerald-600 font-semibold">
                                            {{ $terapis->sesi_selesai }}</td>
                                        <td class="px-5 py-3 text-right text-gray-700 dark:text-gray-300">Rp
                                            {{ number_format($terapis->total_pendapatan ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-5 py-3">
                                            <div class="flex items-center justify-end gap-2">
                                                <div class="progress-bar w-20">
                                                    <div class="progress-fill"
                                                        style="width:{{ $rate }}%;background:{{ $rate >= 80 ? 'linear-gradient(90deg,#10B981,#6EE7B7)' : ($rate >= 50 ? 'linear-gradient(90deg,#F59E0B,#FCD34D)' : 'linear-gradient(90deg,#F43F5E,#FDA4AF)') }}">
                                                    </div>
                                                </div>
                                                <span
                                                    class="text-xs font-semibold {{ $rate >= 80 ? 'text-emerald-500' : ($rate >= 50 ? 'text-amber-500' : 'text-rose-500') }}">{{ $rate }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-10 text-gray-400">Belum ada data.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ===== SEC: TRANSAKSI ===== --}}
            <div class="section-block" id="sec-transaksi">
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div
                        class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <span class="section-title">🧾 Detail Transaksi</span>
                        <span class="text-xs text-gray-400">{{ $detailTransaksi->total() }} transaksi</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Waktu
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                        Pelanggan</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                        Layanan</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                        Terapis</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                                        Harga</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">
                                        Bayar</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                @forelse($detailTransaksi as $booking)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-4 py-3 text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($booking->scheduled_at)->format('d/m H:i') }}</td>
                                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">
                                            {{ $booking->customer->name ?? '—' }}</td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                            {{ $booking->service->name ?? '—' }}</td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                            {{ $booking->therapist->name ?? '—' }}</td>
                                        <td
                                            class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-gray-200">
                                            Rp {{ number_format($booking->final_price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if ($booking->payment)
                                                <span
                                                    class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $booking->payment->method === 'qris' ? 'bg-cyan-100 text-cyan-700' : 'bg-amber-100 text-amber-700' }}">
                                                    {{ strtoupper($booking->payment->method) }}
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @php
                                                $cls = match ($booking->status) {
                                                    'completed' => 'bg-emerald-100 text-emerald-700',
                                                    'cancelled' => 'bg-red-100 text-red-700',
                                                    'ongoing' => 'bg-blue-100 text-blue-700',
                                                    'scheduled' => 'bg-amber-100 text-amber-700',
                                                    default => 'bg-gray-100 text-gray-500',
                                                };
                                                $lbl = match ($booking->status) {
                                                    'completed' => 'Selesai',
                                                    'cancelled' => 'Batal',
                                                    'ongoing' => 'Berlangsung',
                                                    'scheduled' => 'Terjadwal',
                                                    default => $booking->status,
                                                };
                                            @endphp
                                            <span class="status-badge {{ $cls }}">{{ $lbl }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-10 text-gray-400">Belum ada
                                            transaksi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($detailTransaksi->hasPages())
                        <div class="px-5 py-3 border-t border-gray-50 dark:border-gray-700">
                            {{ $detailTransaksi->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // ── SECTION TOGGLE — tersimpan di sessionStorage ──────────────────────
        const SKEY = 'laporan_vis';

        const DEFAULTS = {
            'sec-keuangan': true,
            'sec-pengunjung': true,
            'sec-grafik': true,
            'sec-layanan': true,
            'sec-terapis': true,
            'sec-transaksi': true,
        };

        function loadVis() {
            try {
                const raw = sessionStorage.getItem(SKEY);
                return raw ? {
                    ...DEFAULTS,
                    ...JSON.parse(raw)
                } : {
                    ...DEFAULTS
                };
            } catch (e) {
                return {
                    ...DEFAULTS
                };
            }
        }

        function saveVis(v) {
            try {
                sessionStorage.setItem(SKEY, JSON.stringify(v));
            } catch (e) {}
        }

        function applyVis(v) {
            Object.entries(v).forEach(([key, show]) => {
                const el = document.getElementById(key);
                const chip = document.querySelector(`[data-sec="${key}"]`);
                if (!el || !chip) return;
                el.classList.toggle('sec-hidden', !show);
                chip.classList.toggle('chip-on', show);
                chip.classList.toggle('chip-off', !show);
            });
        }

        function toggleSec(key) {
            const v = loadVis();
            v[key] = !v[key];
            saveVis(v);
            applyVis(v);
        }

        function resetSecs() {
            saveVis(DEFAULTS);
            applyVis(DEFAULTS);
        }

        // Apply on load
        applyVis(loadVis());

        // ── MODE SWITCHING ────────────────────────────────────────────────────
        function setMode(mode) {
            document.getElementById('modeInput').value = mode;
            ['harian', 'mingguan', 'bulanan'].forEach(m => {
                document.getElementById('tab-' + m).classList.toggle('active', m === mode);
                document.getElementById('input-' + m).classList.toggle('hidden', m !== mode);
            });
            document.getElementById('filterForm').submit();
        }

        // ── CHARTS ────────────────────────────────────────────────────────────
        const labels = @json($chartLabels);
        const pendapatan = @json($chartPendapatan);
        const booking = @json($chartBooking);
        const isDark = document.documentElement.classList.contains('dark');
        const gridC = isDark ? 'rgba(255,255,255,0.07)' : 'rgba(0,0,0,0.06)';
        const textC = isDark ? '#9CA3AF' : '#6B7280';
        const scales = {
            x: {
                grid: {
                    color: gridC
                },
                ticks: {
                    color: textC,
                    maxTicksLimit: 12
                }
            },
            y: {
                grid: {
                    color: gridC
                },
                ticks: {
                    color: textC
                }
            }
        };

        new Chart(document.getElementById('chartPendapatan'), {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    data: pendapatan,
                    backgroundColor: 'rgba(79,70,229,.7)',
                    borderColor: '#4F46E5',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    ...scales,
                    y: {
                        ...scales.y,
                        ticks: {
                            color: textC,
                            callback: v => 'Rp ' + (v >= 1e6 ? (v / 1e6).toFixed(1) + 'jt' : (v / 1e3).toFixed(0) +
                                'rb')
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('chartBooking'), {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    data: booking,
                    backgroundColor: 'rgba(16,185,129,.15)',
                    borderColor: '#10B981',
                    borderWidth: 2,
                    fill: true,
                    tension: .4,
                    pointBackgroundColor: '#10B981',
                    pointRadius: 3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales
            }
        });
    </script>
</x-app-layout>
