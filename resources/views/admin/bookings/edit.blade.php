<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit Booking</h2>
            <a href="{{ route('admin.bookings.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg">← Kembali</a>
        </div>
    </x-slot>

    <div class="py-6"><div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-6">

            {{-- Info booking (read only) --}}
            <div class="grid grid-cols-2 gap-4 mb-5 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm">
                <div><span class="text-gray-500">Pelanggan:</span> <span class="font-medium text-gray-800 dark:text-gray-200">{{ $booking->customer->name ?? '—' }}</span></div>
                <div><span class="text-gray-500">Terapis:</span> <span class="font-medium text-gray-800 dark:text-gray-200">{{ $booking->therapist->name ?? '—' }}</span></div>
                <div><span class="text-gray-500">Layanan:</span> <span class="font-medium text-gray-800 dark:text-gray-200">{{ $booking->service->name ?? '—' }}</span></div>
                <div><span class="text-gray-500">Total:</span> <span class="font-bold text-amber-600">Rp {{ number_format($booking->final_price, 0, ',', '.') }}</span></div>
            </div>

            {{-- ✅ FORM UPDATE --}}
            <form method="POST" action="{{ route('admin.bookings.update', $booking) }}">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tanggal & Waktu</label>
                        <input type="datetime-local" name="scheduled_at"
                            value="{{ \Carbon\Carbon::parse($booking->scheduled_at)->format('Y-m-d\TH:i') }}"
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Status *</label>
                        <select name="status" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                            <option value="pending"    {{ $booking->status=='pending'?'selected':'' }}>Pending</option>
                            <option value="scheduled"  {{ $booking->status=='scheduled'?'selected':'' }}>Terjadwal</option>
                            <option value="ongoing"    {{ $booking->status=='ongoing'?'selected':'' }}>Berlangsung</option>
                            <option value="completed"  {{ $booking->status=='completed'?'selected':'' }}>Selesai (generate komisi)</option>
                            <option value="cancelled"  {{ $booking->status=='cancelled'?'selected':'' }}>Dibatalkan</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-400">💡 Ubah ke "Selesai" untuk generate komisi terapis otomatis</p>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Catatan</label>
                        <textarea name="notes" rows="2" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">{{ old('notes', $booking->notes) }}</textarea>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                        Update Booking
                    </button>
                    <a href="{{ route('admin.bookings.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg">
                        Batal
                    </a>
                </div>
            </form>
            {{-- ✅ FORM HAPUS — DI LUAR form Update, tidak nested --}}
            <form method="POST" action="{{ route('admin.bookings.destroy', $booking) }}"
                  onsubmit="return confirm('Hapus booking ini?')"
                  class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                @csrf @method('DELETE')
                <button type="submit" class="px-5 py-2.5 bg-red-50 hover:bg-red-100 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-600 dark:text-red-400 text-sm font-medium rounded-lg">
                    Hapus Booking Ini
                </button>
            </form>

        </div>
    </div></div>
</x-app-layout>
