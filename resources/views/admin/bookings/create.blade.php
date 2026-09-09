<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Buat Booking Baru</h2>
            <a href="{{ route('admin.bookings.index') }}"
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg">←
                Kembali</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-6">

                @if ($errors->any())
                    <div class="mb-5 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.bookings.store') }}">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                        {{-- Pelanggan (searchable) --}}
                        <div class="relative" id="customerCombobox">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Pelanggan *
                            </label>

                            @php
                                $oldCustomer = old('customer_id') ? $customers->firstWhere('id', old('customer_id')) : null;
                            @endphp

                            <input type="hidden" name="customer_id" id="customerIdInput" value="{{ old('customer_id') }}">
                            <input type="text" id="customerSearchInput" autocomplete="off"
                                placeholder="Ketik nama pelanggan..." value="{{ $oldCustomer?->name }}"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">

                            <div id="customerDropdown"
                                class="hidden absolute z-20 mt-1 w-full max-h-60 overflow-y-auto bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg text-sm">
                                @foreach ($customers as $c)
                                    <button type="button"
                                        class="customer-option w-full text-left px-4 py-2 hover:bg-indigo-50 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200"
                                        data-id="{{ $c->id }}" data-name="{{ $c->name }}">
                                        {{ $c->name }}
                                        @if ($c->points > 0)
                                            <span class="text-xs text-gray-400">({{ $c->points }} poin{{ $c->hasBonus() ? ' 🎁' : '' }})</span>
                                        @endif
                                    </button>
                                @endforeach
                                <div id="customerNoMatch" class="hidden px-4 py-2 text-gray-400 text-xs">Tidak ada
                                    pelanggan yang cocok.</div>
                            </div>
                        </div>

                        {{-- Terapis --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Terapis *
                            </label>
                            <select name="therapist_id" required
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                <option value="">-- Pilih --</option>
                                @foreach ($therapists as $t)
                                    <option value="{{ $t->id }}"
                                        {{ old('therapist_id') == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Layanan --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Layanan *
                            </label>
                            <select name="service_id" id="serviceSelect" required
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                <option value="">-- Pilih --</option>
                                @foreach ($services as $s)
                                    <option value="{{ $s->id }}" data-price="{{ $s->price }}"
                                        data-points="{{ $s->reward_points ?? 0 }}"
                                        {{ old('service_id') == $s->id ? 'selected' : '' }}>
                                        {{ $s->name }} — Rp {{ number_format($s->price, 0, ',', '.') }}
                                        @if (($s->reward_points ?? 0) > 0)
                                            ⭐ +{{ $s->reward_points }} poin
                                        @endif
                                    </option>
                                @endforeach
                            </select>

                            {{-- Info poin layanan --}}
                            <div id="servicePointInfo" class="hidden mt-2 px-3 py-2 rounded-lg text-xs font-medium">
                            </div>
                        </div>

                        {{-- Sumber Order --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Sumber Order
                            </label>
                            <select name="order_source"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                <option value="walkin">Walk-in</option>
                                <option value="wa">WhatsApp</option>
                                <option value="web">Online/App</option>
                            </select>
                        </div>

                        {{-- Tanggal & Waktu --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Tanggal & Waktu *
                            </label>
                            <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" required
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                        </div>

                        {{-- Program --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Program
                            </label>
                            <select name="program_id" id="programSelect"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                <option value="">-- Tanpa Program --</option>
                                @foreach ($programs as $program)
                                    <option value="{{ $program->id }}"
                                        data-discount-type="{{ $program->discount_type }}"
                                        data-discount-value="{{ $program->discount_value }}"
                                        data-max-discount="{{ $program->max_discount ?? 0 }}"
                                        {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                        {{ $program->nama_program }}
                                        @if ($program->discount_type === 'percent')
                                            — {{ $program->discount_value }}% off
                                        @else
                                            — Rp {{ number_format($program->discount_value, 0, ',', '.') }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Hanya program aktif yang ditampilkan.</p>
                        </div>

                        {{-- Promo --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Promo
                            </label>
                            <select name="promo_id" id="promoSelect"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                <option value="">-- Tanpa Promo --</option>
                                @foreach ($promos as $promo)
                                    <option value="{{ $promo->id }}" data-discount="{{ $promo->discount }}"
                                        {{ old('promo_id') == $promo->id ? 'selected' : '' }}>
                                        [{{ strtoupper($promo->code) }}] {{ $promo->nama_promo }} —
                                        {{ $promo->discount }}% off
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Hanya promo aktif yang ditampilkan.</p>
                        </div>

                        {{-- Diskon Manual (Rp) --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Diskon Tambahan (Rp)
                            </label>
                            <input type="number" name="discount" id="discount" value="{{ old('discount', 0) }}"
                                min="0"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200"
                                oninput="calcTotal()" placeholder="0">
                            <p class="text-xs text-gray-400 mt-1">Diskon manual tambahan di luar program dan promo.</p>
                        </div>

                        {{-- Preview Harga --}}
                        <div
                            class="sm:col-span-2 px-5 py-4 bg-gray-50 dark:bg-gray-700 rounded-xl border border-gray-100 dark:border-gray-600">
                            <div class="flex flex-wrap gap-6 items-center">
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Harga Layanan</div>
                                    <div id="displayPrice" class="font-bold text-gray-800 dark:text-gray-200">Rp 0</div>
                                </div>
                                <div class="text-gray-300 dark:text-gray-500 text-lg">−</div>
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Diskon Program</div>
                                    <div id="displayProgramDisc" class="font-bold text-blue-600">Rp 0</div>
                                    <div id="displayProgramLabel" class="text-xs text-gray-400 mt-0.5"></div>
                                </div>
                                <div class="text-gray-300 dark:text-gray-500 text-lg">−</div>
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Diskon Promo</div>
                                    <div id="displayPromoDisc" class="font-bold text-emerald-600">Rp 0</div>
                                    <div id="displayPromoLabel" class="text-xs text-gray-400 mt-0.5"></div>
                                </div>
                                <div class="text-gray-300 dark:text-gray-500 text-lg">−</div>
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Diskon Tambahan</div>
                                    <div id="displayDiscount" class="font-bold text-red-500">Rp 0</div>
                                </div>
                                <div class="text-gray-300 dark:text-gray-500 text-lg">=</div>
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total Bayar</div>
                                    <div id="displayTotal" class="font-bold text-amber-600 text-xl">Rp 0</div>
                                </div>
                                {{-- ✅ Preview Poin --}}
                                <div id="pointPreviewWrap" class="hidden ml-auto">
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Poin Didapat</div>
                                    <div id="displayPoint" class="font-bold text-indigo-600 text-lg">+0 poin</div>
                                </div>
                            </div>
                        </div>

                        {{-- ✅ Notes --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Catatan
                            </label>
                            <textarea name="notes" rows="2" placeholder="Catatan tambahan..."
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">{{ old('notes') }}</textarea>
                        </div>

                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="submit"
                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                            Buat Booking
                        </button>
                        <a href="{{ route('admin.bookings.index') }}"
                            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let basePrice = 0;
        let servicePoints = 0;

        const serviceSelect = document.getElementById('serviceSelect');
        const programSelect = document.getElementById('programSelect');
        const promoSelect = document.getElementById('promoSelect');
        const pointInfoBox = document.getElementById('servicePointInfo');
        const pointPreviewWrap = document.getElementById('pointPreviewWrap');
        const displayPoint = document.getElementById('displayPoint');

        // ✅ Saat pilih layanan: update harga + tampilkan info poin
        serviceSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            basePrice = parseInt(selected.getAttribute('data-price')) || 0;
            servicePoints = parseInt(selected.getAttribute('data-points')) || 0;

            // Tampilkan info poin di bawah dropdown layanan
            if (this.value === '') {
                pointInfoBox.className = 'hidden mt-2 px-3 py-2 rounded-lg text-xs font-medium';
                pointInfoBox.textContent = '';
            } else if (servicePoints > 0) {
                pointInfoBox.className =
                    'mt-2 px-3 py-2 rounded-lg text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100';
                pointInfoBox.textContent =
                    `⭐ Layanan ini memberikan ${servicePoints} poin reward saat booking selesai. Pelanggan butuh 10 poin untuk bonus gratis 1 jam.`;
            } else {
                pointInfoBox.className =
                    'mt-2 px-3 py-2 rounded-lg text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400';
                pointInfoBox.textContent = '— Layanan ini tidak memberikan poin reward.';
            }

            calcTotal();
        });

        programSelect.addEventListener('change', calcTotal);
        promoSelect.addEventListener('change', calcTotal);

        function calcTotal() {
            const selectedProgram = programSelect.options[programSelect.selectedIndex];
            const selectedPromo = promoSelect.options[promoSelect.selectedIndex];
            const manualDisc = parseInt(document.getElementById('discount').value) || 0;

            let programDisc = 0;
            let programLabel = '';
            let promoDisc = 0;
            let promoLabel = '';

            // Hitung diskon program
            if (selectedProgram.value !== '') {
                const discType = selectedProgram.getAttribute('data-discount-type');
                const discValue = parseFloat(selectedProgram.getAttribute('data-discount-value')) || 0;
                const maxDisc = parseInt(selectedProgram.getAttribute('data-max-discount')) || 0;

                if (discType === 'percent') {
                    programDisc = Math.round(basePrice * discValue / 100);
                    if (maxDisc > 0 && programDisc > maxDisc) programDisc = maxDisc;
                    programLabel = discValue + '% dari harga layanan';
                } else {
                    programDisc = Math.round(discValue);
                    programLabel = 'Diskon tetap dari program';
                }
            }

            // Hitung diskon promo
            if (selectedPromo.value !== '') {
                const pct = parseFloat(selectedPromo.getAttribute('data-discount')) || 0;
                promoDisc = Math.round(basePrice * pct / 100);
                promoLabel = pct + '% dari harga layanan';
            }

            const total = Math.max(0, basePrice - programDisc - promoDisc - manualDisc);

            document.getElementById('displayPrice').textContent = 'Rp ' + basePrice.toLocaleString('id-ID');
            document.getElementById('displayProgramDisc').textContent = 'Rp ' + programDisc.toLocaleString('id-ID');
            document.getElementById('displayProgramLabel').textContent = programLabel;
            document.getElementById('displayPromoDisc').textContent = 'Rp ' + promoDisc.toLocaleString('id-ID');
            document.getElementById('displayPromoLabel').textContent = promoLabel;
            document.getElementById('displayDiscount').textContent = 'Rp ' + manualDisc.toLocaleString('id-ID');
            document.getElementById('displayTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');

            // ✅ Tampilkan preview poin di kotak ringkasan harga
            if (servicePoints > 0 && serviceSelect.value !== '') {
                pointPreviewWrap.classList.remove('hidden');
                displayPoint.textContent = '+' + servicePoints + ' poin';
            } else {
                pointPreviewWrap.classList.add('hidden');
            }
        }

        // ── Searchable customer combobox ──
        const customerWrap = document.getElementById('customerCombobox');
        const customerSearchInput = document.getElementById('customerSearchInput');
        const customerIdInput = document.getElementById('customerIdInput');
        const customerDropdown = document.getElementById('customerDropdown');
        const customerOptions = Array.from(document.querySelectorAll('.customer-option'));
        const customerNoMatch = document.getElementById('customerNoMatch');

        function filterCustomers() {
            const q = customerSearchInput.value.trim().toLowerCase();
            let visible = 0;
            customerOptions.forEach(opt => {
                const match = opt.dataset.name.toLowerCase().includes(q);
                opt.classList.toggle('hidden', !match);
                if (match) visible++;
            });
            customerNoMatch.classList.toggle('hidden', visible !== 0);
        }

        customerSearchInput.addEventListener('focus', () => {
            filterCustomers();
            customerDropdown.classList.remove('hidden');
        });

        customerSearchInput.addEventListener('input', () => {
            customerIdInput.value = ''; // reset kalau user ubah teks manual
            filterCustomers();
            customerDropdown.classList.remove('hidden');
        });

        customerOptions.forEach(opt => {
            opt.addEventListener('click', () => {
                customerIdInput.value = opt.dataset.id;
                customerSearchInput.value = opt.dataset.name;
                customerDropdown.classList.add('hidden');
            });
        });

        document.addEventListener('click', (e) => {
            if (!customerWrap.contains(e.target)) {
                customerDropdown.classList.add('hidden');
            }
        });

        document.querySelector('form').addEventListener('submit', function (e) {
            if (!customerIdInput.value) {
                e.preventDefault();
                customerSearchInput.classList.add('ring-2', 'ring-red-500');
                customerSearchInput.focus();
            }
        });

        // Inisialisasi saat halaman load
        document.addEventListener('DOMContentLoaded', calcTotal);
    </script>
</x-app-layout>
