<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Booking</h2>
            <a href="{{ route('admin.bookings.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                + Booking Baru
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-emerald-400 rounded-lg text-sm">
                    ✓ {{ session('success') }}
                </div>
            @endif

            {{-- Stats --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700 text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $bookings->count() }}</div>
                    <div class="text-xs text-gray-500 mt-1">Total</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700 text-center">
                    <div class="text-2xl font-bold text-amber-500">{{ $bookings->where('status','scheduled')->count() }}</div>
                    <div class="text-xs text-gray-500 mt-1">Terjadwal</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700 text-center">
                    <div class="text-2xl font-bold text-emerald-500">{{ $bookings->where('status','completed')->count() }}</div>
                    <div class="text-xs text-gray-500 mt-1">Selesai</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700 text-center">
                    <div class="text-2xl font-bold text-red-500">{{ $bookings->where('status','cancelled')->count() }}</div>
                    <div class="text-xs text-gray-500 mt-1">Batal</div>
                </div>
            </div>

            {{-- ═══════════════ KALENDER ═══════════════ --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 mb-6 overflow-hidden">
                {{-- Kalender Header --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200">📅 Kalender Jadwal</h3>
                        {{-- Legend --}}
                        <div class="hidden sm:flex items-center gap-3 text-xs text-gray-400">
                            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span>Terjadwal</span>
                            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-400 inline-block"></span>Berlangsung</span>
                            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-400 inline-block"></span>Selesai</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button id="prevDay" class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-sm transition-colors">‹</button>
                        <div class="text-center min-w-[160px]">
                            <div id="displayDate" class="text-sm font-semibold text-gray-700 dark:text-gray-200"></div>
                            <input type="date" id="datePicker" class="text-xs text-indigo-500 border-0 bg-transparent cursor-pointer focus:outline-none w-full text-center">
                        </div>
                        <button id="nextDay" class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-sm transition-colors">›</button>
                        <button id="todayBtn" class="px-3 py-1.5 text-xs font-medium bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg transition-colors ml-1">Hari Ini</button>
                    </div>
                </div>

                {{-- Grid --}}
                <div id="calendarGrid" class="overflow-x-auto">
                    <div class="flex items-center justify-center py-12 text-gray-400 text-sm gap-2">
                        <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        Memuat kalender...
                    </div>
                </div>
            </div>
            {{-- ═══════════════ END KALENDER ═══════════════ --}}

            {{-- Tabel Semua Booking --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">Semua Booking</h3>
                </div>
                @if($bookings->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700/50">
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pelanggan</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Terapis</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Layanan</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Jadwal</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($bookings as $i => $booking)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-5 py-3.5 text-gray-400">{{ $i + 1 }}</td>
                                <td class="px-5 py-3.5 font-medium text-gray-800 dark:text-gray-200">{{ $booking->customer->name ?? '—' }}</td>
                                <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">{{ $booking->therapist->name ?? '—' }}</td>
                                <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">{{ $booking->service->name ?? '—' }}</td>
                                <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($booking->scheduled_at)->format('d M Y, H:i') }}
                                </td>
                                <td class="px-5 py-3.5 font-semibold text-amber-600 dark:text-amber-400">
                                    Rp {{ number_format($booking->final_price, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-3.5">
                                    @php
                                        $cls = match($booking->status) {
                                            'scheduled' => 'bg-amber-100 text-amber-700',
                                            'completed' => 'bg-emerald-100 text-emerald-700',
                                            'cancelled' => 'bg-red-100 text-red-700',
                                            'ongoing'   => 'bg-blue-100 text-blue-700',
                                            default     => 'bg-gray-100 text-gray-500',
                                        };
                                        $lbl = match($booking->status) {
                                            'scheduled' => 'Terjadwal',
                                            'completed' => 'Selesai',
                                            'cancelled' => 'Batal',
                                            'ongoing'   => 'Berlangsung',
                                            default     => $booking->status
                                        };
                                    @endphp
                                    <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold {{ $cls }}">{{ $lbl }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.bookings.edit', $booking) }}" class="px-3 py-1 bg-amber-50 hover:bg-amber-100 text-amber-600 text-xs font-medium rounded-lg transition-colors">Edit</a>
                                        <form method="POST" action="{{ route('admin.bookings.destroy', $booking) }}" onsubmit="return confirm('Hapus booking ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="px-3 py-1 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-medium rounded-lg transition-colors">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-16 text-gray-400 text-sm">
                    Belum ada booking. <a href="{{ route('admin.bookings.create') }}" class="text-indigo-500 hover:underline">Buat sekarang</a>
                </div>
                @endif
            </div>

        </div>
    </div>

    {{-- ═══════════ MODAL BOOKING ═══════════ --}}
    <div id="bookingModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" id="modalBackdrop"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg relative">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-white" id="modalTitle">Buat Booking</h3>
                    <button id="closeModal" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
                </div>
                <form method="POST" action="{{ route('admin.bookings.store') }}" class="px-6 py-5">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Pelanggan *</label>
                            <select name="customer_id" required class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                <option value="">-- Pilih --</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Terapis *</label>
                            <select name="therapist_id" id="modalTherapist" required class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                <option value="">-- Pilih --</option>
                                @foreach($therapists as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Layanan *</label>
                            <select name="service_id" id="modalService" required class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                <option value="">-- Pilih --</option>
                                @foreach($services as $s)
                                    <option value="{{ $s->id }}" data-price="{{ $s->price }}">
                                        {{ $s->name }} — Rp {{ number_format($s->price,0,',','.') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Sumber Order</label>
                            <select name="order_source" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                <option value="walkin">Walk-in</option>
                                <option value="wa">WhatsApp</option>
                                <option value="web">Online/App</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Tanggal & Waktu</label>
                            <input type="datetime-local" name="scheduled_at" id="modalDatetime" required
                                class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Diskon (Rp)</label>
                            <input type="number" name="discount" id="modalDiscount" value="0" min="0"
                                class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200"
                                oninput="calcModalTotal()">
                        </div>
                        {{-- Ringkasan harga --}}
                        <div class="sm:col-span-2 px-3 py-2.5 bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800 rounded-lg flex gap-6 items-center text-sm">
                            <div><div class="text-xs text-gray-400 mb-0.5">Harga</div><div id="mDisplayPrice" class="font-bold text-gray-700 dark:text-gray-200">Rp 0</div></div>
                            <div class="text-gray-300">−</div>
                            <div><div class="text-xs text-gray-400 mb-0.5">Diskon</div><div id="mDisplayDiscount" class="font-bold text-red-500">Rp 0</div></div>
                            <div class="text-gray-300">=</div>
                            <div><div class="text-xs text-gray-400 mb-0.5">Total</div><div id="mDisplayTotal" class="font-bold text-amber-600 text-base">Rp 0</div></div>
                        </div>
                    </div>
                    <div class="flex gap-3 mt-5">
                        <button type="submit" class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                            Buat Booking
                        </button>
                        <button type="button" id="cancelModal" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // ── Data dari Blade ──────────────────────────────────────────
    const THERAPISTS = @json($therapists);
    const HOURS      = Array.from({length: 13}, (_, i) => i + 10); // 10..22

    // ── State ────────────────────────────────────────────────────
    let currentDate = new Date();
    currentDate.setHours(0,0,0,0);

    // ── Helpers ──────────────────────────────────────────────────
    function toLocalISO(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth()+1).padStart(2,'0');
        const d = String(date.getDate()).padStart(2,'0');
        return `${y}-${m}-${d}`;
    }

    function formatDisplayDate(date) {
        return date.toLocaleDateString('id-ID', {
            weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
        });
    }

    function setDate(date) {
        currentDate = date;
        document.getElementById('datePicker').value = toLocalISO(date);
        document.getElementById('displayDate').textContent  = formatDisplayDate(date);
        loadCalendar(toLocalISO(date));
    }

    // ── Navigasi ─────────────────────────────────────────────────
    document.getElementById('prevDay').addEventListener('click', () => {
        const d = new Date(currentDate); d.setDate(d.getDate()-1); setDate(d);
    });
    document.getElementById('nextDay').addEventListener('click', () => {
        const d = new Date(currentDate); d.setDate(d.getDate()+1); setDate(d);
    });
    document.getElementById('todayBtn').addEventListener('click', () => {
        const d = new Date(); d.setHours(0,0,0,0); setDate(d);
    });
    document.getElementById('datePicker').addEventListener('change', function() {
        const d = new Date(this.value + 'T00:00:00'); setDate(d);
    });

    // ── Load data dari server ────────────────────────────────────
    async function loadCalendar(dateStr) {
        document.getElementById('calendarGrid').innerHTML = `
            <div class="flex items-center justify-center py-12 text-gray-400 text-sm gap-2">
                <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg> Memuat...
            </div>`;

        try {
            const res  = await fetch(`{{ route('admin.bookings.calendar-data') }}?date=${dateStr}`);
            const data = await res.json();
            renderGrid(dateStr, data.bookings);
        } catch(e) {
            document.getElementById('calendarGrid').innerHTML =
                `<div class="text-center py-10 text-red-400 text-sm">Gagal memuat data kalender.</div>`;
        }
    }

    // ── Render grid ──────────────────────────────────────────────
    function renderGrid(dateStr, bookings) {
        // Build map: map[therapist_id][hour] = booking
        const map = {};
        THERAPISTS.forEach(t => { map[t.id] = {}; });
        bookings.forEach(b => {
            if (map[b.therapist_id]) map[b.therapist_id][b.hour] = b;
        });

        const now         = new Date();
        const todayStr    = toLocalISO(now);
        const currentHour = now.getHours();

        // Hitung ringkasan per terapis untuk header
        const summary = {};
        THERAPISTS.forEach(t => {
            const booked = bookings.filter(b => b.therapist_id == t.id && ['scheduled','ongoing'].includes(b.status));
            summary[t.id] = { booked: booked.length, free: HOURS.length - booked.length };
        });

        let html = `<table class="w-full text-sm border-collapse" style="min-width:${THERAPISTS.length * 140 + 80}px">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700/60 sticky top-0 z-10">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider w-20 border-b border-r border-gray-100 dark:border-gray-700">Jam</th>`;

        THERAPISTS.forEach(t => {
            const s = summary[t.id];
            html += `<th class="px-3 py-3 text-center border-b border-r border-gray-100 dark:border-gray-700 last:border-r-0">
                <div class="text-xs font-semibold text-gray-700 dark:text-gray-200">${t.name}</div>
                <div class="flex items-center justify-center gap-1.5 mt-1">
                    ${s.booked > 0
                        ? `<span class="text-xs px-1.5 py-0.5 bg-amber-100 text-amber-600 rounded-full">${s.booked} booking</span>`
                        : `<span class="text-xs px-1.5 py-0.5 bg-emerald-100 text-emerald-600 rounded-full">✓ Bebas</span>`}
                </div>
            </th>`;
        });
        html += `</tr></thead><tbody>`;

        HOURS.forEach(hour => {
            const timeLabel = `${String(hour).padStart(2,'0')}:00`;
            const timeEnd   = `${String(hour+1).padStart(2,'0')}:00`;
            const isPast    = dateStr === todayStr && hour < currentHour;
            const isNow     = dateStr === todayStr && hour === currentHour;

            const rowBg = isNow
                ? 'bg-indigo-50/50 dark:bg-indigo-900/10'
                : (isPast ? 'opacity-40' : '');

            html += `<tr class="border-b border-gray-100 dark:border-gray-700 ${rowBg} hover:bg-gray-50/80 dark:hover:bg-gray-700/20 transition-colors">
                <td class="px-4 py-2 border-r border-gray-100 dark:border-gray-700 whitespace-nowrap">
                    <div class="font-mono text-xs font-semibold ${isNow ? 'text-indigo-600' : 'text-gray-400'}">${timeLabel}</div>
                    <div class="font-mono text-[10px] text-gray-300">${timeEnd}</div>
                </td>`;

            THERAPISTS.forEach(t => {
                const booking = map[t.id][hour];

                if (booking) {
                    const cfg = {
                        scheduled: { bg:'bg-amber-50 dark:bg-amber-900/20', border:'border-amber-200 dark:border-amber-700', text:'text-amber-700 dark:text-amber-300', dot:'bg-amber-400', label:'Terjadwal' },
                        ongoing:   { bg:'bg-blue-50 dark:bg-blue-900/20',   border:'border-blue-200 dark:border-blue-700',   text:'text-blue-700 dark:text-blue-300',   dot:'bg-blue-400',   label:'Berlangsung' },
                        completed: { bg:'bg-emerald-50 dark:bg-emerald-900/20', border:'border-emerald-200 dark:border-emerald-700', text:'text-emerald-700 dark:text-emerald-300', dot:'bg-emerald-400', label:'Selesai' },
                        cancelled: { bg:'bg-gray-50 dark:bg-gray-700/40',   border:'border-gray-200 dark:border-gray-600',   text:'text-gray-400',   dot:'bg-gray-300',   label:'Batal' },
                    }[booking.status] ?? { bg:'bg-gray-50', border:'border-gray-200', text:'text-gray-500', dot:'bg-gray-300', label:booking.status };

                    html += `<td class="px-2 py-1.5 border-r border-gray-100 dark:border-gray-700 last:border-r-0">
                        <a href="${booking.edit_url}" class="block rounded-lg border px-2.5 py-1.5 ${cfg.bg} ${cfg.border} hover:shadow-sm transition-shadow">
                            <div class="flex items-center gap-1 mb-0.5">
                                <span class="w-1.5 h-1.5 rounded-full ${cfg.dot} flex-shrink-0"></span>
                                <span class="text-[10px] font-semibold ${cfg.text}">${cfg.label}</span>
                            </div>
                            <div class="text-xs font-medium text-gray-700 dark:text-gray-200 truncate">${booking.customer_name}</div>
                            <div class="text-[10px] text-gray-400 truncate">${booking.service_name}</div>
                        </a>
                    </td>`;
                } else {
                    if (isPast) {
                        html += `<td class="px-2 py-1.5 border-r border-gray-100 dark:border-gray-700 last:border-r-0">
                            <div class="h-[52px] rounded-lg bg-gray-50 dark:bg-gray-700/20"></div>
                        </td>`;
                    } else {
                        html += `<td class="px-2 py-1.5 border-r border-gray-100 dark:border-gray-700 last:border-r-0">
                            <button type="button"
                                onclick="openModal('${dateStr}', ${hour}, ${t.id}, '${t.name.replace(/'/g,"\\'")}' )"
                                class="w-full h-[52px] rounded-lg border border-dashed border-gray-200 dark:border-gray-600 hover:border-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 text-gray-300 hover:text-indigo-400 transition-all text-xs font-medium group flex items-center justify-center">
                                <span class="opacity-0 group-hover:opacity-100 transition-opacity select-none">+ Booking</span>
                            </button>
                        </td>`;
                    }
                }
            });

            html += `</tr>`;
        });

        html += `</tbody></table>`;
        document.getElementById('calendarGrid').innerHTML = html;
    }

    // ── Modal ────────────────────────────────────────────────────
    let modalServicePrice = 0;

    function openModal(dateStr, hour, therapistId, therapistName) {
        document.getElementById('modalDatetime').value = `${dateStr}T${String(hour).padStart(2,'0')}:00`;
        document.getElementById('modalTitle').textContent = `Booking — ${therapistName}, ${String(hour).padStart(2,'0')}:00`;
        document.getElementById('modalDiscount').value = 0;
        document.getElementById('modalService').selectedIndex = 0;
        modalServicePrice = 0;
        calcModalTotal();

        // Pre-select terapis
        const sel = document.getElementById('modalTherapist');
        [...sel.options].forEach(o => { o.selected = o.value == therapistId; });

        document.getElementById('bookingModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('bookingModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    document.getElementById('closeModal').addEventListener('click', closeModal);
    document.getElementById('cancelModal').addEventListener('click', closeModal);
    document.getElementById('modalBackdrop').addEventListener('click', closeModal);

    document.getElementById('modalService').addEventListener('change', function() {
        modalServicePrice = parseInt(this.options[this.selectedIndex].getAttribute('data-price')) || 0;
        calcModalTotal();
    });

    function calcModalTotal() {
        const disc  = parseInt(document.getElementById('modalDiscount').value) || 0;
        const total = Math.max(0, modalServicePrice - disc);
        const fmt   = n => 'Rp ' + n.toLocaleString('id-ID');
        document.getElementById('mDisplayPrice').textContent    = fmt(modalServicePrice);
        document.getElementById('mDisplayDiscount').textContent = fmt(disc);
        document.getElementById('mDisplayTotal').textContent    = fmt(total);
    }

    // ── Init ─────────────────────────────────────────────────────
    setDate(currentDate);
    </script>
</x-app-layout>
