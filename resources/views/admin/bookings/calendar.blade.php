<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Kalender Booking</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.bookings.index') }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg">
                    ☰ Semua Booking
                </a>
                <a href="{{ route('admin.bookings.create') }}"
                   class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                    + Booking Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Date navigator --}}
            <div class="flex items-center justify-between mb-6">
                <button id="prevDay"
                    class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    ← Sebelumnya
                </button>
                <div class="text-center">
                    <div id="displayDate" class="text-lg font-bold text-gray-800 dark:text-white"></div>
                    <input type="date" id="datePicker"
                        class="mt-1 text-sm text-indigo-600 border-0 bg-transparent cursor-pointer focus:outline-none">
                </div>
                <button id="nextDay"
                    class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Berikutnya →
                </button>
            </div>

            {{-- Legend --}}
            <div class="flex items-center gap-4 mb-4 text-xs text-gray-500">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-gray-100 border border-gray-300 inline-block"></span> Tersedia</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span> Terjadwal</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-blue-400 inline-block"></span> Berlangsung</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-400 inline-block"></span> Selesai</span>
            </div>

            {{-- Calendar grid --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div id="calendarGrid" class="overflow-x-auto">
                    <div class="flex items-center justify-center py-16 text-gray-400">
                        <svg class="animate-spin w-6 h-6 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        Memuat kalender...
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Booking --}}
    <div id="bookingModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" id="modalBackdrop"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg relative">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-white" id="modalTitle">Buat Booking</h3>
                    <button id="closeModal" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>
                <form method="POST" action="{{ route('admin.bookings.store') }}" class="px-6 py-5">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Pelanggan *</label>
                            <select name="customer_id" required
                                class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                <option value="">-- Pilih --</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Terapis *</label>
                            <select name="therapist_id" id="modalTherapist" required
                                class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                <option value="">-- Pilih --</option>
                                @foreach($therapists as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Layanan *</label>
                            <select name="service_id" id="modalService" required
                                class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                <option value="">-- Pilih --</option>
                                @foreach($services as $s)
                                    <option value="{{ $s->id }}" data-price="{{ $s->price }}">
                                        {{ $s->name }} — Rp {{ number_format($s->price, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Sumber Order</label>
                            <select name="order_source"
                                class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
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
                        <div class="sm:col-span-2 px-3 py-2.5 bg-gray-50 dark:bg-gray-700 rounded-lg flex gap-6 items-center text-sm">
                            <div><div class="text-xs text-gray-400 mb-0.5">Harga</div><div id="mDisplayPrice" class="font-bold text-gray-700 dark:text-gray-200">Rp 0</div></div>
                            <div class="text-gray-400">−</div>
                            <div><div class="text-xs text-gray-400 mb-0.5">Diskon</div><div id="mDisplayDiscount" class="font-bold text-red-500">Rp 0</div></div>
                            <div class="text-gray-400">=</div>
                            <div><div class="text-xs text-gray-400 mb-0.5">Total</div><div id="mDisplayTotal" class="font-bold text-amber-600 text-base">Rp 0</div></div>
                        </div>
                    </div>
                    <div class="flex gap-3 mt-5">
                        <button type="submit"
                            class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                            Buat Booking
                        </button>
                        <button type="button" id="cancelModal"
                            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    const THERAPISTS = @json($therapists);
    const HOURS = Array.from({length: 13}, (_, i) => i + 10); // 10..22

    let currentDate = new Date();
    currentDate.setHours(0,0,0,0);

    const datePicker = document.getElementById('datePicker');
    const displayDate = document.getElementById('displayDate');

    function toLocalISO(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth()+1).padStart(2,'0');
        const d = String(date.getDate()).padStart(2,'0');
        return `${y}-${m}-${d}`;
    }

    function formatDisplayDate(date) {
        return date.toLocaleDateString('id-ID', {weekday:'long', day:'numeric', month:'long', year:'numeric'});
    }

    function setDate(date) {
        currentDate = date;
        datePicker.value = toLocalISO(date);
        displayDate.textContent = formatDisplayDate(date);
        loadCalendar(toLocalISO(date));
    }

    document.getElementById('prevDay').addEventListener('click', () => {
        const d = new Date(currentDate); d.setDate(d.getDate()-1); setDate(d);
    });
    document.getElementById('nextDay').addEventListener('click', () => {
        const d = new Date(currentDate); d.setDate(d.getDate()+1); setDate(d);
    });
    datePicker.addEventListener('change', () => {
        const d = new Date(datePicker.value + 'T00:00:00'); setDate(d);
    });

    async function loadCalendar(dateStr) {
        document.getElementById('calendarGrid').innerHTML = `
            <div class="flex items-center justify-center py-16 text-gray-400">
                <svg class="animate-spin w-6 h-6 mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg> Memuat...
            </div>`;

        const res = await fetch(`{{ route('admin.bookings.calendar-data') }}?date=${dateStr}`);
        const data = await res.json(); // { bookings: [{therapist_id, hour, status, customer_name, service_name}] }
        renderGrid(dateStr, data.bookings);
    }

    function renderGrid(dateStr, bookings) {
        // Build lookup: bookingMap[therapist_id][hour] = booking
        const map = {};
        THERAPISTS.forEach(t => { map[t.id] = {}; });
        bookings.forEach(b => {
            if (map[b.therapist_id]) map[b.therapist_id][b.hour] = b;
        });

        const now = new Date();
        const todayStr = toLocalISO(now);
        const currentHour = now.getHours();

        let html = `<table class="w-full text-sm border-collapse min-w-[600px]">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700/60">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-20 border-b border-gray-100 dark:border-gray-700">Jam</th>`;
        THERAPISTS.forEach(t => {
            html += `<th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                        <div>${t.name}</div>
                     </th>`;
        });
        html += `</tr></thead><tbody>`;

        HOURS.forEach(hour => {
            const timeLabel = `${String(hour).padStart(2,'0')}:00`;
            const nextLabel = `${String(hour+1).padStart(2,'0')}:00`;
            const isPast = dateStr === todayStr && hour < currentHour;

            html += `<tr class="border-b border-gray-50 dark:border-gray-700/50 ${isPast ? 'opacity-50' : 'hover:bg-gray-50 dark:hover:bg-gray-700/30'} transition-colors">
                <td class="px-4 py-2.5 text-gray-400 font-mono text-xs whitespace-nowrap">
                    ${timeLabel}<span class="text-gray-300">–${nextLabel}</span>
                </td>`;

            THERAPISTS.forEach(t => {
                const booking = map[t.id][hour];
                if (booking) {
                    const cfg = {
                        scheduled: {bg:'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-700', text:'text-amber-700 dark:text-amber-300', dot:'bg-amber-400', label:'Terjadwal'},
                        ongoing:   {bg:'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-700',   text:'text-blue-700 dark:text-blue-300',   dot:'bg-blue-400',   label:'Berlangsung'},
                        completed: {bg:'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-700', text:'text-emerald-700 dark:text-emerald-300', dot:'bg-emerald-400', label:'Selesai'},
                        cancelled: {bg:'bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600',   text:'text-gray-400',   dot:'bg-gray-300',   label:'Batal'},
                    }[booking.status] || {bg:'bg-gray-50 border-gray-200', text:'text-gray-500', dot:'bg-gray-300', label:booking.status};

                    html += `<td class="px-2 py-1.5">
                        <div class="rounded-lg border px-2.5 py-1.5 ${cfg.bg} cursor-pointer" title="${booking.customer_name} – ${booking.service_name}">
                            <div class="flex items-center gap-1.5 mb-0.5">
                                <span class="w-2 h-2 rounded-full ${cfg.dot} flex-shrink-0"></span>
                                <span class="text-xs font-semibold ${cfg.text}">${cfg.label}</span>
                            </div>
                            <div class="text-xs text-gray-600 dark:text-gray-300 truncate max-w-[120px]">${booking.customer_name}</div>
                            <div class="text-xs text-gray-400 truncate max-w-[120px]">${booking.service_name}</div>
                        </div>
                    </td>`;
                } else {
                    if (isPast) {
                        html += `<td class="px-2 py-1.5"><div class="h-12 rounded-lg bg-gray-50 dark:bg-gray-700/30 border border-dashed border-gray-200 dark:border-gray-600"></div></td>`;
                    } else {
                        html += `<td class="px-2 py-1.5">
                            <button type="button"
                                onclick="openModal('${dateStr}', ${hour}, ${t.id}, '${t.name.replace(/'/g,"\\'")}' )"
                                class="w-full h-12 rounded-lg border border-dashed border-gray-200 dark:border-gray-600 hover:border-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 text-gray-300 hover:text-indigo-500 transition-all text-xs font-medium group">
                                <span class="group-hover:opacity-100 opacity-0 transition-opacity">+ Booking</span>
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

    // Modal
    let modalServicePrice = 0;

    function openModal(dateStr, hour, therapistId, therapistName) {
        const datetimeVal = `${dateStr}T${String(hour).padStart(2,'0')}:00`;
        document.getElementById('modalDatetime').value = datetimeVal;
        document.getElementById('modalTitle').textContent = `Booking – ${therapistName} ${String(hour).padStart(2,'0')}:00`;
        document.getElementById('modalDiscount').value = 0;
        modalServicePrice = 0;
        calcModalTotal();

        // Pre-select therapist
        const sel = document.getElementById('modalTherapist');
        for (let i = 0; i < sel.options.length; i++) {
            if (sel.options[i].value == therapistId) { sel.selectedIndex = i; break; }
        }

        document.getElementById('bookingModal').classList.remove('hidden');
    }

    document.getElementById('closeModal').addEventListener('click', closeModal);
    document.getElementById('cancelModal').addEventListener('click', closeModal);
    document.getElementById('modalBackdrop').addEventListener('click', closeModal);

    function closeModal() {
        document.getElementById('bookingModal').classList.add('hidden');
    }

    document.getElementById('modalService').addEventListener('change', function() {
        modalServicePrice = parseInt(this.options[this.selectedIndex].getAttribute('data-price')) || 0;
        calcModalTotal();
    });

    function calcModalTotal() {
        const disc = parseInt(document.getElementById('modalDiscount').value) || 0;
        const total = Math.max(0, modalServicePrice - disc);
        const fmt = n => 'Rp ' + n.toLocaleString('id-ID');
        document.getElementById('mDisplayPrice').textContent = fmt(modalServicePrice);
        document.getElementById('mDisplayDiscount').textContent = fmt(disc);
        document.getElementById('mDisplayTotal').textContent = fmt(total);
    }

    // Init
    setDate(currentDate);
    </script>
</x-app-layout>
