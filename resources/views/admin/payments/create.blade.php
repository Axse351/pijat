<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Catat Pembayaran</h2>
            <a href="{{ route('admin.payments.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg">← Kembali</a>
        </div>
    </x-slot>
    <div class="py-6"><div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-6">
            @if($bookings->count() == 0)
                <div class="text-center py-10 text-gray-400">
                    <p class="text-sm">Semua booking sudah dibayar ✓</p>
                    <a href="{{ route('admin.payments.index') }}" class="mt-3 inline-block text-sm text-indigo-500 hover:underline">Lihat Riwayat</a>
                </div>
            @else
            <form method="POST" action="{{ route('admin.payments.store') }}">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Pilih Booking *</label>
                        <select name="booking_id" id="bookingSelect" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                            <option value="">-- Pilih Booking --</option>
                            @foreach($bookings as $booking)
                            <option value="{{ $booking->id }}" data-amount="{{ $booking->final_price }}" {{ request('booking_id') == $booking->id ? 'selected' : '' }}>
                                {{ $booking->customer->name ?? '?' }} — {{ $booking->service->name ?? '?' }} — Rp {{ number_format($booking->final_price, 0, ',', '.') }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div id="bookingInfo" style="display:none" class="px-4 py-3 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700 rounded-lg">
                        <div class="text-xs text-gray-500 mb-1">Total yang harus dibayar:</div>
                        <div id="displayAmount" class="text-xl font-bold text-amber-600 dark:text-amber-400">Rp 0</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Metode Pembayaran *</label>
                        <select name="method" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                            <option value="cash">Cash</option>
                            <option value="transfer">Transfer Bank</option>
                            <option value="qris">QRIS</option>
                            <option value="debit">Kartu Debit</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Jumlah (Rp) *</label>
                        <input type="number" name="amount" id="amountInput" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200" placeholder="0">
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Simpan</button>
                    <a href="{{ route('admin.payments.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg">Batal</a>
                </div>
            </form>
            <script>
            document.getElementById('bookingSelect').addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];
                const amount = parseInt(opt.getAttribute('data-amount')) || 0;
                if (amount > 0) {
                    document.getElementById('displayAmount').textContent = 'Rp ' + amount.toLocaleString('id-ID');
                    document.getElementById('amountInput').value = amount;
                    document.getElementById('bookingInfo').style.display = 'block';
                } else {
                    document.getElementById('bookingInfo').style.display = 'none';
                }
            });
            window.addEventListener('load', () => document.getElementById('bookingSelect').dispatchEvent(new Event('change')));
            </script>
            @endif
        </div>
    </div></div>
</x-app-layout>
