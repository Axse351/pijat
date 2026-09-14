<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Tambah User Baru
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">

                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="name" value="Nama" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                            value="{{ old('name') }}" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                            value="{{ old('email') }}" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="role" value="Role" />
                        <select id="role" name="role" required
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Pilih Role --</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="kasir" {{ old('role') === 'kasir' ? 'selected' : '' }}>Kasir</option>
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" value="Password" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full"
                            required />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" value="Konfirmasi Password" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                            class="mt-1 block w-full" required />
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            Batal
                        </a>
                        <x-primary-button>Simpan</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
