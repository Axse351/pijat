<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Booking</h2>
            <a href="{{ route('admin.bookings.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                + Booking Baru
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div
                    class="mb-4 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-emerald-400 rounded-lg text-sm">
                    ✓ {{ session('success') }}
                </div>
            @endif

            {{-- Stats --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                @foreach ([['Total', $bookings->count(), 'text-gray-900 dark:text-white'], ['Terjadwal', $bookings->where('status', 'scheduled')->count(), 'text-amber-500'], ['Selesai', $bookings->where('status', 'completed')->count(), 'text-emerald-500'], ['Batal', $bookings->where('status', 'cancelled')->count(), 'text-red-500']] as [$lbl, $cnt, $cls])
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700 text-center">
                        <div class="text-2xl font-bold {{ $cls }}">{{ $cnt }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ $lbl }}</div>
                    </div>
                @endforeach
            </div>

            {{-- Kalender --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 mb-6 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200">📅 Kalender Jadwal</h3>
                        <div class="hidden sm:flex items-center gap-3 text-xs text-gray-400">
                            <span class="flex items-center gap-1"><span
                                    class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span>Terjadwal</span>
                            <span class="flex items-center gap-1"><span
                                    class="w-2 h-2 rounded-full bg-blue-400 inline-block"></span>Berlangsung</span>
                            <span class="flex items-center gap-1"><span
                                    class="w-2 h-2 rounded-full bg-emerald-400 inline-block"></span>Selesai</span>
                            <span class="flex items-center gap-1"><span
                                    class="w-2 h-2 rounded-full bg-purple-400 inline-block"></span>Dijadwal Ulang</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button id="prevDay"
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-sm transition-colors">‹</button>
                        <div class="text-center min-w-[160px]">
                            <div id="displayDate" class="text-sm font-semibold text-gray-700 dark:text-gray-200"></div>
                            <input type="date" id="datePicker"
                                class="text-xs text-indigo-500 border-0 bg-transparent cursor-pointer focus:outline-none w-full text-center">
                        </div>
                        <button id="nextDay"
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-sm transition-colors">›</button>
                        <button id="todayBtn"
                            class="px-3 py-1.5 text-xs font-medium bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg transition-colors ml-1">Hari
                            Ini</button>
                    </div>
                </div>
                <div id="calendarGrid" class="overflow-x-auto">
                    <div class="flex items-center justify-center py-12 text-gray-400 text-sm gap-2">
                        <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        Memuat kalender...
                    </div>
                </div>
            </div>

            {{-- Tabel --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div
                    class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between flex-wrap gap-2">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">Semua Booking</h3>
                    <div class="flex items-center gap-3 text-xs text-gray-400 flex-wrap">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-green-400 inline-block"></span>WA muncul H-0 & H-1
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-purple-400 inline-block"></span>Dijadwal Ulang
                        </span>
                        <a href="{{ route('admin.wa-templates.index') }}"
                            class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-50 hover:bg-green-100 text-green-600 border border-green-200 rounded-lg font-medium transition-colors">
                            📱 Kelola Template WA
                        </a>
                    </div>
                </div>

                @if ($bookings->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    @foreach (['#', 'Pelanggan', 'Terapis', 'Layanan', 'Jadwal', 'Total', 'Status', 'Aksi'] as $th)
                                        <th
                                            class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            {{ $th }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($bookings as $i => $booking)
                                    @php
                                        $scheduledDate = \Carbon\Carbon::parse($booking->scheduled_at);
                                        $originalDate = $booking->original_scheduled_at
                                            ? \Carbon\Carbon::parse($booking->original_scheduled_at)
                                            : null;
                                        $isRescheduled = $originalDate && $originalDate->ne($scheduledDate);

                                        $today = \Carbon\Carbon::today();
                                        $tomorrow = \Carbon\Carbon::tomorrow();
                                        $isToday = $scheduledDate->isSameDay($today);
                                        $isTomorrow = $scheduledDate->isSameDay($tomorrow);

                                        $showWa =
                                            ($isToday || $isTomorrow) &&
                                            $booking->status === 'scheduled' &&
                                            !empty($booking->customer?->phone);

                                        $waUrl = null;
                                        if ($showWa) {
                                            $jadwalFormatted = $scheduledDate->translatedFormat(
                                                'l, d F Y \p\u\k\u\l H:i',
                                            );
                                            $vars = [
                                                'nama_pelanggan' => $booking->customer->name,
                                                'layanan' => $booking->service->name,
                                                'terapis' => $booking->therapist->name,
                                                'jadwal' => $jadwalFormatted,
                                            ];
                                            $templateKey = $isRescheduled
                                                ? 'booking_reminder_reschedule'
                                                : 'booking_reminder';
                                            if ($isRescheduled && $originalDate) {
                                                $vars['jadwal_lama'] = $originalDate->translatedFormat('d F Y H:i');
                                            }
                                            $waMsg = \App\Models\WaMessageTemplate::render($templateKey, $vars);
                                            if ($waMsg) {
                                                $phone = \App\Models\WaMessageTemplate::normalizePhone(
                                                    $booking->customer->phone,
                                                );
                                                $waUrl = $phone
                                                    ? "https://wa.me/{$phone}?text=" . urlencode($waMsg)
                                                    : null;
                                            }
                                        }

                                        $cls = match ($booking->status) {
                                            'scheduled' => 'bg-amber-100 text-amber-700',
                                            'completed' => 'bg-emerald-100 text-emerald-700',
                                            'cancelled' => 'bg-red-100 text-red-700',
                                            'ongoing' => 'bg-blue-100 text-blue-700',
                                            default => 'bg-gray-100 text-gray-500',
                                        };
                                        $lbl = match ($booking->status) {
                                            'scheduled' => 'Terjadwal',
                                            'completed' => 'Selesai',
                                            'cancelled' => 'Batal',
                                            'ongoing' => 'Berlangsung',
                                            default => $booking->status,
                                        };

                                        $canComplete = in_array($booking->status, ['scheduled', 'ongoing']);
                                    @endphp

                                    <tr
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors
                                        {{ $isRescheduled ? 'border-l-4 border-l-purple-400' : '' }}
                                        {{ $isToday ? 'bg-amber-50/30 dark:bg-amber-900/5' : ($isTomorrow ? 'bg-blue-50/30 dark:bg-blue-900/5' : '') }}">

                                        <td class="px-5 py-3.5 text-gray-400">{{ $i + 1 }}</td>

                                        <td class="px-5 py-3.5">
                                            <div class="font-medium text-gray-800 dark:text-gray-200">
                                                {{ $booking->customer->name ?? '—' }}
                                            </div>
                                            @if ($isToday)
                                                <span
                                                    class="text-[10px] font-semibold text-amber-600 bg-amber-100 px-1.5 py-0.5 rounded-full">Hari
                                                    ini</span>
                                            @elseif ($isTomorrow)
                                                <span
                                                    class="text-[10px] font-semibold text-blue-600 bg-blue-100 px-1.5 py-0.5 rounded-full">Besok</span>
                                            @endif
                                        </td>

                                        <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">
                                            {{ $booking->therapist->name ?? '—' }}
                                        </td>

                                        <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">
                                            {{ $booking->service->name ?? '—' }}
                                        </td>

                                        <td class="px-5 py-3.5">
                                            <div class="text-gray-700 dark:text-gray-300 font-medium">
                                                {{ $scheduledDate->format('d M Y, H:i') }}
                                            </div>
                                            @if ($isRescheduled)
                                                <div class="mt-1">
                                                    <span
                                                        class="inline-flex items-center gap-1 text-[10px] font-bold text-purple-700 bg-purple-100 dark:bg-purple-900/30 dark:text-purple-300 px-2 py-0.5 rounded-full">
                                                        🔄 Dijadwal Ulang
                                                    </span>
                                                </div>
                                                <div class="text-[11px] text-gray-400 mt-0.5 line-through">
                                                    Semula: {{ $originalDate->format('d M Y, H:i') }}
                                                </div>
                                            @endif
                                        </td>

                                        <td class="px-5 py-3.5">
                                            <div class="font-semibold text-amber-600 dark:text-amber-400">
                                                Rp {{ number_format($booking->final_price, 0, ',', '.') }}
                                            </div>
                                            @if ($booking->discount > 0)
                                                <div class="text-[10px] text-gray-400 mt-0.5">
                                                    Disc: Rp {{ number_format($booking->discount, 0, ',', '.') }}
                                                </div>
                                            @endif
                                            @if ($booking->promo_id)
                                                <div class="text-[10px] text-emerald-500 mt-0.5">🎟 Promo</div>
                                            @endif
                                        </td>

                                        <td class="px-5 py-3.5">
                                            <span
                                                class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold {{ $cls }}">
                                                {{ $lbl }}
                                            </span>
                                        </td>

                                        <td class="px-5 py-3.5">
                                            <div class="flex items-center gap-1.5 flex-wrap">

                                                {{-- ✅ Tombol Selesai --}}
                                                @if ($canComplete)
                                                    <form method="POST"
                                                        action="{{ route('admin.bookings.complete', $booking) }}"
                                                        onsubmit="return confirm('Tandai booking {{ addslashes($booking->customer->name) }} sebagai selesai?')">
                                                        @csrf
                                                        <button type="submit"
                                                            class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-lg transition-colors border border-emerald-200">
                                                            ✓ Selesai
                                                        </button>
                                                    </form>
                                                @endif

                                                {{-- 🖨️ Tombol Cetak Struk --}}
                                                <a href="{{ route('admin.bookings.receipt', $booking) }}"
                                                    target="_blank" title="Preview & Cetak Struk"
                                                    class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-xs font-medium rounded-lg transition-colors border border-indigo-200">
                                                    🖨️
                                                </a>

                                                {{-- 📱 Tombol Reminder WA --}}
                                                @if ($waUrl)
                                                    <a href="{{ $waUrl }}" target="_blank"
                                                        class="inline-flex items-center gap-1 px-3 py-1 bg-green-50 hover:bg-green-100 text-green-600 text-xs font-medium rounded-lg transition-colors border border-green-200">
                                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24"
                                                            fill="currentColor">
                                                            <path
                                                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z" />
                                                            <path
                                                                d="M12 0C5.373 0 0 5.373 0 12c0 2.124.554 4.118 1.528 5.847L0 24l6.335-1.507A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.891 0-3.659-.5-5.192-1.375l-.371-.22-3.762.895.952-3.67-.242-.38A9.955 9.955 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z" />
                                                        </svg>
                                                        Reminder
                                                        @if ($isRescheduled)
                                                            <span
                                                                class="w-1.5 h-1.5 rounded-full bg-purple-400 inline-block"
                                                                title="Pesan reschedule"></span>
                                                        @endif
                                                    </a>
                                                @endif

                                                {{-- ✏️ Edit --}}
                                                <a href="{{ route('admin.bookings.edit', $booking) }}"
                                                    class="px-3 py-1 bg-amber-50 hover:bg-amber-100 text-amber-600 text-xs font-medium rounded-lg transition-colors">
                                                    Edit
                                                </a>

                                                {{-- 🗑️ Hapus --}}
                                                <form method="POST"
                                                    action="{{ route('admin.bookings.destroy', $booking) }}"
                                                    onsubmit="return confirm('Hapus booking ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="px-3 py-1 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-medium rounded-lg">
                                                        Hapus
                                                    </button>
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
                        Belum ada booking.
                        <a href="{{ route('admin.bookings.create') }}" class="text-indigo-500 hover:underline">Buat
                            sekarang</a>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         MODAL BOOKING (dari kalender)
    ════════════════════════════════════════════════ --}}
    <div id="bookingModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" id="modalBackdrop"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg relative">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-white" id="modalTitle">Buat Booking</h3>
                    <button id="closeModal"
                        class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
                </div>
                <form method="POST" action="{{ route('admin.bookings.store') }}" class="px-6 py-5"
                    id="bookingForm">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Pelanggan
                                *</label>
                            <select name="customer_id" required
                                class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                <option value="">-- Pilih --</option>
                                @foreach ($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Terapis
                                *</label>
                            <select name="therapist_id" id="modalTherapist" required
                                class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                <option value="">-- Pilih --</option>
                                @foreach ($therapists as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Layanan
                                *</label>
                            <select name="service_id" id="modalService" required
                                class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                <option value="">-- Pilih --</option>
                                @foreach ($services as $s)
                                    <option value="{{ $s->id }}" data-price="{{ $s->price }}">
                                        {{ $s->name }} — Rp {{ number_format($s->price, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Sumber
                                Order</label>
                            <select name="order_source"
                                class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                <option value="walkin">Walk-in</option>
                                <option value="wa">WhatsApp</option>
                                <option value="web">Online/App</option>
                            </select>
                        </div>

                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Tanggal
                                & Waktu</label>
                            <input type="datetime-local" name="scheduled_at" id="modalDatetime" required
                                class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                        </div>

                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Diskon
                                (Rp)</label>
                            <input type="number" name="discount" id="modalDiscount" value="0" min="0"
                                class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200"
                                oninput="calcModalTotal()">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                Promo
                                <span class="font-normal normal-case text-emerald-500 ml-1">(Opsional — jika ada promo,
                                    total boleh Rp 0)</span>
                            </label>
                            <select name="promo_id" id="modalPromo"
                                class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                <option value="">-- Tidak ada promo --</option>
                                @foreach ($promos ?? [] as $promo)
                                    <option value="{{ $promo->id }}"
                                        data-discount="{{ $promo->discount_amount ?? 0 }}"
                                        data-percent="{{ $promo->discount_percent ?? 0 }}">
                                        {{ $promo->name }}
                                        @if ($promo->discount_amount ?? 0)
                                            — Rp {{ number_format($promo->discount_amount, 0, ',', '.') }}
                                        @elseif ($promo->discount_percent ?? 0)
                                            — {{ $promo->discount_percent }}%
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Kalkulasi harga --}}
                        <div
                            class="sm:col-span-2 px-3 py-2.5 bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800 rounded-lg flex gap-6 items-center text-sm">
                            <div>
                                <div class="text-xs text-gray-400 mb-0.5">Harga Layanan</div>
                                <div id="mDisplayPrice" class="font-bold text-gray-700 dark:text-gray-200">Rp 0</div>
                            </div>
                            <div class="text-gray-300">−</div>
                            <div>
                                <div class="text-xs text-gray-400 mb-0.5">Diskon</div>
                                <div id="mDisplayDiscount" class="font-bold text-red-500">Rp 0</div>
                            </div>
                            <div class="text-gray-300">=</div>
                            <div>
                                <div class="text-xs text-gray-400 mb-0.5">Total</div>
                                <div id="mDisplayTotal" class="font-bold text-amber-600 text-base">Rp 0</div>
                            </div>
                        </div>

                        <div id="zeroPriceWarning"
                            class="hidden sm:col-span-2 flex items-start gap-2 px-3 py-2.5 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg text-xs text-red-600 dark:text-red-400">
                            <span class="text-base leading-none">⚠️</span>
                            <span>Total tidak boleh Rp 0 kecuali menggunakan promo. Silakan pilih promo atau kurangi
                                nilai diskon.</span>
                        </div>

                    </div>

                    <div class="flex gap-3 mt-5">
                        <button type="submit" id="modalSubmitBtn"
                            class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-40 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg transition-colors">
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

    {{-- ═══════════════════════════════════════════════
         BANNER SETELAH BOOKING SELESAI
         (WA + Cetak Struk)
    ════════════════════════════════════════════════ --}}
    @if (session('complete_wa_url') || session('complete_receipt_url'))
        <div id="waBanner"
            class="fixed bottom-5 right-5 z-50 flex items-start gap-3 px-5 py-4 bg-white dark:bg-gray-800 border border-green-200 dark:border-green-700 rounded-2xl shadow-2xl max-w-sm"
            style="animation: slideUp 0.3s ease-out;">

            {{-- Icon --}}
            <div
                class="flex-shrink-0 w-11 h-11 bg-green-100 dark:bg-green-900/40 rounded-full flex items-center justify-center mt-0.5">
                <svg class="w-5 h-5 text-green-600" viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z" />
                    <path
                        d="M12 0C5.373 0 0 5.373 0 12c0 2.124.554 4.118 1.528 5.847L0 24l6.335-1.507A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.891 0-3.659-.5-5.192-1.375l-.371-.22-3.762.895.952-3.67-.242-.38A9.955 9.955 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z" />
                </svg>
            </div>

            {{-- Konten --}}
            <div class="flex-1 min-w-0">
                <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">✅ Booking selesai!</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    <span class="font-medium text-gray-700 dark:text-gray-300">
                        {{ session('complete_customer_name', 'Pelanggan') }}
                    </span>
                </div>

                {{-- Progress bar auto-close --}}
                <div class="mt-2 h-0.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div id="waBannerProgress" class="h-full bg-green-400 rounded-full"
                        style="width:100%; transition: width 15s linear;"></div>
                </div>

                {{-- Tombol aksi --}}
                <div class="flex gap-2 mt-3 flex-wrap">

                    {{-- Cetak Struk (auto print) --}}
                    @if (session('complete_receipt_url'))
                        <a href="{{ session('complete_receipt_url') }}?autoprint=1" target="_blank"
                            onclick="closeBanner()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm whitespace-nowrap">
                            🖨️ Cetak Struk
                        </a>
                        <a href="{{ session('complete_receipt_url') }}" target="_blank"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-xs font-medium rounded-lg transition-colors whitespace-nowrap">
                            👁 Preview
                        </a>
                    @endif

                    {{-- Kirim WA --}}
                    @if (session('complete_wa_url'))
                        <a href="{{ session('complete_wa_url') }}" target="_blank" onclick="closeBanner()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm whitespace-nowrap">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z" />
                                <path
                                    d="M12 0C5.373 0 0 5.373 0 12c0 2.124.554 4.118 1.528 5.847L0 24l6.335-1.507A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.891 0-3.659-.5-5.192-1.375l-.371-.22-3.762.895.952-3.67-.242-.38A9.955 9.955 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z" />
                            </svg>
                            Kirim WA
                        </a>
                    @endif

                    <button onclick="closeBanner()"
                        class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-400 text-xs rounded-lg transition-colors">
                        Lewati
                    </button>
                </div>
            </div>
        </div>

        <style>
            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>

        <script>
            const waBannerTimer = setTimeout(closeBanner, 15000);

            document.addEventListener('DOMContentLoaded', function() {
                const bar = document.getElementById('waBannerProgress');
                if (bar) setTimeout(() => {
                    bar.style.width = '0%';
                }, 100);
            });

            function closeBanner() {
                clearTimeout(waBannerTimer);
                const banner = document.getElementById('waBanner');
                if (banner) {
                    banner.style.opacity = '0';
                    banner.style.transform = 'translateY(10px)';
                    banner.style.transition = 'opacity 0.2s, transform 0.2s';
                    setTimeout(() => banner.remove(), 200);
                }
            }
        </script>
    @endif

    {{-- ═══════════════════════════════════════════════
         JAVASCRIPT
    ════════════════════════════════════════════════ --}}
    <script>
        const THERAPISTS = @json($therapists);
        const HOURS = Array.from({
            length: 13
        }, (_, i) => i + 10);
        let currentDate = new Date();
        currentDate.setHours(0, 0, 0, 0);

        function toLocalISO(date) {
            return `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-${String(date.getDate()).padStart(2,'0')}`;
        }

        function formatDisplayDate(date) {
            return date.toLocaleDateString('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        }

        function setDate(date) {
            currentDate = date;
            document.getElementById('datePicker').value = toLocalISO(date);
            document.getElementById('displayDate').textContent = formatDisplayDate(date);
            loadCalendar(toLocalISO(date));
        }

        document.getElementById('prevDay').addEventListener('click', () => {
            const d = new Date(currentDate);
            d.setDate(d.getDate() - 1);
            setDate(d);
        });
        document.getElementById('nextDay').addEventListener('click', () => {
            const d = new Date(currentDate);
            d.setDate(d.getDate() + 1);
            setDate(d);
        });
        document.getElementById('todayBtn').addEventListener('click', () => {
            const d = new Date();
            d.setHours(0, 0, 0, 0);
            setDate(d);
        });
        document.getElementById('datePicker').addEventListener('change', function() {
            setDate(new Date(this.value + 'T00:00:00'));
        });

        async function loadCalendar(dateStr) {
            document.getElementById('calendarGrid').innerHTML =
                `<div class="flex items-center justify-center py-12 text-gray-400 text-sm gap-2">
                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg> Memuat...
                </div>`;
            try {
                const res = await fetch(`{{ route('admin.bookings.calendar-data') }}?date=${dateStr}`);
                const data = await res.json();
                renderGrid(dateStr, data.bookings);
            } catch (e) {
                document.getElementById('calendarGrid').innerHTML =
                    `<div class="text-center py-10 text-red-400 text-sm">Gagal memuat data kalender.</div>`;
            }
        }

        function renderGrid(dateStr, bookings) {
            const map = {};
            THERAPISTS.forEach(t => {
                map[t.id] = {};
            });
            bookings.forEach(b => {
                if (map[b.therapist_id]) map[b.therapist_id][b.hour] = b;
            });

            const now = new Date();
            const todayStr = toLocalISO(now);
            const currentHour = now.getHours();

            const summary = {};
            THERAPISTS.forEach(t => {
                const booked = bookings.filter(b => b.therapist_id == t.id && ['scheduled', 'ongoing'].includes(b
                    .status));
                summary[t.id] = {
                    booked: booked.length
                };
            });

            let html =
                `<table class="w-full text-sm border-collapse" style="min-width:${THERAPISTS.length*140+80}px">
                <thead><tr class="bg-gray-50 dark:bg-gray-700/60 sticky top-0 z-10">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider w-20 border-b border-r border-gray-100 dark:border-gray-700">Jam</th>`;

            THERAPISTS.forEach(t => {
                const s = summary[t.id];
                html += `<th class="px-3 py-3 text-center border-b border-r border-gray-100 dark:border-gray-700 last:border-r-0">
                    <div class="text-xs font-semibold text-gray-700 dark:text-gray-200">${t.name}</div>
                    <div class="mt-1">${s.booked > 0
                        ? `<span class="text-xs px-1.5 py-0.5 bg-amber-100 text-amber-600 rounded-full">${s.booked} booking</span>`
                        : `<span class="text-xs px-1.5 py-0.5 bg-emerald-100 text-emerald-600 rounded-full">✓ Bebas</span>`
                    }</div></th>`;
            });
            html += `</tr></thead><tbody>`;

            HOURS.forEach(hour => {
                const timeLabel = `${String(hour).padStart(2,'0')}:00`;
                const timeEnd = `${String(hour+1).padStart(2,'0')}:00`;
                const isPast = dateStr === todayStr && hour < currentHour;
                const isNow = dateStr === todayStr && hour === currentHour;
                const rowBg = isNow ? 'bg-indigo-50/50 dark:bg-indigo-900/10' : (isPast ? 'opacity-40' : '');

                html += `<tr class="border-b border-gray-100 dark:border-gray-700 ${rowBg} hover:bg-gray-50/80 dark:hover:bg-gray-700/20 transition-colors">
                    <td class="px-4 py-2 border-r border-gray-100 dark:border-gray-700 whitespace-nowrap">
                        <div class="font-mono text-xs font-semibold ${isNow ? 'text-indigo-600' : 'text-gray-400'}">${timeLabel}</div>
                        <div class="font-mono text-[10px] text-gray-300">${timeEnd}</div>
                    </td>`;

                THERAPISTS.forEach(t => {
                    const booking = map[t.id][hour];
                    if (booking) {
                        const cfg = {
                            scheduled: {
                                bg: 'bg-amber-50 dark:bg-amber-900/20',
                                border: 'border-amber-200',
                                text: 'text-amber-700',
                                dot: 'bg-amber-400',
                                label: 'Terjadwal'
                            },
                            ongoing: {
                                bg: 'bg-blue-50 dark:bg-blue-900/20',
                                border: 'border-blue-200',
                                text: 'text-blue-700',
                                dot: 'bg-blue-400',
                                label: 'Berlangsung'
                            },
                            completed: {
                                bg: 'bg-emerald-50 dark:bg-emerald-900/20',
                                border: 'border-emerald-200',
                                text: 'text-emerald-700',
                                dot: 'bg-emerald-400',
                                label: 'Selesai'
                            },
                            cancelled: {
                                bg: 'bg-gray-50 dark:bg-gray-700/40',
                                border: 'border-gray-200',
                                text: 'text-gray-400',
                                dot: 'bg-gray-300',
                                label: 'Batal'
                            },
                        } [booking.status] ?? {
                            bg: 'bg-gray-50',
                            border: 'border-gray-200',
                            text: 'text-gray-500',
                            dot: 'bg-gray-300',
                            label: booking.status
                        };

                        const rescheduleBadge = booking.is_rescheduled ?
                            `<div class="mt-0.5 text-[9px] font-bold text-purple-600 bg-purple-100 px-1.5 py-0.5 rounded-full inline-block">🔄 Dijadwal Ulang</div>` :
                            '';

                        const waBtn = booking.wa_url ?
                            `<a href="${booking.wa_url}" target="_blank" onclick="event.stopPropagation()"
                                class="mt-1 flex items-center gap-1 text-[10px] font-semibold text-green-600 bg-green-50 hover:bg-green-100 border border-green-200 rounded px-1.5 py-0.5 transition-colors">
                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                    <path d="M12 0C5.373 0 0 5.373 0 12c0 2.124.554 4.118 1.528 5.847L0 24l6.335-1.507A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.891 0-3.659-.5-5.192-1.375l-.371-.22-3.762.895.952-3.67-.242-.38A9.955 9.955 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                                </svg>
                                Reminder</a>` :
                            '';

                        const receiptBtn = booking.receipt_url ?
                            `<a href="${booking.receipt_url}" target="_blank" onclick="event.stopPropagation()"
                                class="mt-0.5 flex items-center gap-1 text-[10px] font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded px-1.5 py-0.5 transition-colors">
                                🖨️ Struk</a>` :
                            '';

                        const rescheduleStyle = booking.is_rescheduled ? 'border-l-2 border-l-purple-400' :
                            '';

                        html += `<td class="px-2 py-1.5 border-r border-gray-100 dark:border-gray-700 last:border-r-0 ${rescheduleStyle}">
                            <a href="${booking.edit_url}" class="block rounded-lg border px-2.5 py-1.5 ${cfg.bg} ${cfg.border} hover:shadow-sm transition-shadow">
                                <div class="flex items-center gap-1 mb-0.5">
                                    <span class="w-1.5 h-1.5 rounded-full ${cfg.dot} flex-shrink-0"></span>
                                    <span class="text-[10px] font-semibold ${cfg.text}">${cfg.label}</span>
                                </div>
                                <div class="text-xs font-medium text-gray-700 dark:text-gray-200 truncate">${booking.customer_name}</div>
                                <div class="text-[10px] text-gray-400 truncate">${booking.service_name}</div>
                            </a>
                            ${rescheduleBadge}${waBtn}${receiptBtn}
                        </td>`;
                    } else {
                        html += `<td class="px-2 py-1.5 border-r border-gray-100 dark:border-gray-700 last:border-r-0">
                            ${isPast
                                ? `<div class="h-[52px] rounded-lg bg-gray-50 dark:bg-gray-700/20"></div>`
                                : `<button type="button"
                                        onclick="openModal('${dateStr}', ${hour}, ${t.id}, '${t.name.replace(/'/g,"\\'")}' )"
                                        class="w-full h-[52px] rounded-lg border border-dashed border-gray-200 dark:border-gray-600 hover:border-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 text-gray-300 hover:text-indigo-400 transition-all text-xs font-medium group flex items-center justify-center">
                                        <span class="opacity-0 group-hover:opacity-100 transition-opacity select-none">+ Booking</span>
                                    </button>`
                            }
                        </td>`;
                    }
                });

                html += `</tr>`;
            });

            html += `</tbody></table>`;
            document.getElementById('calendarGrid').innerHTML = html;
        }

        // ── Modal ──────────────────────────────────────────
        let modalServicePrice = 0;
        let hasPromo = false;

        function openModal(dateStr, hour, therapistId, therapistName) {
            document.getElementById('modalDatetime').value = `${dateStr}T${String(hour).padStart(2,'0')}:00`;
            document.getElementById('modalTitle').textContent =
                `Booking — ${therapistName}, ${String(hour).padStart(2,'0')}:00`;
            document.getElementById('modalDiscount').value = 0;
            document.getElementById('modalService').selectedIndex = 0;
            document.getElementById('modalPromo').selectedIndex = 0;
            modalServicePrice = 0;
            hasPromo = false;
            calcModalTotal();
            [...document.getElementById('modalTherapist').options].forEach(o => {
                o.selected = o.value == therapistId;
            });
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

        document.getElementById('modalPromo').addEventListener('change', function() {
            hasPromo = this.value !== '';
            if (hasPromo) {
                const opt = this.options[this.selectedIndex];
                const discAmount = parseInt(opt.getAttribute('data-discount')) || 0;
                const discPercent = parseInt(opt.getAttribute('data-percent')) || 0;
                const disc = discAmount || Math.round(modalServicePrice * discPercent / 100);
                document.getElementById('modalDiscount').value = disc;
            }
            calcModalTotal();
        });

        function calcModalTotal() {
            const disc = parseInt(document.getElementById('modalDiscount').value) || 0;
            const total = Math.max(0, modalServicePrice - disc);
            const fmt = n => 'Rp ' + n.toLocaleString('id-ID');
            document.getElementById('mDisplayPrice').textContent = fmt(modalServicePrice);
            document.getElementById('mDisplayDiscount').textContent = fmt(disc);
            document.getElementById('mDisplayTotal').textContent = fmt(total);
            const isInvalid = (total === 0 && modalServicePrice > 0 && !hasPromo);
            document.getElementById('zeroPriceWarning').classList.toggle('hidden', !isInvalid);
            document.getElementById('modalSubmitBtn').disabled = isInvalid;
        }

        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            const disc = parseInt(document.getElementById('modalDiscount').value) || 0;
            const total = Math.max(0, modalServicePrice - disc);
            if (total === 0 && modalServicePrice > 0 && !hasPromo) {
                e.preventDefault();
                document.getElementById('zeroPriceWarning').classList.remove('hidden');
            }
        });

        setDate(currentDate);
    </script>
</x-app-layout>
