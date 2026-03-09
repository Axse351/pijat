<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Program Diskon</h2>
            <a href="{{ route('admin.programs.create') }}"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                + Tambah Program
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">
                    ✓ {{ session('success') }}
                </div>
            @endif

            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">
                        Daftar Program ({{ $programs->count() }})
                    </h3>
                </div>

                @if ($programs->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        #</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Nama Program</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Diskon</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Min. Transaksi</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Periode</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($programs as $i => $program)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-5 py-3.5 text-gray-400">{{ $i + 1 }}</td>

                                        <td class="px-5 py-3.5">
                                            <div class="font-medium text-gray-800 dark:text-gray-200">
                                                {{ $program->nama_program }}</div>
                                            @if ($program->description)
                                                <div class="text-xs text-gray-400 mt-0.5 max-w-xs truncate">
                                                    {{ $program->description }}</div>
                                            @endif
                                        </td>

                                        <td class="px-5 py-3.5">
                                            <span
                                                class="px-2.5 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-xs font-bold rounded-lg">
                                                {{ $program->discount_label }}
                                            </span>
                                        </td>

                                        <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400 text-xs">
                                            {{ $program->min_transaction ? 'Rp ' . number_format($program->min_transaction, 0, ',', '.') : '-' }}
                                        </td>

                                        <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400 text-xs">
                                            @if ($program->start_date || $program->end_date)
                                                {{ $program->start_date?->format('d M Y') ?? '∞' }}
                                                –
                                                {{ $program->end_date?->format('d M Y') ?? '∞' }}
                                            @else
                                                <span class="text-gray-400">Tidak ada batas</span>
                                            @endif
                                        </td>

                                        <td class="px-5 py-3.5">
                                            @php
                                                $isExpired = $program->end_date && $program->end_date->isPast();
                                                $isUpcoming = $program->start_date && $program->start_date->isFuture();
                                                if ($isExpired) {
                                                    $sc =
                                                        'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400';
                                                    $sl = 'Kadaluarsa';
                                                } elseif (!$program->is_active) {
                                                    $sc =
                                                        'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
                                                    $sl = 'Nonaktif';
                                                } elseif ($isUpcoming) {
                                                    $sc =
                                                        'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
                                                    $sl = 'Belum Mulai';
                                                } else {
                                                    $sc =
                                                        'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400';
                                                    $sl = 'Aktif';
                                                }
                                            @endphp
                                            <span
                                                class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold {{ $sc }}">
                                                {{ $sl }}
                                            </span>
                                        </td>

                                        <td class="px-5 py-3.5">
                                            <div class="flex gap-2">
                                                <a href="{{ route('admin.programs.edit', $program) }}"
                                                    class="px-3 py-1 bg-amber-50 hover:bg-amber-100 dark:bg-amber-900/20 dark:hover:bg-amber-900/40 text-amber-600 dark:text-amber-400 text-xs font-medium rounded-lg transition-colors">
                                                    Edit
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('admin.programs.destroy', $program) }}"
                                                    onsubmit="return confirm('Hapus program ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="px-3 py-1 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 text-red-600 dark:text-red-400 text-xs font-medium rounded-lg transition-colors">
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

                    @if ($programs->hasPages())
                        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
                            {{ $programs->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-16 text-gray-400 text-sm">
                        Belum ada program.
                        <a href="{{ route('admin.programs.create') }}" class="text-indigo-500 hover:underline">
                            Tambah sekarang
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
