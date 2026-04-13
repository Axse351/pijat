<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit Terapis —
                {{ $therapist->name }}</h2>
            <a href="{{ route('admin.therapists.index') }}"
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">←
                Kembali</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div
                    class="mb-4 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg text-sm text-red-600 dark:text-red-400">
                    <p class="font-semibold mb-1">Terdapat kesalahan:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-6">
                <form method="POST" action="{{ route('admin.therapists.update', $therapist) }}"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="space-y-5">
                        {{-- Foto --}}
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                                Foto Profil
                            </label>
                            <div class="relative">
                                <input type="file" name="photo" id="photoInput"
                                    accept="image/jpeg,image/png,image/jpg,image/gif" class="hidden"
                                    onchange="previewPhoto(event)">
                                <label for="photoInput"
                                    class="block w-full px-4 py-6 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:border-indigo-400 dark:hover:border-indigo-500 transition-colors text-center">
                                    <div id="photoPreview" class="{{ $therapist->photo ? '' : 'hidden' }}">
                                        @if ($therapist->photo)
                                            <img id="previewImg" src="{{ asset('storage/' . $therapist->photo) }}"
                                                alt="Preview" class="w-20 h-20 rounded-lg object-cover mx-auto mb-2">
                                        @else
                                            <img id="previewImg" src="" alt="Preview"
                                                class="w-20 h-20 rounded-lg object-cover mx-auto mb-2">
                                        @endif
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Klik untuk mengganti foto
                                        </p>
                                    </div>
                                    <div id="photoPlaceholder" class="{{ $therapist->photo ? 'hidden' : '' }}">
                                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Upload foto (JPEG, PNG, GIF
                                            | Max 2MB)</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Nama Lengkap --}}
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                                Nama Lengkap *
                            </label>
                            <input type="text" name="name" value="{{ old('name', $therapist->name) }}" required
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                        </div>

                        {{-- Email --}}
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                                Email *
                            </label>
                            <input type="email" name="email"
                                value="{{ old('email', $therapist->user?->email) }}" required
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200"
                                placeholder="email@example.com">
                            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Email digunakan untuk login
                                terapis.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            {{-- Spesialisasi --}}
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                                    Spesialisasi
                                </label>
                                <input type="text" name="specialty"
                                    value="{{ old('specialty', $therapist->specialty) }}"
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                            </div>

                            {{-- Telepon --}}
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                                    Telepon
                                </label>
                                <input type="text" name="phone" value="{{ old('phone', $therapist->phone) }}"
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            {{-- Komisi --}}
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                                    Komisi (%) *
                                </label>
                                <input type="number" name="commission_percent"
                                    value="{{ old('commission_percent', $therapist->commission_percent) }}" required
                                    min="0" max="100"
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                            </div>

                            {{-- Status --}}
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                                    Status *
                                </label>
                                <select name="is_active" required
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-gray-200">
                                    <option value="1" {{ $therapist->is_active ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ !$therapist->is_active ? 'selected' : '' }}>Nonaktif
                                    </option>
                                </select>
                            </div>
                        </div>

                        {{-- Reset Password --}}
                        <div class="pt-1 border-t border-gray-100 dark:border-gray-700">
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                                Reset Password
                            </p>
                            <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="flex-1">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Reset password terapis ke default <strong class="text-gray-800 dark:text-gray-200">123456</strong>.
                                    </p>
                                </div>
                                <form method="POST" action="{{ route('admin.therapists.resetPassword', $therapist) }}"
                                    onsubmit="return confirm('Reset password {{ $therapist->name }} ke 123456?')">
                                    @csrf
                                    <button type="submit"
                                        class="flex-shrink-0 px-4 py-2 bg-amber-50 hover:bg-amber-100 dark:bg-amber-900/30 dark:hover:bg-amber-900/50 text-amber-600 dark:text-amber-400 text-xs font-semibold rounded-lg transition-colors border border-amber-200 dark:border-amber-700">
                                        Reset Password
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="submit"
                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors">
                            Update
                        </button>
                        <a href="{{ route('admin.therapists.index') }}"
                            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function previewPhoto(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('photoPlaceholder').classList.add('hidden');
                    document.getElementById('photoPreview').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</x-app-layout>
