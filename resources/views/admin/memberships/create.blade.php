<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="text-xl font-semibold">Tambah Membership</h2>
            <a href="{{ route('admin.memberships.index') }}" class="px-4 py-2 bg-gray-200 text-sm rounded-lg">←
                Kembali</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4">
            <div class="bg-white rounded-xl border p-6">

                <form method="POST" action="{{ route('admin.memberships.store') }}">
                    @csrf

                    <div class="mb-5">
                        <label class="block text-xs font-semibold mb-2">Nama Membership *</label>
                        <input type="text" name="name" required class="w-full px-4 py-2 border rounded-lg">
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-semibold mb-2">Durasi (Hari) *</label>
                        <input type="number" name="duration_days" required class="w-full px-4 py-2 border rounded-lg">
                    </div>

                    <button class="px-5 py-2 bg-indigo-600 text-white rounded-lg">
                        Simpan
                    </button>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
