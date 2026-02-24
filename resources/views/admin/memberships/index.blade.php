<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Membership
            </h2>
            <a href="{{ route('admin.memberships.create') }}"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                + Tambah Membership
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4">

            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">
                    ✓ {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-xl border overflow-hidden">
                <div class="px-5 py-4 border-b">
                    <h3 class="font-semibold">
                        Daftar Membership ({{ $memberships->count() }})
                    </h3>
                </div>

                @if ($memberships->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase">#</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase">Nama</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase">Durasi</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($memberships as $i => $membership)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-5 py-3">{{ $i + 1 }}</td>
                                        <td class="px-5 py-3 font-medium">{{ $membership->name }}</td>
                                        <td class="px-5 py-3">
                                            <span class="px-2 py-1 bg-indigo-50 text-indigo-600 text-xs rounded-lg">
                                                {{ $membership->duration_days }} Hari
                                            </span>
                                        </td>
                                        <td class="px-5 py-3">
                                            <div class="flex gap-2">
                                                <a href="{{ route('admin.memberships.edit', $membership) }}"
                                                    class="px-3 py-1 bg-amber-50 text-amber-600 text-xs rounded-lg">
                                                    Edit
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('admin.memberships.destroy', $membership) }}">
                                                    @csrf @method('DELETE')
                                                    <button class="px-3 py-1 bg-red-50 text-red-600 text-xs rounded-lg">
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
                    <div class="text-center py-10 text-gray-400 text-sm">
                        Belum ada membership.
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
