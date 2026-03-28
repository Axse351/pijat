<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Laporan</h2>
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

        .stat-card.indigo::before {
            background: linear-gradient(90deg, #6366F1, #A5B4FC);
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

        .section-block.sec-hidden {
            display: none;
        }

        .section-title {
            font-size: 15px;
            font-weight: 700;
            color: #374151;
        }

        .dark .section-title {
            color: #E5E7EB;
        }

        .split-bar {
            display: flex;
            height: 32px;
            border-radius: 10px;
            overflow: hidden;
        }

        .split-bar-spa {
            background: linear-gradient(90deg, #10B981, #6EE7B7);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .split-bar-komisi {
            background: linear-gradient(90deg, #F59E0B, #FCD34D);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .split-bar-empty {
            background: #F3F4F6;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .dark .split-bar-empty {
            background: #374151;
        }

        .split-bar span {
            font-size: 11px;
            font-weight: 700;
            color: white;
            white-space: nowrap;
            padding: 0 6px;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .tbl-daily td,
        .tbl-daily th {
            padding: 8px 12px;
        }

        .rp {
            font-variant-numeric: tabular-nums;
        }
    </style>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ── FILTER ─────────────────────────────────────────────────────── --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4">
                <form method="GET" action="{{ route('admin.laporan.index') }}" id="filterForm">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="text-sm font-semibold text-gray-500 me-2">Periode:</span>
                        @foreach (['harian' => '📅 Harian', 'mingguan' => '📆 Mingguan', 'bulanan' => '🗓️ Bulanan', 'rentang' => '📊 Rentang'] as $m => $lbl)
                            <button type="button" onclick="setMode('{{ $m }}')"
                                class="tab-btn {{ $mode === $m ? 'active' : '' }}"
                                id="tab-{{ $m }}">{{ $lbl }}</button>
                        @endforeach
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
                        <div id="input-rentang" class="{{ $mode !== 'rentang' ? 'hidden' : '' }} flex items-end gap-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Dari</label>
                                <input type="date" name="range_start" value="{{ $rangeStart }}"
                                    class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Sampai</label>
                                <input type="date" name="range_end" value="{{ $rangeEnd }}"
                                    class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-white">
                            </div>
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition">Tampilkan</button>
                        </div>
                        <div class="ms-auto text-right">
                            <div class="text-xs text-gray-400">Menampilkan</div>
                            <div class="text-sm font-bold text-indigo-600 dark:text-indigo-400">{{ $label }}
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- ── EXPORT ──────────────────────────────────────────────────────── --}}
            <div class="flex gap-2 justify-end">
                <a href="{{ route('admin.laporan.export', request()->query()) }}"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-semibold hover:bg-green-700 transition flex items-center gap-2">
                    📥 Export Excel (4 Sheet)
                </a>
            </div>

            {{-- ── TOGGLE SECTIONS ─────────────────────────────────────────────── --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-semibold text-gray-400 uppercase me-1">Tampilkan:</span>
                    @foreach ([
        'sec-keuangan' => ['💰', 'Keuangan', '#EEF2FF', '#4F46E5'],
        'sec-rekap' => ['📅', 'Rekap Harian', '#F0FDF4', '#10B981'],
        'sec-terapis' => ['🧑‍⚕️', 'Terapis', '#F0F9FF', '#0EA5E9'],
        'sec-layanan' => ['💆', 'Layanan', '#FDF2F8', '#EC4899'],
        'sec-grafik' => ['📈', 'Grafik', '#FFF7ED', '#F59E0B'],
        'sec-transaksi' => ['🧾', 'Transaksi', '#F5F3FF', '#8B5CF6'],
    ] as $key => [$icon, $name, $bg, $color])
                        <span class="widget-chip chip-on"
                            style="background:{{ $bg }};color:{{ $color }};border-color:{{ $color }}"
                            onclick="toggleSec('{{ $key }}',this)"
                            data-sec="{{ $key }}">{{ $icon }} {{ $name }}</span>
                    @endforeach
                    <button onclick="resetSecs()"
                        class="ms-auto text-xs text-gray-400 hover:text-indigo-500 font-medium">↺ Reset</button>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════════════
             SEC: KEUANGAN
        ════════════════════════════════════════════════════════════════════ --}}
            <div class="section-block" id="sec-keuangan">
                <h3 class="section-title mb-3">💰 Laporan Keuangan</h3>

                {{-- Row 1: 3 kartu utama --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
                    <div
                        class="stat-card blue bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                        <div class="text-xs text-gray-400 mb-1">Total Uang Masuk (Bruto)</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white rp">Rp
                            {{ number_format($totalBruto, 0, ',', '.') }}</div>
                        <div class="text-xs text-blue-500 mt-1">Setelah diskon · dari pelanggan</div>
                    </div>
                    <div
                        class="stat-card amber bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                        <div class="text-xs text-gray-400 mb-1">Total Komisi Terapis</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white rp">Rp
                            {{ number_format($totalKomisiTerapis, 0, ',', '.') }}</div>
                        <div class="text-xs text-amber-500 mt-1">
                            Pijat Rp {{ number_format($totalKomisiPijat, 0, ',', '.') }} +
                            Hadir Rp {{ number_format($totalBonusHadir, 0, ',', '.') }}
                        </div>
                    </div>
                    <div
                        class="stat-card emerald bg-white dark:bg-gray-800 rounded-xl p-5 border-2 border-emerald-400 dark:border-emerald-500">
                        <div class="flex items-center gap-1 text-xs text-gray-400 mb-1 font-semibold">
                            <span class="text-emerald-500">✅</span> Pendapatan Bersih Spa
                        </div>
                        <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 rp">Rp
                            {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                        @php $spaPct = $totalBruto > 0 ? round(($totalPendapatan/$totalBruto)*100) : 0 @endphp
                        <div class="text-xs text-emerald-500 mt-1">{{ $spaPct }}% dari bruto</div>
                    </div>
                </div>

                {{-- Row 2: breakdown komisi --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                        <div class="text-xs text-gray-400 mb-1">Harga Asli (Sebelum Diskon)</div>
                        <div class="text-lg font-bold text-gray-700 dark:text-gray-300 rp">Rp
                            {{ number_format($totalHargaAsli, 0, ',', '.') }}</div>
                    </div>
                    <div
                        class="stat-card rose bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                        <div class="text-xs text-gray-400 mb-1">Total Diskon Pelanggan</div>
                        <div class="text-lg font-bold text-rose-500 rp">− Rp
                            {{ number_format($totalDiskon, 0, ',', '.') }}</div>
                    </div>
                    <div
                        class="stat-card indigo bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                        <div class="text-xs text-gray-400 mb-1">Komisi Pijat (25%)</div>
                        <div class="text-lg font-bold text-indigo-600 rp">Rp
                            {{ number_format($totalKomisiPijat, 0, ',', '.') }}</div>
                        <div class="text-xs text-gray-400">Per sesi selesai</div>
                    </div>
                    <div
                        class="stat-card amber bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                        <div class="text-xs text-gray-400 mb-1">Bonus Kehadiran</div>
                        <div class="text-lg font-bold text-amber-600 rp">Rp
                            {{ number_format($totalBonusHadir, 0, ',', '.') }}</div>
                        <div class="text-xs text-gray-400">Rp 20.000 × hari hadir</div>
                    </div>
                </div>

                {{-- Row 3: metode bayar + split bar --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    {{-- Split bar --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                        <div class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-4">Pembagian Uang Masuk
                        </div>
                        @php
                            $spaPct = $totalBruto > 0 ? round(($totalPendapatan / $totalBruto) * 100) : 0;
                            $komisiPct = $totalBruto > 0 ? round(($totalKomisiTerapis / $totalBruto) * 100) : 0;
                        @endphp
                        <div class="split-bar mb-3">
                            @if ($totalBruto > 0)
                                @if ($spaPct > 0)
                                    <div class="split-bar-spa" style="width:{{ $spaPct }}%">
                                        @if ($spaPct > 10)
                                            <span>Spa {{ $spaPct }}%</span>
                                        @endif
                                    </div>
                                @endif
                                @if ($komisiPct > 0)
                                    <div class="split-bar-komisi" style="width:{{ $komisiPct }}%">
                                        @if ($komisiPct > 10)
                                            <span>Terapis {{ $komisiPct }}%</span>
                                        @endif
                                    </div>
                                @endif
                            @else
                                <div class="split-bar-empty"><span class="text-xs text-gray-400">Belum ada data</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-wrap gap-4 text-xs mt-2">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full"
                                    style="background:#10B981;display:inline-block"></span>
                                <span class="text-gray-500">Kas Spa</span>
                                <span class="font-bold text-gray-800 dark:text-white rp">Rp
                                    {{ number_format($totalPendapatan, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full"
                                    style="background:#F59E0B;display:inline-block"></span>
                                <span class="text-gray-500">Komisi Terapis</span>
                                <span class="font-bold text-gray-800 dark:text-white rp">Rp
                                    {{ number_format($totalKomisiTerapis, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    {{-- Metode bayar --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                        <div class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-4">Metode Pembayaran
                        </div>
                        @php
                            $totalPay = $pendapatanQris + $pendapatanCash;
                            $qrisPct = $totalPay > 0 ? round(($pendapatanQris / $totalPay) * 100) : 0;
                            $cashPct = 100 - $qrisPct;
                        @endphp
                        @foreach (['QRIS' => [$pendapatanQris, $qrisPct, '#06B6D4', 'cyan'], 'Cash' => [$pendapatanCash, $cashPct, '#F59E0B', 'amber']] as $name => [$amt, $pct, $clr, $badge])
                            <div class="mb-3">
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="font-semibold"
                                        style="color:{{ $clr }}">{{ $name }}</span>
                                    <span class="text-gray-500 rp">Rp {{ number_format($amt, 0, ',', '.') }} ·
                                        {{ $pct }}%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill"
                                        style="width:{{ $pct }}%;background:{{ $clr }}"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════════════
             SEC: REKAP HARIAN
        ════════════════════════════════════════════════════════════════════ --}}
            <div class="section-block" id="sec-rekap">
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div
                        class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <span class="section-title">📅 Rekap Harian — {{ $label }}</span>
                        <span class="text-xs text-gray-400">Komisi 25% pijat + Rp 20.000/terapis hadir</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm tbl-daily">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <th class="text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                                    <th class="text-right text-xs font-semibold text-gray-500 uppercase">Sesi</th>
                                    <th class="text-right text-xs font-semibold text-gray-500 uppercase">Terapis Hadir
                                    </th>
                                    <th class="text-right text-xs font-semibold text-gray-500 uppercase">Bruto (Rp)
                                    </th>
                                    <th class="text-right text-xs font-semibold text-gray-500 uppercase">Komisi Pijat
                                        25%</th>
                                    <th class="text-right text-xs font-semibold text-gray-500 uppercase">Bonus Hadir
                                    </th>
                                    <th class="text-right text-xs font-semibold text-gray-500 uppercase">Total Komisi
                                    </th>
                                    <th class="text-right text-xs font-semibold text-gray-500 uppercase">Bersih Spa
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                @php
                                    $totBruto = 0;
                                    $totKomisiP = 0;
                                    $totBonus = 0;
                                    $totKomisi = 0;
                                    $totBersih = 0;
                                    $totSesi = 0;
                                    $totHadir = 0;
                                @endphp
                                @foreach ($rekapHarian as $d)
                                    @php
                                        $totBruto += $d['bruto'];
                                        $totKomisiP += $d['komisi_pijat'];
                                        $totBonus += $d['bonus_hadir'];
                                        $totKomisi += $d['total_komisi'];
                                        $totBersih += $d['bersih'];
                                        $totSesi += $d['sesi'];
                                        $totHadir += $d['hadir'];
                                        $isSunday = $d['tanggal']->dayOfWeek === 0;
                                        $isToday = $d['tanggal']->isToday();
                                    @endphp
                                    <tr
                                        class="hover:bg-indigo-50/30 dark:hover:bg-gray-700/30
                      {{ $isSunday ? 'opacity-50' : '' }}
                      {{ $isToday ? 'bg-indigo-50/50 dark:bg-indigo-900/20' : '' }}">
                                        <td class="font-medium text-gray-700 dark:text-gray-300">
                                            {{ $d['tanggal']->translatedFormat('d M') }}
                                            <span
                                                class="text-xs text-gray-400 ms-1">{{ $d['tanggal']->translatedFormat('D') }}</span>
                                            @if ($isToday)
                                                <span
                                                    class="ms-1 text-xs bg-indigo-100 text-indigo-600 px-1.5 rounded-full">hari
                                                    ini</span>
                                            @endif
                                        </td>
                                        <td
                                            class="text-right font-semibold {{ $d['sesi'] > 0 ? 'text-indigo-600' : 'text-gray-300' }}">
                                            {{ $d['sesi'] ?: '—' }}</td>
                                        <td class="text-right text-gray-600 dark:text-gray-400">
                                            {{ $d['hadir'] ?: '—' }}</td>
                                        <td class="text-right font-semibold text-gray-700 dark:text-gray-300 rp">
                                            {{ $d['bruto'] > 0 ? 'Rp ' . number_format($d['bruto'], 0, ',', '.') : '—' }}
                                        </td>
                                        <td class="text-right text-indigo-600 rp">
                                            {{ $d['komisi_pijat'] > 0 ? 'Rp ' . number_format($d['komisi_pijat'], 0, ',', '.') : '—' }}
                                        </td>
                                        <td class="text-right text-amber-600 rp">
                                            {{ $d['bonus_hadir'] > 0 ? 'Rp ' . number_format($d['bonus_hadir'], 0, ',', '.') : '—' }}
                                        </td>
                                        <td class="text-right font-semibold text-rose-500 rp">
                                            {{ $d['total_komisi'] > 0 ? 'Rp ' . number_format($d['total_komisi'], 0, ',', '.') : '—' }}
                                        </td>
                                        <td class="text-right font-bold text-emerald-600 rp">
                                            {{ $d['bersih'] > 0 ? 'Rp ' . number_format($d['bersih'], 0, ',', '.') : '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-indigo-50 dark:bg-indigo-900/30 font-bold border-t-2 border-indigo-200">
                                    <td class="text-indigo-700 dark:text-indigo-300">TOTAL</td>
                                    <td class="text-right text-indigo-700">{{ $totSesi }}</td>
                                    <td class="text-right text-gray-600">{{ $totHadir }}</td>
                                    <td class="text-right text-gray-800 dark:text-white rp">Rp
                                        {{ number_format($totBruto, 0, ',', '.') }}</td>
                                    <td class="text-right text-indigo-600 rp">Rp
                                        {{ number_format($totKomisiP, 0, ',', '.') }}</td>
                                    <td class="text-right text-amber-600 rp">Rp
                                        {{ number_format($totBonus, 0, ',', '.') }}</td>
                                    <td class="text-right text-rose-600 rp">Rp
                                        {{ number_format($totKomisi, 0, ',', '.') }}</td>
                                    <td class="text-right text-emerald-600 rp">Rp
                                        {{ number_format($totBersih, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════════════
             SEC: TERAPIS
        ════════════════════════════════════════════════════════════════════ --}}
            <div class="section-block" id="sec-terapis">
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div
                        class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <span class="section-title">🧑‍⚕️ Komisi & Kinerja Terapis</span>
                        <span class="text-xs text-gray-400">25% per sesi + Rp 20.000 per hari hadir</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                        Terapis</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Sesi
                                        Selesai</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Hari
                                        Hadir</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                                        Bruto (Rp)</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                                        Komisi Pijat 25%</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                                        Bonus Hadir</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                                        Total Komisi</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                                        Completion %</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                @forelse($laporanTerapis as $t)
                                    @php $rate = $t->total_sesi>0 ? round(($t->sesi_selesai/$t->total_sesi)*100) : 0; @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-5 py-3">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white font-bold text-sm">
                                                    {{ strtoupper(substr($t->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-gray-800 dark:text-gray-200">
                                                        {{ $t->name }}</div>
                                                    <div class="text-xs text-gray-400">
                                                        {{ $t->specialty ?? 'Terapis' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3 text-right font-bold text-indigo-600">
                                            {{ $t->sesi_selesai }}</td>
                                        <td class="px-5 py-3 text-right text-gray-600">{{ $t->hari_hadir }}</td>
                                        <td class="px-5 py-3 text-right text-gray-700 dark:text-gray-300 rp">Rp
                                            {{ number_format($t->total_bruto_fmt ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-5 py-3 text-right text-indigo-600 font-semibold rp">Rp
                                            {{ number_format($t->komisi_pijat ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-5 py-3 text-right text-amber-600 font-semibold rp">Rp
                                            {{ number_format($t->bonus_hadir ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-5 py-3 text-right">
                                            <span class="font-bold text-rose-600 rp">Rp
                                                {{ number_format($t->total_komisi ?? 0, 0, ',', '.') }}</span>
                                        </td>
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
                                        <td colspan="8" class="text-center py-10 text-gray-400">Belum ada data.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════════════
             SEC: LAYANAN
        ════════════════════════════════════════════════════════════════════ --}}
            <div class="section-block" id="sec-layanan">
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                        <span class="section-title">💆 Layanan Terpopuler</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#
                                    </th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                        Layanan</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Sesi
                                    </th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                                        Bruto (Rp)</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                        Proporsi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                @forelse($topLayanan as $i=>$svc)
                                    @php $pct = $totalBooking>0 ? round(($svc->total_sesi/$totalBooking)*100) : 0; @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-5 py-3 text-gray-400 font-mono">{{ $i + 1 }}</td>
                                        <td class="px-5 py-3 font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $svc->name }}</td>
                                        <td class="px-5 py-3 text-right font-bold text-indigo-600">
                                            {{ $svc->total_sesi }}</td>
                                        <td class="px-5 py-3 text-right text-gray-700 dark:text-gray-300 rp">Rp
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

            {{-- ═══════════════════════════════════════════════════════════════════
             SEC: GRAFIK
        ════════════════════════════════════════════════════════════════════ --}}
            <div class="section-block" id="sec-grafik">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-5">
                        <div class="section-title mb-1">📈 Pendapatan Bersih Spa</div>
                        <div class="text-xs text-gray-400 mb-4">Setelah dikurangi komisi pijat 25%</div>
                        <canvas id="chartPendapatan" height="200"></canvas>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-5">
                        <div class="section-title mb-4">📊 Jumlah Booking</div>
                        <canvas id="chartBooking" height="200"></canvas>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════════════
             SEC: TRANSAKSI
        ════════════════════════════════════════════════════════════════════ --}}
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
                                    @foreach (['Waktu', 'Pelanggan', 'Layanan', 'Terapis', 'Harga Asli', 'Diskon', 'Bayar', 'Komisi 25%', 'Metode', 'Status'] as $h)
                                        <th
                                            class="px-4 py-3 text-{{ in_array($h, ['Harga Asli', 'Diskon', 'Bayar', 'Komisi 25%']) ? 'right' : 'left' }} text-xs font-semibold text-gray-500 uppercase">
                                            {{ $h }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                @forelse($detailTransaksi as $b)
                                    @php
                                        $komisiRow = $b->status === 'completed' ? $b->final_price * 0.25 : 0;
                                        $cls = match ($b->status) {
                                            'completed' => 'bg-emerald-100 text-emerald-700',
                                            'cancelled' => 'bg-red-100 text-red-700',
                                            'ongoing' => 'bg-blue-100 text-blue-700',
                                            'scheduled' => 'bg-amber-100 text-amber-700',
                                            default => 'bg-gray-100 text-gray-500',
                                        };
                                        $lbl = match ($b->status) {
                                            'completed' => 'Selesai',
                                            'cancelled' => 'Batal',
                                            'ongoing' => 'Jalan',
                                            'scheduled' => 'Terjadwal',
                                            default => $b->status,
                                        };
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-4 py-3 text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($b->scheduled_at)->setTimezone('Asia/Jakarta')->format('d/m H:i') }}
                                        </td>
                                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">
                                            {{ $b->customer->name ?? '—' }}</td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                            {{ $b->service->name ?? '—' }}</td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                            {{ $b->therapist->name ?? '—' }}</td>
                                        <td class="px-4 py-3 text-right text-gray-600 rp">Rp
                                            {{ number_format($b->price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-right text-rose-500 rp">
                                            {{ $b->discount > 0 ? '−Rp ' . number_format($b->discount, 0, ',', '.') : '—' }}</td>
                                        <td
                                            class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-gray-200 rp">
                                            Rp {{ number_format($b->final_price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-right text-indigo-600 font-semibold rp">
                                            {{ $komisiRow > 0 ? 'Rp ' . number_format($komisiRow, 0, ',', '.') : '—' }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if ($b->payment)
                                                <span
                                                    class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $b->payment->method === 'qris' ? 'bg-cyan-100 text-cyan-700' : 'bg-amber-100 text-amber-700' }}">{{ strtoupper($b->payment->method) }}</span>
                                            @else
                                                <span class="text-xs text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3"><span
                                                class="status-badge {{ $cls }}">{{ $lbl }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-10 text-gray-400">Belum ada
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
        // ── Section toggle ──────────────────────────────────────────────────────
        const SKEY = 'laporan_vis';
        const DEFAULTS = {
            'sec-keuangan': true,
            'sec-rekap': true,
            'sec-terapis': true,
            'sec-layanan': true,
            'sec-grafik': true,
            'sec-transaksi': true
        };
        const loadVis = () => {
            try {
                const r = sessionStorage.getItem(SKEY);
                return r ? {
                    ...DEFAULTS,
                    ...JSON.parse(r)
                } : {
                    ...DEFAULTS
                };
            } catch (e) {
                return {
                    ...DEFAULTS
                };
            }
        };
        const saveVis = v => {
            try {
                sessionStorage.setItem(SKEY, JSON.stringify(v));
            } catch (e) {}
        };

        function applyVis(v) {
            Object.entries(v).forEach(([k, show]) => {
                const el = document.getElementById(k),
                    chip = document.querySelector(`[data-sec="${k}"]`);
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
        applyVis(loadVis());

        // ── Mode switching ──────────────────────────────────────────────────────
        function setMode(mode) {
            document.getElementById('modeInput').value = mode;
            ['harian', 'mingguan', 'bulanan', 'rentang'].forEach(m => {
                document.getElementById('tab-' + m).classList.toggle('active', m === mode);
                document.getElementById('input-' + m).classList.toggle('hidden', m !== mode);
            });
            if (mode !== 'rentang') document.getElementById('filterForm').submit();
        }

        // ── Charts ──────────────────────────────────────────────────────────────
        const labels = @json($chartLabels);
        const pendapatan = @json($chartPendapatan);
        const bruto = @json($chartBruto);
        const booking = @json($chartBooking);
        const isDark = document.documentElement.classList.contains('dark');
        const gridC = isDark ? 'rgba(255,255,255,0.07)' : 'rgba(0,0,0,0.06)';
        const textC = isDark ? '#9CA3AF' : '#6B7280';
        const baseScales = {
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
                    color: textC,
                    callback: v => 'Rp ' + (v >= 1e6 ? (v / 1e6).toFixed(1) + 'jt' : (v / 1e3).toFixed(0) + 'rb')
                }
            }
        };

        new Chart(document.getElementById('chartPendapatan'), {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                        label: 'Bruto',
                        data: bruto,
                        backgroundColor: 'rgba(99,102,241,.3)',
                        borderColor: '#6366F1',
                        borderWidth: 1,
                        borderRadius: 3
                    },
                    {
                        label: 'Bersih Spa',
                        data: pendapatan,
                        backgroundColor: 'rgba(16,185,129,.7)',
                        borderColor: '#10B981',
                        borderWidth: 1,
                        borderRadius: 3
                    },
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: textC,
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' Rp ' + ctx.raw.toLocaleString('id-ID')
                        }
                    }
                },
                scales: baseScales
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
                scales: {
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
                }
            }
        });
    </script>
</x-app-layout>
