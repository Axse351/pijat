<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Pembayaran</h2>
            <a href="{{ route('admin.payments.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">+ Catat Pembayaran</a>
        </div>
    </x-slot>
    <div class="py-6"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))<div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">✓ {{ session('success') }}</div>@endif
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($payments->sum('amount') / 1000000, 1) }}jt</div>
                <div class="text-xs text-gray-500 mt-1">Total Diterima</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $payments->count() }}</div>
                <div class="text-xs text-gray-500 mt-1">Total Transaksi</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($payments->where('paid_at', '>=', now()->startOfMonth())->sum('amount') / 1000000, 1) }}jt</div>
                <div class="text-xs text-gray-500 mt-1">Bulan Ini</div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Riwayat Pembayaran</h3>
            </div>
            @if($payments->count())
            <div class="overflow-x-auto"><table class="w-full text-sm">
                <thead><tr class="bg-gray-50 dark:bg-gray-700/50">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pelanggan</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Layanan</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Metode</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($payments as $i => $payment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-5 py-3.5 text-gray-400">{{ $i+1 }}</td>
                        <td class="px-5 py-3.5 font-medium text-gray-800 dark:text-gray-200">{{ $payment->booking->customer->name ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">{{ $payment->booking->service->name ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            @php $mc = match($payment->method) { 'cash'=>'bg-emerald-100 text-emerald-700', 'transfer'=>'bg-blue-100 text-blue-700', 'qris'=>'bg-amber-100 text-amber-700', default=>'bg-gray-100 text-gray-500' }; @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $mc }}">{{ strtoupper($payment->method) }}</span>
                        </td>
                        <td class="px-5 py-3.5 font-semibold text-amber-600 dark:text-amber-400">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">{{ \Carbon\Carbon::parse($payment->paid_at)->format('d M Y, H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table></div>
            @else
            <div class="text-center py-16 text-gray-400 text-sm">Belum ada pembayaran.</div>
            @endif
        </div>
    </div></div>
</x-app-layout>
