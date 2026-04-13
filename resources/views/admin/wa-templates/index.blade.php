<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                📱 Template Pesan WhatsApp
            </h2>
            <a href="{{ route('admin.customers.index') }}"
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                ← Kembali ke Pelanggan
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
            @if (session('error'))
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    ✗ {{ session('error') }}
                </div>
            @endif

            @foreach ($templates as $categoryKey => $group)
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">
                        {{ $categories[$categoryKey] ?? ucfirst($categoryKey) }}
                    </h3>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider w-1/4">
                                        Label</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Template</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">
                                        Status</th>
                                    <th
                                        class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($group as $tpl)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-5 py-3.5">
                                            <div class="font-medium text-gray-800 dark:text-gray-200">
                                                {{ $tpl->label }}</div>
                                            <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $tpl->key }}
                                            </div>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <pre class="text-xs text-gray-600 dark:text-gray-400 whitespace-pre-wrap font-sans line-clamp-3">{{ $tpl->template }}</pre>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            @if ($tpl->is_active)
                                                <span
                                                    class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-medium rounded-full">Aktif</span>
                                            @else
                                                <span
                                                    class="px-2 py-0.5 bg-gray-100 text-gray-500 text-xs font-medium rounded-full">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <a href="{{ route('admin.wa-templates.edit', $tpl) }}"
                                                class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-xs font-medium rounded-lg">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

            @if ($templates->isEmpty())
                <div class="text-center py-16 text-gray-400 text-sm">
                    Belum ada template WhatsApp.
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
