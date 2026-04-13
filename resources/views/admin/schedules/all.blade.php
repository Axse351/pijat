<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Semua Jadwal Terapis') }}
            </h2>
            <a href="{{ route('admin.schedules.index') }}"
                class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-sm font-semibold transition">
                ← {{ __('Per Terapis') }}
            </a>
        </div>
    </x-slot>

    {{-- ── Pastel palette: 12 warna, di-assign ke terapis berdasarkan index --}}
    @php
        $pastelPalette = [
            // [ bg-pill, text-pill, bg-avatar, text-avatar, bg-dot, ring ]
            ['bg-rose-100', 'text-rose-700', 'bg-rose-400', 'text-white', 'bg-rose-400', 'ring-rose-300'],
            ['bg-sky-100', 'text-sky-700', 'bg-sky-400', 'text-white', 'bg-sky-400', 'ring-sky-300'],
            [
                'bg-emerald-100',
                'text-emerald-700',
                'bg-emerald-400',
                'text-white',
                'bg-emerald-400',
                'ring-emerald-300',
            ],
            ['bg-violet-100', 'text-violet-700', 'bg-violet-400', 'text-white', 'bg-violet-400', 'ring-violet-300'],
            ['bg-amber-100', 'text-amber-700', 'bg-amber-400', 'text-white', 'bg-amber-400', 'ring-amber-300'],
            ['bg-pink-100', 'text-pink-700', 'bg-pink-400', 'text-white', 'bg-pink-400', 'ring-pink-300'],
            ['bg-teal-100', 'text-teal-700', 'bg-teal-400', 'text-white', 'bg-teal-400', 'ring-teal-300'],
            ['bg-orange-100', 'text-orange-700', 'bg-orange-400', 'text-white', 'bg-orange-400', 'ring-orange-300'],
            ['bg-indigo-100', 'text-indigo-700', 'bg-indigo-400', 'text-white', 'bg-indigo-400', 'ring-indigo-300'],
            ['bg-lime-100', 'text-lime-700', 'bg-lime-500', 'text-white', 'bg-lime-500', 'ring-lime-300'],
            [
                'bg-fuchsia-100',
                'text-fuchsia-700',
                'bg-fuchsia-400',
                'text-white',
                'bg-fuchsia-400',
                'ring-fuchsia-300',
            ],
            ['bg-cyan-100', 'text-cyan-700', 'bg-cyan-400', 'text-white', 'bg-cyan-400', 'ring-cyan-300'],
        ];

        // Map therapist id → palette index
        $therapistColors = [];
        foreach ($therapists as $idx => $t) {
            $therapistColors[$t->id] = $pastelPalette[$idx % count($pastelPalette)];
        }
    @endphp

    <style>
        /* Tooltip sederhana tanpa JS */
        .has-tooltip {
            position: relative;
        }

        .has-tooltip .tooltip-text {
            visibility: hidden;
            opacity: 0;
            position: absolute;
            bottom: calc(100% + 6px);
            left: 50%;
            transform: translateX(-50%);
            background: #1e293b;
            color: #fff;
            font-size: 11px;
            white-space: nowrap;
            padding: 4px 8px;
            border-radius: 6px;
            pointer-events: none;
            transition: opacity .15s ease;
            z-index: 50;
        }

        .has-tooltip:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }

        /* Animasi pulse untuk hari ini */
        @keyframes today-ring {

            0%,
            100% {
                box-shadow: 0 0 0 2px #6366f1, 0 0 0 4px rgba(99, 102, 241, .2);
            }

            50% {
                box-shadow: 0 0 0 2px #6366f1, 0 0 0 7px rgba(99, 102, 241, .08);
            }
        }

        .today-cell {
            animation: today-ring 2.5s ease-in-out infinite;
        }
    </style>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div
                    class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <p class="text-green-700 dark:text-green-300">{{ session('success') }}</p>
                </div>
            @endif

            {{-- ── Filter ── --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6">
                <div class="p-5">
                    <form method="GET" class="flex flex-wrap gap-4 items-end">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Bulan</label>
                            <select name="month"
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                onchange="this.form.submit()">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::createFromDate($year, $m, 1)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tahun</label>
                            <select name="year"
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                onchange="this.form.submit()">
                                @for ($y = now()->year - 1; $y <= now()->year + 2; $y++)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                        {{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="ml-auto flex gap-2">
                            <button type="button" onclick="setView('table')" id="btn-table"
                                class="px-3 py-2 text-sm rounded-lg border transition font-medium">
                                📋 Tabel
                            </button>
                            <button type="button" onclick="setView('card')" id="btn-card"
                                class="px-3 py-2 text-sm rounded-lg border transition font-medium">
                                🗓 Kalender
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ── Legend Terapis (warna pastel) ── --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-4 p-4">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Warna
                    Terapis</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($therapists as $idx => $t)
                        @php $c = $therapistColors[$t->id]; @endphp
                        <span
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold {{ $c[0] }} {{ $c[1] }}">
                            <span class="w-2 h-2 rounded-full {{ $c[4] }} inline-block"></span>
                            {{ $t->name }}
                        </span>
                    @endforeach
                </div>
                {{-- Legend status --}}
                <div
                    class="flex flex-wrap gap-3 mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 text-xs text-gray-500 dark:text-gray-400">
                    <span class="flex items-center gap-1.5"><span
                            class="w-3 h-3 rounded bg-green-500 inline-block"></span> Kerja Pagi</span>
                    <span class="flex items-center gap-1.5"><span
                            class="w-3 h-3 rounded bg-green-800 inline-block"></span> Kerja Malam</span>
                    <span class="flex items-center gap-1.5"><span
                            class="w-3 h-3 rounded bg-orange-400 inline-block"></span> Libur</span>
                    <span class="flex items-center gap-1.5"><span
                            class="w-3 h-3 rounded bg-gray-400 inline-block"></span> Sakit</span>
                    <span class="flex items-center gap-1.5"><span
                            class="w-3 h-3 rounded bg-blue-400 inline-block"></span> Ijin</span>
                    <span class="flex items-center gap-1.5"><span
                            class="w-3 h-3 rounded bg-red-400 inline-block"></span> Cuti Bersama</span>
                    <span class="flex items-center gap-1.5"><span
                            class="w-3 h-3 rounded bg-gray-100 border border-dashed border-gray-300 inline-block"></span>
                        Belum ada</span>
                </div>
            </div>

            {{-- ══════════════════════════════════════════
                 VIEW: TABLE
            ══════════════════════════════════════════ --}}
            <div id="view-table">
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th
                                    class="sticky left-0 z-10 bg-gray-50 dark:bg-gray-900 px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-24">
                                    Tanggal
                                </th>
                                <th
                                    class="px-2 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-10 bg-gray-50 dark:bg-gray-900">
                                    Hari
                                </th>
                                @foreach ($therapists as $t)
                                    @php $c = $therapistColors[$t->id]; @endphp
                                    <th class="px-3 py-3 text-center min-w-[130px] bg-gray-50 dark:bg-gray-900">
                                        <div class="flex flex-col items-center gap-1">
                                            <div
                                                class="w-7 h-7 rounded-full {{ $c[2] }} {{ $c[3] }} flex items-center justify-center text-xs font-bold ring-2 {{ $c[5] }}">
                                                {{ strtoupper(substr($t->name, 0, 1)) }}
                                            </div>
                                            <span
                                                class="text-xs font-semibold text-gray-700 dark:text-gray-200 leading-tight">{{ $t->name }}</span>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($days as $day)
                                @php
                                    $isToday = $day['date']->isToday();
                                    $isWeekend = in_array($day['date']->dayOfWeek, [0, 6]);
                                    $rowBg = $isToday
                                        ? 'bg-indigo-50 dark:bg-indigo-900/20'
                                        : ($isWeekend
                                            ? 'bg-amber-50/40 dark:bg-amber-900/10'
                                            : '');
                                @endphp
                                <tr
                                    class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition {{ $rowBg }}">

                                    {{-- Tanggal --}}
                                    <td
                                        class="sticky left-0 z-10 px-4 py-2 {{ $rowBg ?: 'bg-white dark:bg-gray-800' }} border-r border-gray-100 dark:border-gray-700">
                                        <span
                                            class="font-bold {{ $isToday ? 'text-indigo-600' : 'text-gray-700 dark:text-gray-200' }}">
                                            {{ $day['date']->format('d') }}
                                        </span>
                                        @if ($isToday)
                                            <span
                                                class="ml-1 text-xs bg-indigo-500 text-white px-1.5 py-0.5 rounded-full">Hari
                                                ini</span>
                                        @endif
                                        <div class="text-xs text-gray-400">{{ $day['date']->format('M Y') }}</div>
                                    </td>

                                    {{-- Nama hari --}}
                                    <td
                                        class="px-2 py-2 text-center text-xs font-medium {{ $isWeekend ? 'text-amber-600 dark:text-amber-400' : 'text-gray-500 dark:text-gray-400' }}">
                                        {{ ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'][$day['date']->dayOfWeek] }}
                                    </td>

                                    {{-- Kolom per terapis --}}
                                    @foreach ($therapists as $t)
                                        @php
                                            $c = $therapistColors[$t->id];
                                            $sched = $day['schedules'][$t->id] ?? null;
                                            $status = $sched?->status;

                                            $isNightShift = false;
                                            if ($status === 'working' && $sched->start_time) {
                                                $startHour = \Carbon\Carbon::parse($sched->start_time)->hour;
                                                $isNightShift = $startHour >= 18 || $startHour < 6;
                                            }

                                            // Pill: pakai warna pastel terapis untuk status kerja, override untuk non-kerja
                                            [$pillBg, $pillText] = match ($status) {
                                                'working' => $isNightShift
                                                    ? ['bg-green-800 dark:bg-green-900', 'text-white']
                                                    : [$c[0], $c[1]],
                                                'off' => [
                                                    'bg-orange-100 dark:bg-orange-900/30',
                                                    'text-orange-700 dark:text-orange-300',
                                                ],
                                                'sick' => [
                                                    'bg-gray-100 dark:bg-gray-700',
                                                    'text-gray-500 dark:text-gray-300',
                                                ],
                                                'vacation' => [
                                                    'bg-blue-100 dark:bg-blue-900/30',
                                                    'text-blue-700 dark:text-blue-300',
                                                ],
                                                'cuti_bersama' => [
                                                    'bg-red-100 dark:bg-red-900/30',
                                                    'text-red-700 dark:text-red-300',
                                                ],
                                                default => [
                                                    'bg-gray-100 dark:bg-gray-700/40 border border-dashed border-gray-300 dark:border-gray-600',
                                                    'text-gray-400',
                                                ],
                                            };

                                            $pillLabel = match ($status) {
                                                'working' => $isNightShift ? 'Mlm' : 'Kerja',
                                                'off' => 'Libur',
                                                'sick' => 'Sakit',
                                                'vacation' => 'Ijin',
                                                'cuti_bersama' => 'Cuti',
                                                default => '—',
                                            };
                                        @endphp
                                        <td class="px-3 py-2 text-center">
                                            <div class="flex flex-col items-center gap-0.5">
                                                @if ($sched)
                                                    <a href="{{ route('admin.schedules.edit', $sched) }}"
                                                        class="has-tooltip inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $pillBg }} {{ $pillText }} hover:opacity-75 transition">
                                                        {{-- Titik warna terapis di sebelah label --}}
                                                        @if ($status === 'working' && !$isNightShift)
                                                            <span
                                                                class="w-1.5 h-1.5 rounded-full {{ $c[4] }} opacity-60 flex-shrink-0"></span>
                                                        @endif
                                                        {{ $pillLabel }}
                                                        <span class="tooltip-text">Edit jadwal {{ $t->name }} —
                                                            {{ $day['date']->format('d M Y') }}</span>
                                                    </a>
                                                    @if ($status === 'working' && $sched->start_time)
                                                        <span class="text-xs text-gray-400 dark:text-gray-500">
                                                            {{ \Carbon\Carbon::parse($sched->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($sched->end_time)->format('H:i') }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <a href="{{ route('admin.schedules.create', ['therapist_id' => $t->id, 'date' => $day['date']->format('Y-m-d'), 'month' => $month, 'year' => $year]) }}"
                                                        class="has-tooltip inline-block px-2.5 py-1 rounded-full text-xs font-semibold {{ $pillBg }} {{ $pillText }} hover:opacity-75 transition">
                                                        —
                                                        <span class="tooltip-text">Tambah jadwal
                                                            {{ $t->name }}</span>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ══════════════════════════════════════════
                 VIEW: CALENDAR CARD
            ══════════════════════════════════════════ --}}
            <div id="view-card" class="hidden">
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">

                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}
                    </h3>

                    <div class="grid grid-cols-7 gap-2 mb-2">
                        @foreach (['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $dn)
                            <div
                                class="text-center text-xs font-bold text-gray-500 dark:text-gray-400 py-2 uppercase tracking-wide">
                                {{ $dn }}
                            </div>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-7 gap-2">
                        @for ($i = 0; $i < $firstDow; $i++)
                            <div class="min-h-32 bg-gray-50 dark:bg-gray-900/30 rounded-lg"></div>
                        @endfor

                        @foreach ($days as $day)
                            @php $isToday = $day['date']->isToday(); @endphp
                            <div
                                class="min-h-32 border rounded-lg p-2 {{ $isToday ? 'today-cell border-indigo-400' : 'border-gray-200 dark:border-gray-700' }} bg-white dark:bg-gray-800">
                                <div
                                    class="text-xs font-bold mb-1.5 {{ $isToday ? 'text-indigo-600' : 'text-gray-700 dark:text-gray-300' }}">
                                    {{ $day['date']->format('d') }}
                                </div>

                                <div class="space-y-1">
                                    @foreach ($therapists as $t)
                                        @php
                                            $c = $therapistColors[$t->id];
                                            $sched = $day['schedules'][$t->id] ?? null;
                                            $status = $sched?->status;

                                            $isNightShift = false;
                                            if ($status === 'working' && $sched->start_time) {
                                                $startHour = \Carbon\Carbon::parse($sched->start_time)->hour;
                                                $isNightShift = $startHour >= 18 || $startHour < 6;
                                            }

                                            // Dot: pakai warna terapis kalau kerja, warna status kalau tidak
                                            $dot = match ($status) {
                                                'working' => $isNightShift ? 'bg-green-800' : $c[4],
                                                'off' => 'bg-orange-400',
                                                'sick' => 'bg-gray-400',
                                                'vacation' => 'bg-blue-400',
                                                'cuti_bersama' => 'bg-red-400',
                                                default => 'bg-gray-200 border border-dashed border-gray-300',
                                            };
                                        @endphp
                                        @if ($sched)
                                            <a href="{{ route('admin.schedules.edit', $sched) }}"
                                                class="has-tooltip flex items-center gap-1 group hover:opacity-80 transition"
                                                title="{{ $t->name }}: {{ ucfirst($status) }}">
                                                <span
                                                    class="w-2 h-2 rounded-full flex-shrink-0 {{ $dot }}"></span>
                                                <span
                                                    class="text-xs text-gray-600 dark:text-gray-400 truncate group-hover:{{ $c[1] }} leading-tight transition">
                                                    {{ explode(' ', $t->name)[0] }}
                                                </span>
                                            </a>
                                        @else
                                            <a href="{{ route('admin.schedules.create', ['therapist_id' => $t->id, 'date' => $day['date']->format('Y-m-d'), 'month' => $month, 'year' => $year]) }}"
                                                class="has-tooltip flex items-center gap-1 group hover:opacity-80 transition"
                                                title="Tambah jadwal {{ $t->name }}">
                                                <span
                                                    class="w-2 h-2 rounded-full flex-shrink-0 {{ $dot }}"></span>
                                                <span
                                                    class="text-xs text-gray-400 truncate group-hover:{{ $c[1] }} italic leading-tight transition">
                                                    {{ explode(' ', $t->name)[0] }}
                                                </span>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ── Summary per Terapis ── --}}
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach ($therapists as $t)
                    @php
                        $c = $therapistColors[$t->id];
                        $tScheds = $allSchedules->where('therapist_id', $t->id);
                        $workCount = $tScheds->where('status', 'working')->count();
                        $offCount = $tScheds->where('status', 'off')->count();
                        $sickCount = $tScheds->where('status', 'sick')->count();
                        $ijinCount = $tScheds->where('status', 'vacation')->count();
                        $cutiCount = $tScheds->where('status', 'cuti_bersama')->count();
                        $totalHours = $tScheds->sum('working_hours');
                    @endphp
                    <div
                        class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 border-t-4 {{ str_replace('bg-', 'border-', $c[2]) }}">
                        <div class="flex items-center gap-3 mb-3">
                            <div
                                class="w-9 h-9 rounded-full {{ $c[2] }} {{ $c[3] }} flex items-center justify-center font-bold text-sm ring-2 {{ $c[5] }}">
                                {{ strtoupper(substr($t->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-gray-800 dark:text-gray-100 text-sm">
                                    {{ $t->name }}</div>
                                <div class="text-xs text-gray-400">{{ $t->specialization ?? 'Terapis' }}</div>
                            </div>
                            <a href="{{ route('admin.schedules.index', ['therapist_id' => $t->id, 'month' => $month, 'year' => $year]) }}"
                                class="ml-auto text-xs {{ $c[1] }} hover:underline font-semibold">Detail →</a>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-center text-xs">
                            <div class="rounded-lg {{ $c[0] }} py-2">
                                <div class="text-sm font-bold {{ $c[1] }}">{{ $workCount }}</div>
                                <div class="text-gray-500 dark:text-gray-400 text-xs mt-0.5">Kerja</div>
                            </div>
                            <div class="rounded-lg bg-orange-50 dark:bg-orange-900/20 py-2">
                                <div class="text-sm font-bold text-orange-600">{{ $offCount }}</div>
                                <div class="text-gray-500 dark:text-gray-400 text-xs mt-0.5">Libur</div>
                            </div>
                            <div class="rounded-lg bg-gray-50 dark:bg-gray-700/40 py-2">
                                <div class="text-sm font-bold text-gray-500">{{ $sickCount }}</div>
                                <div class="text-gray-500 dark:text-gray-400 text-xs mt-0.5">Sakit</div>
                            </div>
                            <div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 py-2">
                                <div class="text-sm font-bold text-blue-600">{{ $ijinCount }}</div>
                                <div class="text-gray-500 dark:text-gray-400 text-xs mt-0.5">Ijin</div>
                            </div>
                            <div class="rounded-lg bg-red-50 dark:bg-red-900/20 py-2">
                                <div class="text-sm font-bold text-red-600">{{ $cutiCount }}</div>
                                <div class="text-gray-500 dark:text-gray-400 text-xs mt-0.5">Cuti</div>
                            </div>
                            <div class="rounded-lg {{ $c[0] }} py-2">
                                <div class="text-sm font-bold {{ $c[1] }}">{{ $totalHours }}</div>
                                <div class="text-gray-500 dark:text-gray-400 text-xs mt-0.5">Jam</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>

    <script>
        const ACTIVE = 'bg-indigo-500 text-white border-indigo-500';
        const INACTIVE =
            'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:border-indigo-400';

        function setView(v) {
            const isTable = v === 'table';
            document.getElementById('view-table').classList.toggle('hidden', !isTable);
            document.getElementById('view-card').classList.toggle('hidden', isTable);
            document.getElementById('btn-table').className =
                `px-3 py-2 text-sm rounded-lg border transition font-medium ${isTable ? ACTIVE : INACTIVE}`;
            document.getElementById('btn-card').className =
                `px-3 py-2 text-sm rounded-lg border transition font-medium ${isTable ? INACTIVE : ACTIVE}`;
            localStorage.setItem('schedAllView', v);
        }

        const saved = localStorage.getItem('schedAllView') || 'table';
        setView(saved);
    </script>
</x-app-layout>
