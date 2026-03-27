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

            {{-- ── Filter Card ─────────────────────────────────────────── --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" class="flex flex-col md:flex-row gap-4 items-end">

                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Pilih Terapis') }}
                            </label>
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
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Bulan & Tahun') }}
                            </label>
                            <div class="flex gap-2">
                                <select name="month"
                                    class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500"
                                    onchange="this.form.submit()">
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::createFromDate($year, $m, 1)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                                <select name="year"
                                    class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500"
                                    onchange="this.form.submit()">
                                    @for ($y = now()->year - 1; $y <= now()->year + 2; $y++)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                            {{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        @if ($selectedTherapist)
                            <div class="flex gap-2">
                                <button type="button" onclick="openGenerateModal()"
                                    class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg text-sm font-semibold transition">
                                    🗓 {{ __('Atur Jadwal Bulan Ini') }}
                                </button>
                                <a href="{{ route('admin.schedules.create', ['therapist_id' => $selectedTherapist, 'month' => $month, 'year' => $year]) }}"
                                    class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-semibold transition">
                                    ➕ {{ __('Tambah Manual') }}
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            {{-- ── Calendar ─────────────────────────────────────────────── --}}
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

                        {{-- Calendar days --}}
                        <div class="grid grid-cols-7 gap-2">
                            @foreach ($calendarDays as $day)
                                @php
                                    $schedule = $day['schedule'];
                                    $status = $schedule?->status;
                                    $isEmpty = is_null($day['date']);

                                    $cellClass = match (true) {
                                        $isEmpty => 'bg-gray-50 dark:bg-gray-900/30',
                                        $status === 'working'
                                            => 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800',
                                        $status === 'off'
                                            => 'bg-gray-100 dark:bg-gray-700/60 border-gray-300 dark:border-gray-600',
                                        $status === 'sick'
                                            => 'bg-orange-50 dark:bg-orange-900/20 border-orange-200 dark:border-orange-800',
                                        $status === 'vacation'
                                            => 'bg-purple-50 dark:bg-purple-900/20 border-purple-200 dark:border-purple-800',
                                        $status === 'cuti_bersama'
                                            => 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800',
                                        default => 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700',
                                    };
                                @endphp

                                <div
                                    class="min-h-24 border rounded-lg p-2 {{ $cellClass }} {{ !$isEmpty ? 'hover:shadow-md transition' : '' }}">
                                    @if ($day['date'])
                                        <div class="flex justify-between items-start mb-1">
                                            <span
                                                class="text-sm font-bold
                                                {{ $status === 'working' ? 'text-green-700 dark:text-green-300' : 'text-gray-600 dark:text-gray-400' }}">
                                                {{ $day['date']->format('d') }}
                                            </span>
                                            @if ($schedule)
                                                <a href="{{ route('admin.schedules.edit', $schedule) }}"
                                                    class="text-gray-400 hover:text-blue-500 text-xs"
                                                    title="Edit">✎</a>
                                            @endif
                                        </div>

                                        @if ($schedule)
                                            @if ($status === 'working')
                                                <span
                                                    class="inline-block px-1.5 py-0.5 bg-green-200 dark:bg-green-800 text-green-800 dark:text-green-200 text-xs font-semibold rounded mb-1">
                                                    Kerja
                                                </span>
                                                <div class="text-xs text-green-700 dark:text-green-400 font-medium">
                                                    {{ $schedule->getStartTimeFormatted() }} –
                                                    {{ $schedule->getEndTimeFormatted() }}
                                                </div>
                                                @if ($schedule->working_hours)
                                                    <div class="text-xs text-gray-500 dark:text-gray-500">
                                                        {{ $schedule->working_hours }} jam
                                                    </div>
                                                @endif
                                            @else
                                                @php
                                                    $badgeClass = match ($status) {
                                                        'off'
                                                            => 'bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200',
                                                        'sick'
                                                            => 'bg-orange-200 dark:bg-orange-800 text-orange-800 dark:text-orange-200',
                                                        'vacation'
                                                            => 'bg-purple-200 dark:bg-purple-800 text-purple-800 dark:text-purple-200',
                                                        'cuti_bersama'
                                                            => 'bg-blue-200 dark:bg-blue-800 text-blue-800 dark:text-blue-200',
                                                        default => 'bg-gray-200 text-gray-700',
                                                    };
                                                    $label = match ($status) {
                                                        'off' => 'Libur',
                                                        'sick' => 'Sakit',
                                                        'vacation' => 'Liburan',
                                                        'cuti_bersama' => 'Cuti Bersama',
                                                        default => $status,
                                                    };
                                                @endphp
                                                <span
                                                    class="inline-block px-1.5 py-0.5 {{ $badgeClass }} text-xs font-semibold rounded">
                                                    {{ $label }}
                                                </span>
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
                            class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-green-600">
                                    {{ $schedules->where('status', 'working')->count() }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Hari Kerja</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-gray-500">
                                    {{ $schedules->where('status', 'off')->count() }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Libur</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-orange-500">
                                    {{ $schedules->where('status', 'sick')->count() }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Sakit</p>
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
                    Kerja</div>
                <div class="flex items-center gap-2"><span class="w-4 h-4 rounded bg-gray-300 inline-block"></span>
                    Libur</div>
                <div class="flex items-center gap-2"><span class="w-4 h-4 rounded bg-orange-200 inline-block"></span>
                    Sakit</div>
                <div class="flex items-center gap-2"><span class="w-4 h-4 rounded bg-purple-200 inline-block"></span>
                    Liburan</div>
                <div class="flex items-center gap-2"><span class="w-4 h-4 rounded bg-blue-200 inline-block"></span> Cuti
                    Bersama</div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         MODAL: Atur Jadwal Bulan Ini
         Cara kerja:
         - Tentukan jam kerja (start & end)
         - Pilih pola hari kerja (default Senin-Jumat)
         - Semua hari lain otomatis Libur
         - Klik tanggal di mini-kalender untuk override jadi Libur manual
    ════════════════════════════════════════════════════════════════════ --}}
    <div id="generateModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
        onclick="if(event.target===this) closeGenerateModal()">

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">

                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        🗓 Atur Jadwal —
                        {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}
                    </h3>
                    <button onclick="closeGenerateModal()" class="text-gray-400 hover:text-gray-600 text-xl">✕</button>
                </div>

                <form id="generateForm" action="{{ route('admin.schedules.generate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="therapist_id" value="{{ $selectedTherapist }}">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    {{-- off_dates akan di-inject oleh JS dari klik kalender --}}

                    {{-- Step 1: Jam Kerja --}}
                    <div class="mb-5 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                            1️⃣ Jam Kerja Default
                        </p>
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

                    {{-- Step 2: Pola Hari Kerja --}}
                    <div class="mb-5 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                            2️⃣ Hari Kerja dalam Seminggu
                        </p>
                        <div class="flex flex-wrap gap-2">
                            @php
                                $dayLabels = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
                                $defaultWork = [1, 2, 3, 4, 5]; // Senin-Jumat
                            @endphp
                            @foreach ($dayLabels as $idx => $lbl)
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="working_days[]" value="{{ $idx }}"
                                        {{ in_array($idx, $defaultWork) ? 'checked' : '' }} class="sr-only peer"
                                        onchange="refreshMiniCalendar()">
                                    <span
                                        class="inline-block px-3 py-1.5 rounded-full text-sm font-medium border transition
                                        peer-checked:bg-indigo-500 peer-checked:text-white peer-checked:border-indigo-500
                                        border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400
                                        hover:border-indigo-400">
                                        {{ $lbl }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Step 3: Mini-kalender – klik tanggal untuk tandai libur --}}
                    <div class="mb-5 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            3️⃣ Tandai Hari Libur Khusus
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">
                            Klik tanggal untuk toggle <span class="font-semibold text-red-500">Libur</span> — hari yang
                            tidak diklik mengikuti pola kerja di atas.
                        </p>

                        {{-- Mini calendar header --}}
                        <div class="grid grid-cols-7 gap-1 mb-1">
                            @foreach ($dayLabels as $lbl)
                                <div class="text-center text-xs font-bold text-gray-400 py-1">{{ $lbl }}
                                </div>
                            @endforeach
                        </div>

                        {{-- Mini calendar grid (diisi JS) --}}
                        <div id="miniCalendar" class="grid grid-cols-7 gap-1"></div>

                        {{-- Hidden inputs untuk off_dates --}}
                        <div id="offDatesInputs"></div>

                        <p class="text-xs text-gray-400 mt-2">
                            <span class="inline-block w-3 h-3 rounded bg-red-400 mr-1"></span> = Libur manual
                            <span class="inline-block w-3 h-3 rounded bg-green-400 mx-1 ml-3"></span> = Kerja
                            <span class="inline-block w-3 h-3 rounded bg-gray-300 mx-1 ml-3"></span> = Libur (pola)
                        </p>
                    </div>

                    {{-- Warning --}}
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
        // ── Config dari PHP ──────────────────────────────────────────────
        const CAL_YEAR = {{ $year }};
        const CAL_MONTH = {{ $month }}; // 1-12
        // ────────────────────────────────────────────────────────────────

        let offDates = new Set(); // Set of 'YYYY-MM-DD' string

        function openGenerateModal() {
            document.getElementById('generateModal').classList.remove('hidden');
            refreshMiniCalendar();
        }

        function closeGenerateModal() {
            document.getElementById('generateModal').classList.add('hidden');
        }

        function getWorkingDays() {
            return Array.from(
                document.querySelectorAll('input[name="working_days[]"]:checked')
            ).map(el => parseInt(el.value));
        }

        function refreshMiniCalendar() {
            const workingDays = getWorkingDays();
            const container = document.getElementById('miniCalendar');
            container.innerHTML = '';

            const firstDay = new Date(CAL_YEAR, CAL_MONTH - 1, 1);
            const lastDay = new Date(CAL_YEAR, CAL_MONTH, 0);
            const startDow = firstDay.getDay(); // 0=Sun

            // Empty cells before first day
            for (let i = 0; i < startDow; i++) {
                const blank = document.createElement('div');
                container.appendChild(blank);
            }

            // Days
            for (let d = 1; d <= lastDay.getDate(); d++) {
                const date = new Date(CAL_YEAR, CAL_MONTH - 1, d);
                const dateStr = formatDate(date);
                const dow = date.getDay();

                const isWorkDay = workingDays.includes(dow);
                const isManualOff = offDates.has(dateStr);

                // Final status
                const isWorking = isWorkDay && !isManualOff;

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.dataset.date = dateStr;
                btn.textContent = String(d).padStart(2, '0');

                btn.className = [
                    'w-full aspect-square rounded-lg text-xs font-semibold transition',
                    'focus:outline-none focus:ring-2 focus:ring-indigo-400',
                    isManualOff ?
                    'bg-red-400 text-white hover:bg-red-500' // libur manual
                    :
                    isWorking ?
                    'bg-green-400 text-white hover:bg-green-500' // kerja
                    :
                    'bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-300', // libur pola
                ].join(' ');

                btn.title = isManualOff ?
                    'Libur Manual — klik untuk batalkan' :
                    isWorking ?
                    'Kerja — klik untuk jadikan libur' :
                    'Libur (pola) — klik untuk jadikan libur manual';

                btn.addEventListener('click', () => {
                    if (offDates.has(dateStr)) {
                        offDates.delete(dateStr);
                    } else {
                        offDates.add(dateStr);
                    }
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
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        }
    </script>
</x-app-layout>
