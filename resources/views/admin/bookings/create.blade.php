<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Buat Booking Baru</h2>
            <a href="{{ route('admin.bookings.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg">← Kembali</a>
        </div>
    </x-slot>
    <div class="py-6"><div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-6">
            <form method="POST" action="{{ route('admin.bookings.store') }}">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Pelanggan *</label>
                        <select name="customer_id" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                            <option value="">-- Pilih --</option>
                            @foreach($customers as $c)<option value="{{ $c->id }}" {{ old('customer_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Terapis *</label>
                        <select name="therapist_id" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                            <option value="">-- Pilih --</option>
                            @foreach($therapists as $t)<option value="{{ $t->id }}" {{ old('therapist_id')==$t->id?'selected':'' }}>{{ $t->name }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Layanan *</label>
                        <select name="service_id" id="serviceSelect" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                            <option value="">-- Pilih --</option>
                            @foreach($services as $s)<option value="{{ $s->id }}" data-price="{{ $s->price }}" {{ old('service_id')==$s->id?'selected':'' }}>{{ $s->name }} — Rp {{ number_format($s->price,0,',','.') }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Sumber Order</label>
                        <select name="order_source" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                          <option value="walkin">Walk-in</option>
<option value="wa">WhatsApp</option>
<option value="web">Online/App</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tanggal & Waktu *</label>
                        <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Diskon (Rp)</label>
                        <input type="number" name="discount" id="discount" value="{{ old('discount', 0) }}" min="0" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200" oninput="calcTotal()">
                    </div>
                    <div class="sm:col-span-2 px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg flex gap-8 items-center">
                        <div><div class="text-xs text-gray-500 mb-1">Harga</div><div id="displayPrice" class="font-bold text-gray-800 dark:text-gray-200">Rp 0</div></div>
                        <div class="text-gray-400">−</div>
                        <div><div class="text-xs text-gray-500 mb-1">Diskon</div><div id="displayDiscount" class="font-bold text-red-500">Rp 0</div></div>
                        <div class="text-gray-400">=</div>
                        <div><div class="text-xs text-gray-500 mb-1">Total</div><div id="displayTotal" class="font-bold text-amber-600 text-lg">Rp 0</div></div>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Buat Booking</button>
                    <a href="{{ route('admin.bookings.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg">Batal</a>
                </div>
            </form>
        </div>
    </div></div>
    <script>
    let price = 0;
    document.getElementById('serviceSelect').addEventListener('change', function() {
        price = parseInt(this.options[this.selectedIndex].getAttribute('data-price')) || 0;
        calcTotal();
    });
    function calcTotal() {
        const disc = parseInt(document.getElementById('discount').value) || 0;
        const total = Math.max(0, price - disc);
        document.getElementById('displayPrice').textContent = 'Rp ' + price.toLocaleString('id-ID');
        document.getElementById('displayDiscount').textContent = 'Rp ' + disc.toLocaleString('id-ID');
        document.getElementById('displayTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }
    </script>
</x-app-layout>
