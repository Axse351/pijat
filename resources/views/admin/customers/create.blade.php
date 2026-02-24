<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Tambah Pelanggan</h2>
            <a href="{{ route('admin.customers.index') }}"
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Alert error ringkasan --}}
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <div class="flex items-start gap-3">
                        <span class="text-red-500 text-lg leading-none mt-0.5">✕</span>
                        <div>
                            <p class="text-sm font-semibold text-red-700 mb-1">Terdapat {{ $errors->count() }} kesalahan
                                pada form:</p>
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li class="text-sm text-red-600">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-6">

                <form method="POST" action="{{ route('admin.customers.store') }}">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                        {{-- Nama --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Nama Lengkap *
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border rounded-lg text-sm text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500
                                    {{ $errors->has('name') ? 'border-red-400 bg-red-50 dark:bg-red-900/20' : 'border-gray-200 dark:border-gray-600' }}"
                                placeholder="Nama pelanggan">
                            @error('name')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Telepon --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Nomor Telepon
                            </label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border rounded-lg text-sm text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500
                                    {{ $errors->has('phone') ? 'border-red-400 bg-red-50 dark:bg-red-900/20' : 'border-gray-200 dark:border-gray-600' }}"
                                placeholder="08xx-xxxx-xxxx">
                            @error('phone')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tanggal Lahir --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Tanggal Lahir
                            </label>
                            <input type="date" name="ulang_tahun" value="{{ old('ulang_tahun') }}"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border rounded-lg text-sm text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500
                                    {{ $errors->has('ulang_tahun') ? 'border-red-400 bg-red-50 dark:bg-red-900/20' : 'border-gray-200 dark:border-gray-600' }}">
                            @error('ulang_tahun')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Email *
                            </label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border rounded-lg text-sm text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500
                                    {{ $errors->has('email') ? 'border-red-400 bg-red-50 dark:bg-red-900/20' : 'border-gray-200 dark:border-gray-600' }}"
                                placeholder="email@contoh.com">
                            @error('email')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Password *
                            </label>
                            <input type="password" name="password" required
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border rounded-lg text-sm text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500
                                    {{ $errors->has('password') ? 'border-red-400 bg-red-50 dark:bg-red-900/20' : 'border-gray-200 dark:border-gray-600' }}"
                                placeholder="Min. 8 karakter">
                            @error('password')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Konfirmasi Password *
                            </label>
                            <input type="password" name="password_confirmation" required
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="Ulangi password">
                        </div>

                        {{-- Catatan --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Catatan
                            </label>
                            <textarea name="notes" rows="3"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="Alergi, preferensi, dll">{{ old('notes') }}</textarea>
                        </div>

                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="submit"
                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                            Simpan
                        </button>
                        <a href="{{ route('admin.customers.index') }}"
                            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg">
                            Batal
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
