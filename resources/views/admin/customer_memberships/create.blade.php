<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Beri Membership — {{ $customer->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-6">

                @if ($errors->any())
                    <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                        @foreach ($errors->all() as $error)
                            <div>✗ {{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @if ($memberships->isEmpty())
                    <p class="text-sm text-gray-500">
                        Belum ada master membership.
                        <a href="{{ route('admin.memberships.create') }}" class="text-indigo-500 hover:underline">Buat
                            dulu</a>.
                    </p>
                @else
                    <form method="POST" action="{{ route('admin.customers.membership.store', $customer) }}">
                        @csrf

                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih
                            Membership</label>
                        <select name="membership_id" required
                            class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 rounded-lg text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">— Pilih —</option>
                            @foreach ($memberships as $m)
                                <option value="{{ $m->id }}" @selected(old('membership_id') == $m->id)>
                                    {{ $m->name }} ({{ $m->duration_days }} hari)
                                </option>
                            @endforeach
                        </select>

                        <p class="mt-2 text-xs text-gray-400">
                            Mulai hari ini. Membership aktif sebelumnya otomatis dinonaktifkan.
                        </p>

                        <div class="mt-6 flex gap-2">
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Simpan</button>
                            <a href="{{ route('admin.customers.membership.index', $customer) }}"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg">Batal</a>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
