<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit Pembayaran</h2>
            <a href="{{ route('admin.payments.index') }}"
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            @if ($errors->any())
                <div
                    class="mb-4 px-4 py-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-400 rounded-lg text-sm">
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-6">

                {{-- Info booking (read-only, untuk konteks) --}}
                <div class="mb-6 px-4 py-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-sm space-y-1">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Pelanggan</span>
                        <span class="font-medium text-gray-700 dark:text-gray-200">
                            {{ $payment->booking->customer->name ?? '—' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Terapis</span>
                        <span class="font-medium text-gray-700 dark:text-gray-200">
                            {{ $payment->booking->therapist->name ?? '—' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Layanan</span>
                        <span class="font-medium text-gray-700 dark:text-gray-200">
                            {{ $payment->booking->service->name ?? '—' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Tanggal Bayar</span>
                        <span class="font-medium text-gray-700 dark:text-gray-200">
                            {{ \Carbon\Carbon::parse($payment->paid_at)->format('d M Y, H:i') }}
                        </span>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.payments.update', $payment) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                Metode Pembayaran *
                            </label>
                            <select name="method" required
                                class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                @foreach (['cash' => 'Cash', 'qris' => 'QRIS', 'transfer' => 'Transfer', 'debit' => 'Debit'] as $val => $label)
                                    <option value="{{ $val }}"
                                        {{ old('method', $payment->method) === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                Jumlah (Rp) *
                            </label>
                            <input type="number" name="amount" min="0" required
                                value="{{ old('amount', $payment->amount) }}"
                                class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                            <p class="text-xs text-gray-400 mt-1">
                                ⚠️ Mengubah jumlah ini akan otomatis menghitung ulang komisi terapis (berdasarkan
                                harga asli layanan, bukan jumlah ini).
                            </p>
                        </div>

                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="submit"
                            class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.payments.index') }}"
                            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg text-center">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
