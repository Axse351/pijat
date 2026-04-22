<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            💰 Manajemen Komisi Terapis
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div
                    class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <p class="text-green-700 dark:text-green-300">{{ session('success') }}</p>
                </div>
            @endif

            {{-- ══ SUMMARY UNPAID ══════════════════════════════════════════════ --}}
            @if ($summaryUnpaid->count())
                <div
                    class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-bold text-amber-700 dark:text-amber-300 uppercase tracking-wide">
                            ⚠️ Total Komisi Belum Dibayar
                        </h3>
                        <span class="text-xl font-bold text-amber-700 dark:text-amber-300">
                            Rp {{ number_format($grandTotalUnpaid, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach ($summaryUnpaid as $s)
                            <div
                                class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-amber-100 dark:border-amber-800">
                                <div class="font-semibold text-gray-800 dark:text-gray-200 text-sm truncate">
                                    {{ $s->therapist->name ?? '—' }}
                                </div>
                                <div class="text-lg font-bold text-amber-600 dark:text-amber-400 mt-1">
                                    Rp {{ number_format($s->total, 0, ',', '.') }}
                                </div>
                                {{-- Breakdown: dari sesi vs dari cancel --}}
                                @if ($s->from_cancels > 0)
                                    <div class="text-xs text-gray-400 mt-0.5 space-y-0.5">
                                        <div>🟢 Sesi: Rp {{ number_format($s->from_sessions, 0, ',', '.') }}</div>
                                        <div>🔴 Cancel: Rp {{ number_format($s->from_cancels, 0, ',', '.') }}</div>
                                    </div>
                                @else
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $s->count }} sesi belum lunas</div>
                                @endif
                                <a href="{{ route('admin.commissions.index', ['therapist_id' => $s->therapist_id, 'status' => 'unpaid']) }}"
                                    class="text-xs text-blue-500 hover:underline mt-1 inline-block">
                                    Lihat detail →
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div
                    class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 text-center">
                    <span class="text-green-700 dark:text-green-300 font-semibold">✅ Semua komisi sudah dibayar!</span>
                </div>
            @endif

            {{-- ══ FILTER ══════════════════════════════════════════════════════ --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-5">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Terapis</label>
                        <select name="therapist_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-gray-100"
                            onchange="this.form.submit()">
                            <option value="">Semua Terapis</option>
                            @foreach ($therapists as $t)
                                <option value="{{ $t->id }}" {{ $therapistId == $t->id ? 'selected' : '' }}>
                                    {{ $t->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                        <select name="status"
                            class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-gray-100"
                            onchange="this.form.submit()">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua</option>
                            <option value="unpaid" {{ $status === 'unpaid' ? 'selected' : '' }}>Belum Dibayar</option>
                            <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Sudah Dibayar</option>
                        </select>
                    </div>
                    {{-- Filter sumber komisi --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Sumber</label>
                        <select name="source"
                            class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-gray-100"
                            onchange="this.form.submit()">
                            <option value="all" {{ $source === 'all' ? 'selected' : '' }}>Semua</option>
                            <option value="normal" {{ $source === 'normal' ? 'selected' : '' }}>✅ Dari Sesi
                            </option>
                            <option value="cancel_forfeit"{{ $source === 'cancel_forfeit' ? 'selected' : '' }}>🔴 Dari
                                Cancel</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Minggu</label>
                        <input type="date" name="week_start" value="{{ $weekStart }}"
                            class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-gray-100"
                            onchange="this.form.submit()">
                    </div>
                    @if ($therapistId || $status !== 'all' || $weekStart || $source !== 'all')
                        <a href="{{ route('admin.commissions.index') }}"
                            class="px-3 py-2 text-sm text-gray-500 hover:text-red-500 border border-gray-300 rounded-lg transition">
                            ✕ Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- ══ BULK PAY ════════════════════════════════════════════════════ --}}
            @if ($therapistId && $weekStart && $status !== 'paid')
                @php $unpaidTotal = $commissions->getCollection()->where('is_paid', false)->sum('commission_amount'); @endphp
                @if ($unpaidTotal > 0)
                    <form action="{{ route('admin.commissions.bulk-paid') }}" method="POST">
                        @csrf
                        <input type="hidden" name="therapist_id" value="{{ $therapistId }}">
                        <input type="hidden" name="week_start" value="{{ $weekStart }}">
                        <div
                            class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-700 rounded-xl p-4 flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold text-indigo-700 dark:text-indigo-300">
                                    Total komisi belum dibayar minggu ini:
                                    <span class="text-lg">Rp {{ number_format($unpaidTotal, 0, ',', '.') }}</span>
                                </p>
                                <p class="text-xs text-indigo-500">Klik untuk tandai semua lunas sekaligus</p>
                            </div>
                            <button type="submit"
                                class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-sm transition whitespace-nowrap">
                                ✅ Bayar Semua (Rp {{ number_format($unpaidTotal, 0, ',', '.') }})
                            </button>
                        </div>
                    </form>
                @endif
            @endif

            {{-- ══ TABEL KOMISI ════════════════════════════════════════════════ --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold text-gray-500 uppercase">
                            <th class="px-5 py-3 text-left">Terapis</th>
                            <th class="px-5 py-3 text-left">Pelanggan / Layanan</th>
                            <th class="px-5 py-3 text-center">Tipe</th>
                            <th class="px-5 py-3 text-right">Harga Booking</th>
                            <th class="px-5 py-3 text-center">Rate</th>
                            <th class="px-5 py-3 text-right">Komisi</th>
                            <th class="px-5 py-3 text-right">Koichi Dapat</th>
                            <th class="px-5 py-3 text-left">Minggu</th>
                            <th class="px-5 py-3 text-center">Status</th>
                            <th class="px-5 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($commissions as $c)
                            @php
                                $isCancelForfeit = $c->commission_source === 'cancel_forfeit';
                                $bookingFinalPrice = $c->booking->final_price ?? 0;
                                // Koichi dapat: hanya berlaku untuk komisi normal (bukan forfeit)
                                $koichiGets = $isCancelForfeit ? 0 : $bookingFinalPrice - $c->commission_amount;
                                $rowBg = $isCancelForfeit ? 'bg-red-50/50 dark:bg-red-900/10' : '';
                            @endphp
                            <tr
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ $rowBg }}
                                       {{ !$c->is_paid ? 'border-l-4 border-l-amber-400' : '' }}">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-orange-500
                                                    flex items-center justify-center text-white font-bold text-xs">
                                            {{ strtoupper(substr($c->therapist->name ?? '?', 0, 1)) }}
                                        </div>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200 text-sm">
                                            {{ $c->therapist->name ?? '—' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="font-medium text-gray-700 dark:text-gray-300 text-sm">
                                        {{ $c->booking->customer->name ?? '—' }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $c->booking->service->name ?? '—' }}
                                    </div>
                                    @if ($c->notes)
                                        <div class="text-xs text-gray-400 italic mt-0.5">{{ $c->notes }}</div>
                                    @endif
                                </td>

                                {{-- Tipe komisi --}}
                                <td class="px-5 py-3 text-center">
                                    @if ($isCancelForfeit)
                                        <span
                                            class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-700
                                                     dark:text-red-300 rounded-full text-xs font-bold whitespace-nowrap">
                                            🔴 Cancel Forfeit
                                        </span>
                                    @elseif ($c->booking->commission_type === 'program')
                                        <span
                                            class="px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700
                                                     dark:text-purple-300 rounded-full text-xs font-bold">
                                            📦 Program
                                        </span>
                                    @else
                                        <span
                                            class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700
                                                     dark:text-blue-300 rounded-full text-xs font-bold">
                                            ✅ Sesi
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">
                                    Rp {{ number_format($bookingFinalPrice, 0, ',', '.') }}
                                </td>

                                <td class="px-5 py-3 text-center">
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-bold
                                        {{ $isCancelForfeit
                                            ? 'bg-red-100 dark:bg-red-900/30 text-red-600'
                                            : ($c->commission_percent >= 30
                                                ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700'
                                                : 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300') }}">
                                        {{ $isCancelForfeit ? '100%' : $c->commission_percent . '%' }}
                                    </span>
                                </td>

                                <td class="px-5 py-3 text-right font-bold text-emerald-600 dark:text-emerald-400">
                                    Rp {{ number_format($c->commission_amount, 0, ',', '.') }}
                                </td>

                                {{-- Koichi dapat (hanya untuk sesi normal) --}}
                                <td class="px-5 py-3 text-right">
                                    @if ($isCancelForfeit)
                                        <span class="text-xs text-red-400 italic">Hangus</span>
                                    @else
                                        <span class="font-semibold text-blue-600 dark:text-blue-400">
                                            Rp {{ number_format($koichiGets, 0, ',', '.') }}
                                        </span>
                                        <div class="text-xs text-gray-400">
                                            ({{ 100 - $c->commission_percent }}%)
                                        </div>
                                    @endif
                                </td>

                                <td class="px-5 py-3 text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($c->week_start)->format('d M') }}
                                    –
                                    {{ \Carbon\Carbon::parse($c->week_end)->format('d M Y') }}
                                </td>

                                <td class="px-5 py-3 text-center">
                                    @if ($c->is_paid)
                                        <span
                                            class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700
                                                     dark:text-green-300 rounded-full text-xs font-semibold">
                                            ✓ Lunas
                                        </span>
                                        @if ($c->paid_at)
                                            <div class="text-xs text-gray-400 mt-0.5">
                                                {{ \Carbon\Carbon::parse($c->paid_at)->format('d/m/Y') }}
                                            </div>
                                        @endif
                                    @else
                                        <span
                                            class="px-2 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700
                                                     dark:text-amber-300 rounded-full text-xs font-semibold">
                                            Belum
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-3 text-center">
                                    @if (!$c->is_paid)
                                        <form action="{{ route('admin.commissions.mark-paid', $c) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded-lg text-xs font-semibold transition"
                                                onclick="return confirm('Tandai komisi ini sudah dibayar?')">
                                                Bayar
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-300 text-xs">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-12 text-gray-400">
                                    Belum ada data komisi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if ($commissions->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
                        {{ $commissions->links() }}
                    </div>
                @endif
            </div>

            {{-- ══ LEGENDA ════════════════════════════════════════════════════ --}}
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Keterangan Tipe Komisi</p>
                <div class="flex flex-wrap gap-4 text-xs text-gray-600 dark:text-gray-400">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full font-bold">✅ Sesi</span>
                        Komisi 25% dari sesi yang selesai (standard)
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded-full font-bold">📦
                            Program</span>
                        Komisi 30% dari paket program
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full font-bold">🔴 Cancel
                            Forfeit</span>
                        Customer cancel setelah bayar + terapis spesifik → uang hangus, 100% ke terapis
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
