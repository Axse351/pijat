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
                <form method="POST" action="{{ route('admin.bookings.store') }}">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                        {{-- Pelanggan --}}
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Pelanggan
                                *</label>
                            <select name="customer_id" required
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                <option value="">-- Pilih --</option>
                                @foreach ($customers as $c)
                                    <option value="{{ $c->id }}"
                                        {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Terapis --}}
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Terapis
                                *</label>
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
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Layanan
                                *</label>
                            <select name="service_id" id="serviceSelect" required
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                <option value="">-- Pilih --</option>
                                @foreach ($services as $s)
                                    <option value="{{ $s->id }}" data-price="{{ $s->price }}"
                                        {{ old('service_id') == $s->id ? 'selected' : '' }}>
                                        {{ $s->name }} — Rp {{ number_format($s->price, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Sumber Order --}}
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Sumber
                                Order</label>
                            <select name="order_source"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                <option value="walkin">Walk-in</option>
                                <option value="wa">WhatsApp</option>
                                <option value="web">Online/App</option>
                            </select>
                        </div>

                        {{-- Tanggal & Waktu --}}
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tanggal
                                & Waktu *</label>
                            <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" required
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                        </div>

                        {{-- Program --}}
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Program</label>
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
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Promo</label>
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
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Diskon
                                Tambahan (Rp)</label>
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
                            </div>
                        </div>

                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="submit"
                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Buat
                            Booking</button>
                        <a href="{{ route('admin.bookings.index') }}"
                            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let basePrice = 0;

        document.getElementById('serviceSelect').addEventListener('change', function() {
            basePrice = parseInt(this.options[this.selectedIndex].getAttribute('data-price')) || 0;
            calcTotal();
        });

        document.getElementById('programSelect').addEventListener('change', calcTotal);
        document.getElementById('promoSelect').addEventListener('change', calcTotal);

        function calcTotal() {
            const programSelect = document.getElementById('programSelect');
            const promoSelect = document.getElementById('promoSelect');
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
                    if (maxDisc > 0 && programDisc > maxDisc) {
                        programDisc = maxDisc;
                    }
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
        }

        // Inisialisasi harga saat halaman load
        document.addEventListener('DOMContentLoaded', calcTotal);
    </script>
</x-app-layout>
