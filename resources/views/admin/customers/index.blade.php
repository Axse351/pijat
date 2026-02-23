<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Pelanggan</h2>
            <a href="{{ route('admin.customers.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">+ Tambah Pelanggan</a>
        </div>
    </x-slot>
    <div class="py-6"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))<div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">✓ {{ session('success') }}</div>@endif
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Daftar Pelanggan ({{ $customers->count() }})</h3>
            </div>
            @if($customers->count())
            <div class="overflow-x-auto"><table class="w-full text-sm">
                <thead><tr class="bg-gray-50 dark:bg-gray-700/50">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Telepon</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Bergabung</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($customers as $i => $customer)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-5 py-3.5 text-gray-400">{{ $i+1 }}</td>
                        <td class="px-5 py-3.5 font-medium text-gray-800 dark:text-gray-200">{{ $customer->name }}</td>
                        <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">{{ $customer->phone ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">{{ $customer->created_at->format('d M Y') }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.customers.edit', $customer) }}" class="px-3 py-1 bg-amber-50 hover:bg-amber-100 text-amber-600 text-xs font-medium rounded-lg">Edit</a>
                                <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')
                                    <button type="submit" class="px-3 py-1 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-medium rounded-lg">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table></div>
            @else
            <div class="text-center py-16 text-gray-400 text-sm">Belum ada pelanggan. <a href="{{ route('admin.customers.create') }}" class="text-indigo-500 hover:underline">Tambah sekarang</a></div>
            @endif
        </div>
    </div></div>
</x-app-layout>
