<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Membership</p>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Assign Membership — {{ $customer->user->name }}
                </h2>
            </div>
            <a href="{{ route('admin.customers.membership.index', $customer) }}"
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-lg mx-auto px-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-6">

                @if ($errors->any())
                    <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.customers.membership.store', $customer) }}">
                    @csrf

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Tipe Membership
                        </label>
                        <select name="membership_id"
                            class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Pilih tipe membership --</option>
                            @foreach ($memberships as $membership)
                                <option value="{{ $membership->id }}"
                                    {{ old('membership_id') == $membership->id ? 'selected' : '' }}>
                                    {{ $membership->name }} — {{ $membership->duration_days }} hari
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1.5">
                            Tanggal mulai hari ini. Tanggal berakhir dihitung otomatis dari durasi.
                        </p>
                        @error('membership_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                            class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                            Simpan
                        </button>
                        <a href="{{ route('admin.customers.membership.index', $customer) }}"
                            class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                            Batal
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
