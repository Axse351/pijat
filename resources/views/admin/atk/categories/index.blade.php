<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Kategori COA</h2>
            <a href="{{ route('admin.atk-categories.create') }}"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                + Tambah Kategori
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">
                    ✓ {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Summary -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $categories->total() }}</div>
                    <div class="text-xs text-gray-500 mt-1">Total Kategori</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                    <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $categories->sum('atks_count') }}</div>
                    <div class="text-xs text-gray-500 mt-1">Total Item ATK</div>
                </div>
            </div>

            <!-- Search -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 mb-6">
                <form method="GET" class="flex gap-3">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama atau kode kategori..."
                        class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                        Cari
                    </button>
                    <a href="{{ route('admin.atk-categories.index') }}"
                        class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                        Reset
                    </a>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">Daftar Kategori COA</h3>
                </div>

                @if ($categories->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah Item</th>
                                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($categories as $category)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-5 py-3.5 font-mono font-semibold text-indigo-600 dark:text-indigo-400">
                                            {{ $category->code }}
                                        </td>
                                        <td class="px-5 py-3.5 font-medium text-gray-800 dark:text-gray-200">
                                            {{ $category->name }}
                                        </td>
                                        <td class="px-5 py-3.5 text-gray-500 dark:text-gray-400 text-xs">
                                            {{ $category->description ?? '—' }}
                                        </td>
                                        <td class="px-5 py-3.5 text-center">
                                            <span class="px-2.5 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full text-xs font-semibold">
                                                {{ $category->atks_count }} item
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5 text-center">
                                            <div class="flex gap-3 justify-center">
                                                <a href="{{ route('admin.atk-categories.show', $category) }}"
                                                    class="text-indigo-600 hover:text-indigo-800 dark:hover:text-indigo-400 text-xs font-semibold">
                                                    Lihat
                                                </a>
                                                <a href="{{ route('admin.atk-categories.edit', $category) }}"
                                                    class="text-blue-600 hover:text-blue-800 dark:hover:text-blue-400 text-xs font-semibold">
                                                    Edit
                                                </a>
                                                @if ($category->atks_count === 0)
                                                    <form action="{{ route('admin.atk-categories.destroy', $category) }}" method="POST"
                                                        onsubmit="return confirm('Hapus kategori {{ $category->name }}?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-red-500 hover:text-red-700 dark:hover:text-red-400 text-xs font-semibold">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
                        {{ $categories->links() }}
                    </div>
                @else
                    <div class="text-center py-16 text-gray-400 text-sm">Belum ada kategori COA.</div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
