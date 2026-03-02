<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Terapis</h2>
            <a href="{{ route('admin.therapists.create') }}"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">+
                Tambah Terapis</a>
        </div>
    </x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div
                    class="mb-4 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-emerald-400 rounded-lg text-sm flex items-center gap-2">
                    <span>✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">Daftar Terapis
                        ({{ $therapists->count() }})</h3>
                </div>

                @if ($therapists->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <th
                                        class="text-left px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        #</th>
                                    <th
                                        class="text-left px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Foto</th>
                                    <th
                                        class="text-left px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Nama</th>
                                    <th
                                        class="text-left px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Spesialisasi</th>
                                    <th
                                        class="text-left px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Telepon</th>
                                    <th
                                        class="text-left px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Komisi</th>
                                    <th
                                        class="text-left px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="text-left px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($therapists as $i => $therapist)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-6 py-4 text-gray-400">{{ $i + 1 }}</td>
                                        <td class="px-6 py-4">
                                            @if ($therapist->photo)
                                                <img src="{{ asset('storage/' . $therapist->photo) }}"
                                                    alt="{{ $therapist->name }}"
                                                    class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-gray-700">
                                            @else
                                                <div
                                                    class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white font-bold text-xs">
                                                    {{ strtoupper(substr($therapist->name, 0, 1)) }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="font-medium text-gray-800 dark:text-gray-200">{{ $therapist->name }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                            {{ $therapist->specialty ?? '—' }}</td>
                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                            {{ $therapist->phone ?? '—' }}</td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="inline-flex px-2.5 py-1 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 text-xs font-bold rounded-lg">{{ $therapist->commission_percent }}%</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $therapist->is_active ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                                                {{ $therapist->is_active ? '✓ Aktif' : '✕ Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex gap-2">
                                                <a href="{{ route('admin.therapists.edit', $therapist) }}"
                                                    class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 dark:bg-amber-900/30 dark:hover:bg-amber-900/50 text-amber-600 dark:text-amber-400 text-xs font-medium rounded-lg transition-colors">
                                                    Edit
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('admin.therapists.destroy', $therapist) }}"
                                                    onsubmit="return confirm('Yakin ingin menghapus terapis ini?')"
                                                    class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="px-3 py-1.5 bg-red-50 hover:bg-red-100 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-600 dark:text-red-400 text-xs font-medium rounded-lg transition-colors">
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
                @else
                    <div class="text-center py-16 text-gray-400 text-sm">
                        <p class="mb-2">Belum ada terapis terdaftar.</p>
                        <a href="{{ route('admin.therapists.create') }}"
                            class="text-indigo-500 hover:text-indigo-600 font-medium">Tambah terapis sekarang →</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
