<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Membership — {{ $customerMembership->customer->user->name }}
            </h2>
            <a href="{{ route('admin.customers.membership.history', $customerMembership->customer_id) }}"
               class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700
               text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg">
               ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl border
                        border-gray-100 dark:border-gray-700 p-6">

                <form method="POST"
                      action="{{ route('admin.customer-membership.update', $customerMembership->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                        <!-- Membership -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold
                                text-gray-500 uppercase tracking-wider mb-2">
                                Membership
                            </label>
                            <select name="membership_id"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700
                                border border-gray-200 dark:border-gray-600
                                rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">

                                @foreach($memberships as $membership)
                                    <option value="{{ $membership->id }}"
                                        {{ $customerMembership->membership_id == $membership->id ? 'selected' : '' }}>
                                        {{ $membership->name }}
                                        ({{ $membership->duration_days }} Hari)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Start Date -->
                        <div>
                            <label class="block text-xs font-semibold mb-2">
                                Tanggal Mulai
                            </label>
                            <input type="date" name="start_date"
                                value="{{ old('start_date', $customerMembership->start_date) }}"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700
                                border border-gray-200 dark:border-gray-600
                                rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <!-- End Date -->
                        <div>
                            <label class="block text-xs font-semibold mb-2">
                                Tanggal Berakhir
                            </label>
                            <input type="date" name="end_date"
                                value="{{ old('end_date', $customerMembership->end_date) }}"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700
                                border border-gray-200 dark:border-gray-600
                                rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <!-- Status -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold mb-2">
                                Status
                            </label>
                            <select name="is_active"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700
                                border border-gray-200 dark:border-gray-600
                                rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                                <option value="1" {{ $customerMembership->is_active ? 'selected' : '' }}>
                                    Aktif
                                </option>
                                <option value="0" {{ !$customerMembership->is_active ? 'selected' : '' }}>
                                    Nonaktif
                                </option>
                            </select>
                        </div>

                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="submit"
                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700
                            text-white text-sm font-medium rounded-lg">
                            Update
                        </button>

                        <a href="{{ route('admin.customers.membership.history', $customerMembership->customer_id) }}"
                           class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200
                           dark:bg-gray-700 text-gray-700 dark:text-gray-300
                           text-sm font-medium rounded-lg">
                            Batal
                        </a>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
