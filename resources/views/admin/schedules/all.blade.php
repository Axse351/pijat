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

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div
                    class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <p class="text-green-700 dark:text-green-300">{{ session('success') }}</p>
                </div>
            @endif

            {{-- ── Filter ─────────────────────────────────────────────── --}}
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
                            {{-- View toggle --}}
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

            {{-- ── Legend ──────────────────────────────────────────────── --}}
            <div class="flex flex-wrap gap-3 mb-4 text-xs text-gray-600 dark:text-gray-400">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-green-500 inline-block"></span>
                    Kerja Pagi</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-green-700 inline-block"></span>
                    Kerja Malam</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-orange-500 inline-block"></span>
                    Libur</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-gray-400 inline-block"></span>
                    Sakit</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-blue-500 inline-block"></span>
                    Ijin</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-red-500 inline-block"></span>
                    Cuti Bersama</span>
                <span class="flex items-center gap-1.5"><span
                        class="w-3 h-3 rounded bg-gray-100 border border-dashed border-gray-300 inline-block"></span>
                    Belum ada</span>
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 VIEW: TABLE (default)
                 Baris = tanggal, Kolom = terapis
            ══════════════════════════════════════════════════════════ --}}
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
                                    <th class="px-3 py-3 text-center min-w-[130px] bg-gray-50 dark:bg-gray-900">
                                        <div class="flex flex-col items-center gap-1">
                                            <div
                                                class="w-7 h-7 rounded-full bg-indigo-500 text-white flex items-center justify-center text-xs font-bold">
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
                                            $sched = $day['schedules'][$t->id] ?? null;
                                            $status = $sched?->status;

                                            // Helper function untuk cek waktu kerja
                                            $isNightShift = false;
                                            if ($status === 'working' && $sched->start_time) {
                                                $startHour = \Carbon\Carbon::parse($sched->start_time)->hour;
                                                $isNightShift = $startHour >= 18 || $startHour < 6; // 18:00 - 05:59
                                            }

                                            [$cellBg, $pill, $pillText] = match ($status) {
                                                'working' => $isNightShift
                                                    ? ['', 'bg-green-700 text-white dark:bg-green-800 dark:text-green-100', 'Kerja Mlm']
                                                    : ['', 'bg-green-500 text-white dark:bg-green-600 dark:text-green-100', 'Kerja'],
                                                'off' => [
                                                    '',
                                                    'bg-orange-500 text-white dark:bg-orange-600 dark:text-orange-100',
                                                    'Libur',
                                                ],
                                                'sick' => [
                                                    '',
                                                    'bg-gray-400 text-white dark:bg-gray-500 dark:text-gray-100',
                                                    'Sakit',
                                                ],
                                                'vacation' => [
                                                    '',
                                                    'bg-blue-500 text-white dark:bg-blue-600 dark:text-blue-100',
                                                    'Ijin',
                                                ],
                                                'cuti_bersama' => [
                                                    '',
                                                    'bg-red-500 text-white dark:bg-red-600 dark:text-red-100',
                                                    'Cuti',
                                                ],
                                                default => [
                                                    '',
                                                    'bg-gray-100 dark:bg-gray-700/40 text-gray-400 border border-dashed border-gray-300 dark:border-gray-600',
                                                    '—',
                                                ],
                                            };
                                        @endphp
                                        <td class="px-3 py-2 text-center">
                                            <div class="flex flex-col items-center gap-0.5">
                                                @if ($sched)
                                                    <a href="{{ route('admin.schedules.edit', $sched) }}"
                                                        class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold {{ $pill }} hover:opacity-80 transition cursor-pointer"
                                                        title="Edit jadwal {{ $t->name }} — {{ $day['date']->format('d M Y') }}">
                                                        {{ $pillText }}
                                                    </a>
                                                    @if ($status === 'working' && $sched->start_time)
                                                        <span class="text-xs text-gray-400 dark:text-gray-500">
                                                            {{ \Carbon\Carbon::parse($sched->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($sched->end_time)->format('H:i') }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <a href="{{ route('admin.schedules.create', ['therapist_id' => $t->id, 'date' => $day['date']->format('Y-m-d'), 'month' => $month, 'year' => $year]) }}"
                                                        class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold {{ $pill }} hover:bg-gray-200 transition"
                                                        title="Tambah jadwal {{ $t->name }} — {{ $day['date']->format('d M Y') }}">
                                                        —
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

            {{-- ══════════════════════════════════════════════════════════
                 VIEW: CALENDAR CARD
                 Grid 7 kolom, setiap hari tampilkan semua terapis
            ══════════════════════════════════════════════════════════ --}}
            <div id="view-card" class="hidden">
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">

                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}
                    </h3>

                    {{-- Day headers --}}
                    <div class="grid grid-cols-7 gap-2 mb-2">
                        @foreach (['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $dn)
                            <div
                                class="text-center text-xs font-bold text-gray-500 dark:text-gray-400 py-2 uppercase tracking-wide">
                                {{ $dn }}
                            </div>
                        @endforeach
                    </div>

                    {{-- Calendar grid --}}
                    <div class="grid grid-cols-7 gap-2">
                        {{-- Empty cells before first day --}}
                        @for ($i = 0; $i < $firstDow; $i++)
                            <div class="min-h-32 bg-gray-50 dark:bg-gray-900/30 rounded-lg"></div>
                        @endfor

                        @foreach ($days as $day)
                            @php $isToday = $day['date']->isToday(); @endphp
                            <div
                                class="min-h-32 border rounded-lg p-2 {{ $isToday ? 'ring-2 ring-indigo-400' : 'border-gray-200 dark:border-gray-700' }} bg-white dark:bg-gray-800">

                                <div
                                    class="text-xs font-bold mb-1.5 {{ $isToday ? 'text-indigo-600' : 'text-gray-700 dark:text-gray-300' }}">
                                    {{ $day['date']->format('d') }}
                                </div>

                                <div class="space-y-1">
                                    @foreach ($therapists as $t)
                                        @php
                                            $sched = $day['schedules'][$t->id] ?? null;
                                            $status = $sched?->status;

                                            // Helper function untuk cek waktu kerja
                                            $isNightShift = false;
                                            if ($status === 'working' && $sched->start_time) {
                                                $startHour = \Carbon\Carbon::parse($sched->start_time)->hour;
                                                $isNightShift = $startHour >= 18 || $startHour < 6;
                                            }

                                            $dot = match ($status) {
                                                'working' => $isNightShift ? 'bg-green-700' : 'bg-green-500',
                                                'off' => 'bg-orange-500',
                                                'sick' => 'bg-gray-400',
                                                'vacation' => 'bg-blue-500',
                                                'cuti_bersama' => 'bg-red-500',
                                                default => 'bg-gray-200 border border-dashed border-gray-300',
                                            };
                                        @endphp
                                        @if ($sched)
                                            <a href="{{ route('admin.schedules.edit', $sched) }}"
                                                class="flex items-center gap-1 group hover:opacity-80 transition"
                                                title="{{ $t->name }}: {{ ucfirst($status) }}">
                                                <span
                                                    class="w-2 h-2 rounded-full flex-shrink-0 {{ $dot }}"></span>
                                                <span
                                                    class="text-xs text-gray-600 dark:text-gray-400 truncate group-hover:text-indigo-600 leading-tight">
                                                    {{ explode(' ', $t->name)[0] }}
                                                </span>
                                            </a>
                                        @else
                                            <a href="{{ route('admin.schedules.create', ['therapist_id' => $t->id, 'date' => $day['date']->format('Y-m-d'), 'month' => $month, 'year' => $year]) }}"
                                                class="flex items-center gap-1 group hover:opacity-80 transition"
                                                title="Tambah jadwal {{ $t->name }}">
                                                <span
                                                    class="w-2 h-2 rounded-full flex-shrink-0 {{ $dot }}"></span>
                                                <span
                                                    class="text-xs text-gray-400 truncate group-hover:text-indigo-500 italic leading-tight">
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

            {{-- ── Summary per Terapis ─────────────────────────────────── --}}
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach ($therapists as $t)
                    @php
                        $tScheds = $allSchedules->where('therapist_id', $t->id);
                        $workCount = $tScheds->where('status', 'working')->count();
                        $offCount = $tScheds->where('status', 'off')->count();
                        $sickCount = $tScheds->where('status', 'sick')->count();
                        $ijinCount = $tScheds->where('status', 'vacation')->count();
                        $cutiCount = $tScheds->where('status', 'cuti_bersama')->count();
                        $totalHours = $tScheds->sum('working_hours');
                    @endphp
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4">
                        <div class="flex items-center gap-3 mb-3">
                            <div
                                class="w-9 h-9 rounded-full bg-indigo-500 text-white flex items-center justify-center font-bold text-sm">
                                {{ strtoupper(substr($t->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-gray-800 dark:text-gray-100 text-sm">{{ $t->name }}
                                </div>
                                <div class="text-xs text-gray-400">{{ $t->specialization ?? 'Terapis' }}</div>
                            </div>
                            <a href="{{ route('admin.schedules.index', ['therapist_id' => $t->id, 'month' => $month, 'year' => $year]) }}"
                                class="ml-auto text-xs text-indigo-500 hover:underline">Detail →</a>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-center text-xs">
                            <div>
                                <div class="text-sm font-bold text-green-600">{{ $workCount }}</div>
                                <div class="text-gray-400">Kerja</div>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-orange-500">{{ $offCount }}</div>
                                <div class="text-gray-400">Libur</div>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-gray-400">{{ $sickCount }}</div>
                                <div class="text-gray-400">Sakit</div>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-blue-500">{{ $ijinCount }}</div>
                                <div class="text-gray-400">Ijin</div>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-red-500">{{ $cutiCount }}</div>
                                <div class="text-gray-400">Cuti</div>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-indigo-500">{{ $totalHours }}</div>
                                <div class="text-gray-400">Jam</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>

    <script>
        // ── View toggle ──────────────────────────────────────────────────
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

        // Restore last view
        const saved = localStorage.getItem('schedAllView') || 'table';
        setView(saved);
    </script>
</x-app-layout>
