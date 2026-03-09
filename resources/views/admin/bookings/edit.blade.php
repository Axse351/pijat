<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.bookings.index') }}"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                ← Kembali
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Booking
                @if ($booking->is_rescheduled)
                    <span
                        class="ml-2 text-sm font-normal text-purple-600 bg-purple-100 dark:bg-purple-900/30 dark:text-purple-300 px-2 py-0.5 rounded-full">🔄
                        Pernah Dijadwal Ulang</span>
                @endif
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            @if ($errors->any())
                <div
                    class="mb-4 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg text-sm text-red-600 dark:text-red-400">
                    <p class="font-semibold mb-1">Terdapat kesalahan:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Info jadwal lama jika sudah pernah reschedule --}}
            @if ($booking->is_rescheduled && $booking->original_scheduled_at)
                <div
                    class="mb-4 px-4 py-3 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-700 rounded-lg text-sm">
                    <p class="font-semibold text-purple-700 dark:text-purple-300 mb-1">🔄 Riwayat Penjadwalan Ulang</p>
                    <p class="text-purple-600 dark:text-purple-400">
                        Jadwal asli:
                        <span class="font-medium line-through">
                            {{ \Carbon\Carbon::parse($booking->original_scheduled_at)->translatedFormat('l, d F Y H:i') }}
                        </span>
                    </p>
                    <p class="text-purple-600 dark:text-purple-400 mt-0.5">
                        Diubah ke: <span
                            class="font-medium">{{ \Carbon\Carbon::parse($booking->scheduled_at)->translatedFormat('l, d F Y H:i') }}</span>
                    </p>
                </div>
            @endif

            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <form method="POST" action="{{ route('admin.bookings.update', $booking) }}" id="editForm">
                    @csrf @method('PUT')

                    {{-- Hidden: tandai jika reschedule dikonfirmasi --}}
                    <input type="hidden" name="is_rescheduled" id="isRescheduledFlag"
                        value="{{ $booking->is_rescheduled ? '1' : '0' }}">

                    <div class="px-6 py-5 space-y-4">

                        {{-- Pelanggan --}}
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Pelanggan
                                *</label>
                            <select name="customer_id" required
                                class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                @foreach ($customers as $c)
                                    <option value="{{ $c->id }}" @selected(old('customer_id', $booking->customer_id) == $c->id)>{{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Terapis --}}
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Terapis
                                    *</label>
                                <select name="therapist_id" required
                                    class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                    @foreach ($therapists as $t)
                                        <option value="{{ $t->id }}" @selected(old('therapist_id', $booking->therapist_id) == $t->id)>
                                            {{ $t->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Layanan --}}
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Layanan
                                    *</label>
                                <select name="service_id" id="serviceSelect" required
                                    class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                    @foreach ($services as $s)
                                        <option value="{{ $s->id }}" data-price="{{ $s->price }}"
                                            @selected(old('service_id', $booking->service_id) == $s->id)>
                                            {{ $s->name }} — Rp {{ number_format($s->price, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Sumber Order --}}
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Sumber
                                    Order</label>
                                <select name="order_source"
                                    class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                    @foreach (['walkin' => 'Walk-in', 'wa' => 'WhatsApp', 'web' => 'Online/App'] as $val => $lbl)
                                        <option value="{{ $val }}" @selected(old('order_source', $booking->order_source) === $val)>
                                            {{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Status --}}
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Status</label>
                                <select name="status"
                                    class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                    @foreach (['scheduled' => 'Terjadwal', 'ongoing' => 'Berlangsung', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $val => $lbl)
                                        <option value="{{ $val }}" @selected(old('status', $booking->status) === $val)>
                                            {{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- ── Tanggal & Waktu — dilindungi trigger reschedule ── --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                Tanggal & Waktu *
                            </label>
                            <div class="relative">
                                <input type="datetime-local" name="scheduled_at" id="scheduledAtInput"
                                    value="{{ old('scheduled_at', \Carbon\Carbon::parse($booking->scheduled_at)->format('Y-m-d\TH:i')) }}"
                                    required
                                    class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200 pr-10">
                                {{-- Ikon tanda jika sudah reschedule --}}
                                <div id="rescheduleIcon"
                                    class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-purple-500 text-base"
                                    title="Jadwal sudah diubah">🔄</div>
                            </div>
                            {{-- Info jadwal saat ini vs yang akan diubah --}}
                            <p id="currentDateInfo" class="mt-1 text-xs text-gray-400">
                                Jadwal saat ini: <span
                                    class="font-medium text-gray-600 dark:text-gray-300">{{ \Carbon\Carbon::parse($booking->scheduled_at)->translatedFormat('d F Y, H:i') }}</span>
                            </p>
                            <p id="rescheduleHint"
                                class="hidden mt-1 text-xs font-semibold text-purple-600 dark:text-purple-400">
                                ⚠️ Jadwal berbeda dari semula — akan ditandai sebagai "Dijadwal Ulang"
                            </p>
                        </div>

                        {{-- Program --}}
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Program</label>
                            <select name="program_id" id="programSelect"
                                class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                <option value="">-- Tanpa Program --</option>
                                @foreach ($programs as $program)
                                    <option value="{{ $program->id }}"
                                        data-discount-type="{{ $program->discount_type }}"
                                        data-discount-value="{{ $program->discount_value }}"
                                        data-max-discount="{{ $program->max_discount ?? 0 }}"
                                        @selected(old('program_id', $booking->program_id) == $program->id)>
                                        {{ $program->nama_program }}
                                        @if ($program->discount_type === 'percent')
                                            — {{ $program->discount_value }}% off
                                        @else
                                            — Rp {{ number_format($program->discount_value, 0, ',', '.') }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Promo --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                Promo
                                <span class="font-normal normal-case text-emerald-500 ml-1">(jika ada promo, total boleh
                                    Rp 0)</span>
                            </label>
                            <select name="promo_id" id="promoSelect"
                                class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                <option value="">-- Tidak ada promo --</option>
                                @foreach ($promos ?? [] as $promo)
                                    <option value="{{ $promo->id }}"
                                        data-discount="{{ $promo->discount_amount ?? 0 }}"
                                        data-percent="{{ $promo->discount_percent ?? 0 }}"
                                        @selected(old('promo_id', $booking->promo_id) == $promo->id)>
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

                        {{-- Diskon --}}
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Diskon
                                Tambahan (Rp)</label>
                            <input type="number" name="discount" id="discountInput"
                                value="{{ old('discount', $booking->discount ?? 0) }}" min="0"
                                class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200"
                                oninput="calcTotal()">
                            <p class="text-xs text-gray-400 mt-1">Diskon manual tambahan di luar program dan promo.</p>
                        </div>

                        {{-- Price summary --}}
                        <div
                            class="px-4 py-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800 rounded-lg space-y-2 text-sm flex-wrap">
                            <div class="flex gap-6 items-center flex-wrap">
                                <div>
                                    <div class="text-xs text-gray-400 mb-0.5">Harga Layanan</div>
                                    <div id="displayPrice" class="font-bold text-gray-700 dark:text-gray-200">Rp 0</div>
                                </div>
                                <div class="text-gray-300">−</div>
                                <div>
                                    <div class="text-xs text-gray-400 mb-0.5">Diskon Program</div>
                                    <div id="displayProgramDisc" class="font-bold text-blue-600">Rp 0</div>
                                    <div id="displayProgramLabel" class="text-xs text-gray-400 mt-0.5"></div>
                                </div>
                                <div class="text-gray-300">−</div>
                                <div>
                                    <div class="text-xs text-gray-400 mb-0.5">Diskon Promo</div>
                                    <div id="displayPromoDisc" class="font-bold text-emerald-600">Rp 0</div>
                                    <div id="displayPromoLabel" class="text-xs text-gray-400 mt-0.5"></div>
                                </div>
                                <div class="text-gray-300">−</div>
                                <div>
                                    <div class="text-xs text-gray-400 mb-0.5">Diskon Tambahan</div>
                                    <div id="displayDiscount" class="font-bold text-red-500">Rp 0</div>
                                </div>
                                <div class="text-gray-300">=</div>
                                <div>
                                    <div class="text-xs text-gray-400 mb-0.5">Total</div>
                                    <div id="displayTotal" class="font-bold text-amber-600 text-base">Rp 0</div>
                                </div>
                            </div>
                        </div>

                        {{-- Peringatan total 0 --}}
                        <div id="zeroPriceWarning"
                            class="hidden flex items-start gap-2 px-3 py-2.5 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg text-xs text-red-600 dark:text-red-400">
                            <span class="text-base leading-none">⚠️</span>
                            <span>Total tidak boleh Rp 0 kecuali menggunakan promo. Silakan pilih promo atau kurangi
                                nilai diskon.</span>
                        </div>

                        {{-- Catatan --}}
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Catatan</label>
                            <textarea name="notes" rows="2"
                                class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200 resize-none"
                                placeholder="Catatan tambahan...">{{ old('notes', $booking->notes) }}</textarea>
                        </div>

                    </div>

                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 flex gap-3">
                        <button type="submit" id="submitBtn"
                            class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-40 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-lg transition-colors">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.bookings.index') }}"
                            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg text-center transition-colors">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ═══════ MODAL KONFIRMASI RESCHEDULE ═══════ --}}
    <div id="rescheduleModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
                <div class="text-5xl mb-3">🔄</div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-2">Konfirmasi Penjadwalan Ulang</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Anda mengubah jadwal booking ini:</p>

                <div class="bg-gray-50 dark:bg-gray-700 rounded-xl px-4 py-3 mb-4 text-sm text-left space-y-2">
                    <div class="flex items-start gap-2">
                        <span class="text-gray-400 w-16 shrink-0 text-xs mt-0.5">Semula:</span>
                        <span id="rOldDate" class="font-medium text-gray-700 dark:text-gray-200 line-through"></span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-indigo-400 w-16 shrink-0 text-xs mt-0.5">Menjadi:</span>
                        <span id="rNewDate" class="font-semibold text-indigo-600 dark:text-indigo-400"></span>
                    </div>
                </div>

                <p class="text-xs text-gray-400 mb-5">Perubahan ini akan dicatat dan ditandai dengan badge <strong
                        class="text-purple-600">🔄 Dijadwal Ulang</strong> di daftar booking.</p>

                <div class="flex gap-3">
                    <button id="rConfirmBtn"
                        class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors">
                        Ya, Jadwal Ulang
                    </button>
                    <button id="rCancelBtn"
                        class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-semibold rounded-lg transition-colors">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ── Data dari server ──
        const ORIGINAL_DATETIME =
            "{{ \Carbon\Carbon::parse($booking->original_scheduled_at ?? $booking->scheduled_at)->format('Y-m-d\TH:i') }}";
        const CURRENT_DATETIME = "{{ \Carbon\Carbon::parse($booking->scheduled_at)->format('Y-m-d\TH:i') }}";
        const SERVICE_PRICE = {{ $booking->service->price ?? 0 }};
        const HAS_PROMO_INITIAL = {{ $booking->promo_id ? 'true' : 'false' }};

        // Harga semua layanan (untuk update saat layanan diganti)
        const SERVICE_PRICES = {
            @foreach ($services as $s)
                "{{ $s->id }}": {{ $s->price }},
            @endforeach
        };

        // Data semua program
        const PROGRAMS = {
            @foreach ($programs as $p)
                "{{ $p->id }}": {
                    discountType: "{{ $p->discount_type }}",
                    discountValue: {{ $p->discount_value }},
                    maxDiscount: {{ $p->max_discount ?? 0 }},
                },
            @endforeach
        };

        // ── State ──
        let servicePrice = SERVICE_PRICE;
        let hasPromo = HAS_PROMO_INITIAL;
        let dateConfirmed = false;

        const scheduledAtInput = document.getElementById('scheduledAtInput');
        const rescheduleModal = document.getElementById('rescheduleModal');
        const rescheduleHint = document.getElementById('rescheduleHint');
        const rescheduleIcon = document.getElementById('rescheduleIcon');
        const isRescheduledFlag = document.getElementById('isRescheduledFlag');

        // Inisialisasi harga
        calcTotal();

        // ── Deteksi perubahan tanggal ──
        scheduledAtInput.addEventListener('change', function() {
            const newVal = this.value;

            if (newVal === CURRENT_DATETIME) {
                rescheduleHint.classList.add('hidden');
                rescheduleIcon.classList.add('hidden');
                isRescheduledFlag.value = '{{ $booking->is_rescheduled ? '1' : '0' }}';
                dateConfirmed = false;
                return;
            }

            const fmtDate = d => new Date(d).toLocaleDateString('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            document.getElementById('rOldDate').textContent = fmtDate(CURRENT_DATETIME);
            document.getElementById('rNewDate').textContent = fmtDate(newVal);
            rescheduleModal.classList.remove('hidden');

            document.getElementById('rConfirmBtn').onclick = function() {
                rescheduleModal.classList.add('hidden');
                isRescheduledFlag.value = '1';
                dateConfirmed = true;
                rescheduleHint.classList.remove('hidden');
                rescheduleIcon.classList.remove('hidden');
            };

            document.getElementById('rCancelBtn').onclick = function() {
                rescheduleModal.classList.add('hidden');
                scheduledAtInput.value = CURRENT_DATETIME;
                rescheduleHint.classList.add('hidden');
                rescheduleIcon.classList.add('hidden');
                isRescheduledFlag.value = '{{ $booking->is_rescheduled ? '1' : '0' }}';
                dateConfirmed = false;
            };
        });

        // ── Update harga saat layanan diganti ──
        document.getElementById('serviceSelect').addEventListener('change', function() {
            servicePrice = SERVICE_PRICES[this.value] || 0;
            calcTotal();
        });

        // ── Update promo ──
        document.getElementById('promoSelect').addEventListener('change', function() {
            hasPromo = this.value !== '';
            if (hasPromo) {
                const opt = this.options[this.selectedIndex];
                const discAmount = parseInt(opt.getAttribute('data-discount')) || 0;
                const discPercent = parseInt(opt.getAttribute('data-percent')) || 0;
                const disc = discAmount || Math.round(servicePrice * discPercent / 100);
                document.getElementById('discountInput').value = disc;
            }
            calcTotal();
        });

        // ── Update program ──
        document.getElementById('programSelect').addEventListener('change', function() {
            calcTotal();
        });

        // ── Kalkulasi total ──
        function calcTotal() {
            const programSelect = document.getElementById('programSelect');
            const manualDisc = parseInt(document.getElementById('discountInput').value) || 0;

            let programDisc = 0;
            let programLabel = '';

            // Hitung diskon program
            if (programSelect.value !== '') {
                const programData = PROGRAMS[programSelect.value];
                if (programData) {
                    if (programData.discountType === 'percent') {
                        programDisc = Math.round(servicePrice * programData.discountValue / 100);
                        if (programData.maxDiscount > 0 && programDisc > programData.maxDiscount) {
                            programDisc = programData.maxDiscount;
                        }
                        programLabel = programData.discountValue + '% dari harga layanan';
                    } else {
                        programDisc = Math.round(programData.discountValue);
                        programLabel = 'Diskon tetap dari program';
                    }
                }
            }

            const total = Math.max(0, servicePrice - programDisc - manualDisc);
            const fmt = n => 'Rp ' + n.toLocaleString('id-ID');

            document.getElementById('displayPrice').textContent = fmt(servicePrice);
            document.getElementById('displayProgramDisc').textContent = fmt(programDisc);
            document.getElementById('displayProgramLabel').textContent = programLabel;
            document.getElementById('displayDiscount').textContent = fmt(manualDisc);
            document.getElementById('displayTotal').textContent = fmt(total);

            const isInvalid = (total === 0 && servicePrice > 0 && !hasPromo);
            document.getElementById('zeroPriceWarning').classList.toggle('hidden', !isInvalid);
            document.getElementById('submitBtn').disabled = isInvalid;
        }

        // ── Validasi submit ──
        document.getElementById('editForm').addEventListener('submit', function(e) {
            const total = Math.max(0, servicePrice - parseInt(document.getElementById('discountInput').value || 0));
            if (total === 0 && servicePrice > 0 && !hasPromo) {
                e.preventDefault();
                document.getElementById('zeroPriceWarning').classList.remove('hidden');
            }
        });
    </script>
</x-app-layout>
