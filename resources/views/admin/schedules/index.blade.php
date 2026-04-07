<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Jadwal Terapis Bulanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <p class="text-red-700 dark:text-red-300 font-semibold mb-2">{{ __('Terjadi Kesalahan:') }}</p>
                    <ul class="list-disc list-inside text-red-600 dark:text-red-400 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session('success'))
                <div
                    class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <p class="text-green-700 dark:text-green-300">{{ session('success') }}</p>
                </div>
            @endif

            {{-- ── Filter Card ─────────────────────────────────── --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                        <div class="flex-1">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Pilih Terapis') }}</label>
                            <select name="therapist_id"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500"
                                onchange="this.form.submit()">
                                <option value="">-- Pilih Terapis --</option>
                                @foreach ($therapists as $therapist)
                                    <option value="{{ $therapist->id }}"
                                        {{ $selectedTherapist == $therapist->id ? 'selected' : '' }}>
                                        {{ $therapist->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Bulan & Tahun') }}</label>
                            <div class="flex gap-2">
                                <select name="month"
                                    class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100"
                                    onchange="this.form.submit()">
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::createFromDate($year, $m, 1)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                                <select name="year"
                                    class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100"
                                    onchange="this.form.submit()">
                                    @for ($y = now()->year - 1; $y <= now()->year + 2; $y++)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                            {{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        @if ($selectedTherapist)
                            <div class="flex gap-2 flex-wrap">
                                <button type="button" onclick="openGenerateModal()"
                                    class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg text-sm font-semibold transition">
                                    🗓 {{ __('Atur Jadwal') }}
                                </button>
                                <a href="{{ route('admin.schedules.create', ['therapist_id' => $selectedTherapist, 'month' => $month, 'year' => $year]) }}"
                                    class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-semibold transition">
                                    ➕ {{ __('Tambah Manual') }}
                                </a>
                                <a href="{{ route('admin.schedules.all', ['month' => $month, 'year' => $year]) }}"
                                    class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-sm font-semibold transition">
                                    👥 Semua Terapis
                                </a>
                                {{-- ★ Tombol baru: Kelola Pengajuan Izin Terapis ini --}}
                                <a href="{{ route('admin.leaves.index', ['therapist_id' => $selectedTherapist]) }}"
                                    class="relative px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-semibold transition inline-flex items-center gap-1.5">
                                    📋 Pengajuan Izin
                                    @if (isset($pendingLeaveCount) && $pendingLeaveCount > 0)
                                        <span
                                            class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold bg-red-500 text-white rounded-full leading-none">
                                            {{ $pendingLeaveCount }}
                                        </span>
                                    @endif
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            {{-- ★ Leave Request Banner (jika ada pending) --}}
            @if ($selectedTherapist && isset($pendingLeaves) && $pendingLeaves->count() > 0)
                <div
                    class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-300 dark:border-yellow-700 rounded-lg flex items-start gap-3">
                    <span class="text-xl shrink-0">⚠️</span>
                    <div class="flex-1">
                        <p class="font-semibold text-yellow-800 dark:text-yellow-300">
                            Ada {{ $pendingLeaves->count() }} pengajuan izin yang menunggu persetujuan
                        </p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($pendingLeaves as $pl)
                                <a href="{{ route('admin.leaves.show', $pl) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-gray-800 border border-yellow-300 dark:border-yellow-700 rounded-lg text-xs font-medium text-yellow-800 dark:text-yellow-300 hover:bg-yellow-100 dark:hover:bg-yellow-900/40 transition">
                                    @switch($pl->type)
                                        @case('sakit')
                                            🏥
                                        @break

                                        @case('pribadi')
                                            👤
                                        @break

                                        @case('cuti')
                                            🏖️
                                        @break

                                        @case('izin_khusus')
                                            ⭐
                                        @break

                                        @default
                                            📋
                                        @break
                                    @endswitch
                                    {{ $pl->start_date->format('d M') }} – {{ $pl->end_date->format('d M Y') }}
                                    <span
                                        class="px-1.5 py-0.5 bg-yellow-200 dark:bg-yellow-800 rounded text-yellow-900 dark:text-yellow-200">Proses
                                        →</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- ── Calendar ─────────────────────────────────────── --}}
            @if ($selectedTherapist)
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}
                        </h3>

                        {{-- Day headers --}}
                        <div class="grid grid-cols-7 gap-2 mb-2">
                            @foreach (['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $dayName)
                                <div
                                    class="text-center text-xs font-bold text-gray-500 dark:text-gray-400 py-2 uppercase tracking-wide">
                                    {{ $dayName }}
                                </div>
                            @endforeach
                        </div>

                        {{-- ★ Build leave lookup: date => leave object --}}
                        @php
                            $leaveLookup = [];
                            if (isset($monthLeaves)) {
                                foreach ($monthLeaves as $leave) {
                                    $cursor = $leave->start_date->copy();
                                    while ($cursor <= $leave->end_date) {
                                        $leaveLookup[$cursor->format('Y-m-d')] = $leave;
                                        $cursor->addDay();
                                    }
                                }
                            }
                        @endphp

                        {{-- Calendar days --}}
                        <div class="grid grid-cols-7 gap-2">
                            @foreach ($calendarDays as $day)
                                @php
                                    $schedule = $day['schedule'];
                                    $status = $schedule?->status;
                                    $isEmpty = is_null($day['date']);
                                    $dateKey = $day['date'] ? $day['date']->format('Y-m-d') : null;
                                    $leave = $dateKey ? $leaveLookup[$dateKey] ?? null : null;

                                    $cellClass = match (true) {
                                        $isEmpty => 'bg-gray-50 dark:bg-gray-900/30',
                                        $leave && $leave->status === 'pending'
                                            => 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-300 dark:border-yellow-700',
                                        $leave && $leave->status === 'approved'
                                            => 'bg-orange-50 dark:bg-orange-900/20 border-orange-300 dark:border-orange-700',
                                        $status === 'working'
                                            => 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800',
                                        $status === 'working_afternoon'
                                            => 'bg-amber-50 dark:bg-amber-900/20 border-amber-300 dark:border-amber-700',
                                        $status === 'off'
                                            => 'bg-orange-50 dark:bg-orange-900/20 border-orange-200 dark:border-orange-800',
                                        $status === 'sick'
                                            => 'bg-gray-100 dark:bg-gray-700/60 border-gray-300 dark:border-gray-600',
                                        $status === 'vacation'
                                            => 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800',
                                        $status === 'cuti_bersama'
                                            => 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800',
                                        default => 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700',
                                    };
                                @endphp

                                <div
                                    class="min-h-24 border rounded-lg p-2 {{ $cellClass }} {{ !$isEmpty ? 'hover:shadow-md transition' : '' }}">
                                    @if ($day['date'])
                                        <div class="flex justify-between items-start mb-1">
                                            <span
                                                class="text-sm font-bold
                                                {{ $leave && $leave->status === 'pending'
                                                    ? 'text-yellow-700 dark:text-yellow-400'
                                                    : ($leave && $leave->status === 'approved'
                                                        ? 'text-orange-700 dark:text-orange-400'
                                                        : ($status === 'working'
                                                            ? 'text-green-700 dark:text-green-300'
                                                            : ($status === 'working_afternoon'
                                                                ? 'text-amber-700 dark:text-amber-300'
                                                                : 'text-gray-600 dark:text-gray-400'))) }}">
                                                {{ $day['date']->format('d') }}
                                            </span>
                                            @if ($schedule && !$leave)
                                                <a href="{{ route('admin.schedules.edit', $schedule) }}"
                                                    class="text-gray-400 hover:text-blue-500 text-xs"
                                                    title="Edit">✎</a>
                                            @elseif ($leave)
                                                {{-- Ikon link ke detail leave --}}
                                                <a href="{{ route('admin.leaves.show', $leave) }}"
                                                    class="{{ $leave->status === 'pending' ? 'text-yellow-500 hover:text-yellow-700' : 'text-orange-500 hover:text-orange-700' }} text-xs"
                                                    title="{{ $leave->status === 'pending' ? 'Proses pengajuan izin' : 'Lihat detail izin' }}">
                                                    {{ $leave->status === 'pending' ? '⚠' : '📋' }}
                                                </a>
                                            @endif
                                        </div>

                                        {{-- ★ Leave Badge (prioritas tampil) --}}
                                        @if ($leave)
                                            @if ($leave->status === 'pending')
                                                <span
                                                    class="inline-block px-1.5 py-0.5 bg-yellow-200 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-200 text-xs font-semibold rounded mb-1">
                                                    ⏳ Menunggu
                                                </span>
                                                <div class="text-xs text-yellow-700 dark:text-yellow-400">
                                                    @switch($leave->type)
                                                        @case('sakit')
                                                            🏥 Sakit
                                                        @break

                                                        @case('pribadi')
                                                            👤 Pribadi
                                                        @break

                                                        @case('cuti')
                                                            🏖️ Cuti
                                                        @break

                                                        @case('izin_khusus')
                                                            ⭐ Khusus
                                                        @break
                                                    @endswitch
                                                </div>
                                                <a href="{{ route('admin.leaves.show', $leave) }}"
                                                    class="mt-1 inline-block text-xs text-yellow-600 dark:text-yellow-400 hover:underline font-medium">
                                                    Proses →
                                                </a>
                                            @elseif ($leave->status === 'approved')
                                                <span
                                                    class="inline-block px-1.5 py-0.5 bg-orange-200 dark:bg-orange-800 text-orange-800 dark:text-orange-200 text-xs font-semibold rounded mb-1">
                                                    📋 Izin
                                                </span>
                                                <div class="text-xs text-orange-700 dark:text-orange-400">
                                                    @switch($leave->type)
                                                        @case('sakit')
                                                            🏥 Sakit
                                                        @break

                                                        @case('pribadi')
                                                            👤 Pribadi
                                                        @break

                                                        @case('cuti')
                                                            🏖️ Cuti
                                                        @break

                                                        @case('izin_khusus')
                                                            ⭐ Khusus
                                                        @break
                                                    @endswitch
                                                </div>
                                            @endif

                                            {{-- Schedule Badge (jika tidak ada leave) --}}
                                        @elseif ($schedule)
                                            @if ($status === 'working')
                                                <span
                                                    class="inline-block px-1.5 py-0.5 bg-green-200 dark:bg-green-800 text-green-800 dark:text-green-200 text-xs font-semibold rounded mb-1">🌅
                                                    Pagi</span>
                                                <div class="text-xs text-green-700 dark:text-green-400 font-medium">
                                                    {{ $schedule->getStartTimeFormatted() }} –
                                                    {{ $schedule->getEndTimeFormatted() }}
                                                </div>
                                                @if ($schedule->working_hours)
                                                    <div class="text-xs text-gray-500 dark:text-gray-500">
                                                        {{ $schedule->working_hours }} jam</div>
                                                @endif
                                            @elseif ($status === 'working_afternoon')
                                                <span
                                                    class="inline-block px-1.5 py-0.5 bg-amber-200 dark:bg-amber-800 text-amber-800 dark:text-amber-200 text-xs font-semibold rounded mb-1">🌤
                                                    Siang</span>
                                                <div class="text-xs text-amber-700 dark:text-amber-400 font-medium">
                                                    {{ $schedule->getStartTimeFormatted() }} –
                                                    {{ $schedule->getEndTimeFormatted() }}
                                                </div>
                                                @if ($schedule->working_hours)
                                                    <div class="text-xs text-gray-500 dark:text-gray-500">
                                                        {{ $schedule->working_hours }} jam</div>
                                                @endif
                                            @else
                                                @php
                                                    $badgeClass = match ($status) {
                                                        'off'
                                                            => 'bg-orange-200 dark:bg-orange-800 text-orange-800 dark:text-orange-200',
                                                        'sick'
                                                            => 'bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200',
                                                        'vacation'
                                                            => 'bg-blue-200 dark:bg-blue-800 text-blue-800 dark:text-blue-200',
                                                        'cuti_bersama'
                                                            => 'bg-red-200 dark:bg-red-800 text-red-800 dark:text-red-200',
                                                        default => 'bg-gray-200 text-gray-700',
                                                    };
                                                    $label = match ($status) {
                                                        'off' => 'Libur',
                                                        'sick' => 'Sakit',
                                                        'vacation' => 'Ijin',
                                                        'cuti_bersama' => 'Cuti Bersama',
                                                        default => $status,
                                                    };
                                                @endphp
                                                <span
                                                    class="inline-block px-1.5 py-0.5 {{ $badgeClass }} text-xs font-semibold rounded">{{ $label }}</span>
                                            @endif
                                        @else
                                            <div class="text-xs text-gray-400 italic mt-1">Belum ada</div>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        {{-- Summary --}}
                        <div
                            class="grid grid-cols-2 md:grid-cols-6 gap-4 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-green-600">
                                    {{ $schedules->where('status', 'working')->count() }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Kerja Pagi</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-amber-500">
                                    {{ $schedules->where('status', 'working_afternoon')->count() }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Kerja Siang</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-orange-500">
                                    {{ $schedules->where('status', 'off')->count() }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Libur</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-gray-500">
                                    {{ $schedules->where('status', 'sick')->count() }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Sakit</p>
                            </div>
                            {{-- ★ Summary leave --}}
                            <div class="text-center">
                                <p class="text-2xl font-bold text-yellow-500">
                                    {{ isset($monthLeaves) ? $monthLeaves->where('status', 'pending')->count() : 0 }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Izin Pending</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-purple-600">{{ $schedules->sum('working_hours') }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Total Jam</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center text-gray-400 dark:text-gray-500">
                        <div class="text-5xl mb-4">🗓</div>
                        <p class="text-lg">{{ __('Pilih terapis untuk melihat jadwalnya') }}</p>
                    </div>
                </div>
            @endif

            {{-- Legend --}}
            <div class="mt-4 flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-400">
                <div class="flex items-center gap-2"><span class="w-4 h-4 rounded bg-green-200 inline-block"></span>
                    Kerja Pagi</div>
                <div class="flex items-center gap-2"><span class="w-4 h-4 rounded bg-amber-200 inline-block"></span>
                    Kerja Siang</div>
                <div class="flex items-center gap-2"><span class="w-4 h-4 rounded bg-orange-200 inline-block"></span>
                    Libur/Izin Disetujui</div>
                <div class="flex items-center gap-2"><span class="w-4 h-4 rounded bg-yellow-200 inline-block"></span>
                    Izin Menunggu ⏳</div>
                <div class="flex items-center gap-2"><span class="w-4 h-4 rounded bg-gray-300 inline-block"></span>
                    Sakit</div>
                <div class="flex items-center gap-2"><span class="w-4 h-4 rounded bg-blue-200 inline-block"></span>
                    Ijin</div>
                <div class="flex items-center gap-2"><span class="w-4 h-4 rounded bg-red-200 inline-block"></span>
                    Cuti Bersama</div>
            </div>
        </div>
    </div>

    {{-- ═══════════════ MODAL Atur Jadwal ═══════════════ --}}
    <div id="generateModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
        onclick="if(event.target===this) closeGenerateModal()">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        🗓 Atur Jadwal — {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}
                    </h3>
                    <button onclick="closeGenerateModal()"
                        class="text-gray-400 hover:text-gray-600 text-xl">✕</button>
                </div>

                <form id="generateForm" action="{{ route('admin.schedules.generate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="therapist_id" value="{{ $selectedTherapist }}">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">

                    {{-- Shift --}}
                    <div class="mb-5 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">1️⃣ Shift & Jam Kerja
                            Default</p>
                        <div class="flex gap-3 mb-4">
                            @foreach ([['morning', '🌅 Pagi', 'green'], ['afternoon', '🌤 Siang', 'amber'], ['custom', '✏️ Custom', 'indigo']] as [$val, $lbl, $col])
                                <label class="cursor-pointer flex-1">
                                    <input type="radio" name="shift_type" value="{{ $val }}"
                                        {{ $val === 'morning' ? 'checked' : '' }} class="sr-only peer"
                                        onchange="applyShiftPreset(this.value)">
                                    <span
                                        class="flex items-center justify-center gap-2 px-3 py-2 rounded-lg border-2 text-sm font-medium transition
                                        peer-checked:border-{{ $col }}-500 peer-checked:bg-{{ $col }}-50 peer-checked:text-{{ $col }}-700
                                        dark:peer-checked:bg-{{ $col }}-900/30 dark:peer-checked:text-{{ $col }}-300
                                        border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:border-{{ $col }}-400">
                                        {{ $lbl }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Jam Masuk</label>
                                <input type="time" name="start_time" id="startTime" value="09:00" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Jam Keluar</label>
                                <input type="time" name="end_time" id="endTime" value="17:00" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100 text-sm">
                            </div>
                        </div>
                    </div>

                    {{-- Hari Kerja --}}
                    <div class="mb-5 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">2️⃣ Hari Kerja dalam
                            Seminggu</p>
                        <div class="flex flex-wrap gap-2">
                            @php
                                $dayLabels = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
                                $defaultWork = [1, 2, 3, 4, 5];
                            @endphp
                            @foreach ($dayLabels as $idx => $lbl)
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="working_days[]" value="{{ $idx }}"
                                        {{ in_array($idx, $defaultWork) ? 'checked' : '' }} class="sr-only peer"
                                        onchange="refreshMiniCalendar()">
                                    <span
                                        class="inline-block px-3 py-1.5 rounded-full text-sm font-medium border transition
                                        peer-checked:bg-indigo-500 peer-checked:text-white peer-checked:border-indigo-500
                                        border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:border-indigo-400">
                                        {{ $lbl }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Mini Calendar --}}
                    <div class="mb-5 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">3️⃣ Tandai Hari Libur
                            Khusus</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">Klik tanggal untuk toggle <span
                                class="font-semibold text-red-500">Libur</span></p>
                        <div class="grid grid-cols-7 gap-1 mb-1">
                            @foreach ($dayLabels as $lbl)
                                <div class="text-center text-xs font-bold text-gray-400 py-1">{{ $lbl }}
                                </div>
                            @endforeach
                        </div>
                        <div id="miniCalendar" class="grid grid-cols-7 gap-1"></div>
                        <div id="offDatesInputs"></div>
                        <p class="text-xs text-gray-400 mt-2 flex flex-wrap gap-x-3 gap-y-1">
                            <span><span class="inline-block w-3 h-3 rounded bg-red-400 mr-1"></span>Libur manual</span>
                            <span><span class="inline-block w-3 h-3 rounded bg-green-400 mr-1"></span>Kerja Pagi</span>
                            <span><span class="inline-block w-3 h-3 rounded bg-amber-400 mr-1"></span>Kerja
                                Siang</span>
                            <span><span class="inline-block w-3 h-3 rounded bg-gray-300 mr-1"></span>Libur
                                (pola)</span>
                        </p>
                    </div>

                    <div
                        class="mb-5 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                        <p class="text-xs text-amber-700 dark:text-amber-300">
                            ⚠️ Generate akan <strong>menghapus & membuat ulang</strong> semua jadwal bulan
                            {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }} untuk terapis ini.
                        </p>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeGenerateModal()"
                            class="px-4 py-2 text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-sm">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg font-semibold text-sm transition">
                            Generate Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const CAL_YEAR = {{ $year }};
        const CAL_MONTH = {{ $month }};
        let offDates = new Set();
        let currentShift = 'morning';
        const SHIFT_PRESETS = {
            morning: {
                start: '09:00',
                end: '17:00'
            },
            afternoon: {
                start: '13:00',
                end: '21:00'
            },
            custom: null
        };

        function applyShiftPreset(shift) {
            currentShift = shift;
            const preset = SHIFT_PRESETS[shift];
            if (preset) {
                document.getElementById('startTime').value = preset.start;
                document.getElementById('endTime').value = preset.end;
            }
            refreshMiniCalendar();
        }

        function openGenerateModal() {
            document.getElementById('generateModal').classList.remove('hidden');
            refreshMiniCalendar();
        }

        function closeGenerateModal() {
            document.getElementById('generateModal').classList.add('hidden');
        }

        function getWorkingDays() {
            return Array.from(document.querySelectorAll('input[name="working_days[]"]:checked')).map(el => parseInt(el
                .value));
        }

        function refreshMiniCalendar() {
            const workingDays = getWorkingDays();
            const isAfternoon = currentShift === 'afternoon';
            const container = document.getElementById('miniCalendar');
            container.innerHTML = '';
            const firstDay = new Date(CAL_YEAR, CAL_MONTH - 1, 1);
            const lastDay = new Date(CAL_YEAR, CAL_MONTH, 0);
            for (let i = 0; i < firstDay.getDay(); i++) container.appendChild(document.createElement('div'));
            for (let d = 1; d <= lastDay.getDate(); d++) {
                const date = new Date(CAL_YEAR, CAL_MONTH - 1, d);
                const dateStr = formatDate(date);
                const dow = date.getDay();
                const isWorkDay = workingDays.includes(dow);
                const isManualOff = offDates.has(dateStr);
                const isWorking = isWorkDay && !isManualOff;
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.dataset.date = dateStr;
                btn.textContent = String(d).padStart(2, '0');
                btn.className = [
                    'w-full aspect-square rounded-lg text-xs font-semibold transition focus:outline-none focus:ring-2 focus:ring-indigo-400',
                    isManualOff ? 'bg-red-400 text-white hover:bg-red-500' :
                    isWorking ? (isAfternoon ? 'bg-amber-400 text-white hover:bg-amber-500' :
                        'bg-green-400 text-white hover:bg-green-500') :
                    'bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-300'
                ].join(' ');
                btn.addEventListener('click', () => {
                    offDates.has(dateStr) ? offDates.delete(dateStr) : offDates.add(dateStr);
                    refreshMiniCalendar();
                    syncOffDateInputs();
                });
                container.appendChild(btn);
            }
            syncOffDateInputs();
        }

        function syncOffDateInputs() {
            const container = document.getElementById('offDatesInputs');
            container.innerHTML = '';
            offDates.forEach(date => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'off_dates[]';
                input.value = date;
                container.appendChild(input);
            });
        }

        function formatDate(date) {
            return `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-${String(date.getDate()).padStart(2,'0')}`;
        }
    </script>
</x-app-layout>
