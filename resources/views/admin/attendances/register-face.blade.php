<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Registrasi Wajah - ') }} {{ $therapist->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <!-- Alert Messages -->
            @if ($errors->any())
                <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <p class="text-red-700 dark:text-red-300 font-semibold mb-2">{{ __('Terjadi Kesalahan:') }}</p>
                    <ul class="list-disc list-inside text-red-600 dark:text-red-400 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <p class="text-green-700 dark:text-green-300">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <p class="text-red-700 dark:text-red-300">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Existing Face Data Info -->
            @if ($faceData)
                <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-1">{{ __('Data Wajah Sudah Ada') }}</p>
                            <p class="text-sm text-blue-800 dark:text-blue-300">
                                {{ __('Status:') }}
                                <span class="font-semibold">{{ $faceData->getStatusLabel() }}</span>
                                - {{ __('Terdaftar:') }} {{ $faceData->getRegisteredAtFormatted() }}
                            </p>
                            @if ($faceData->isRejected())
                                <p class="text-sm text-red-600 dark:text-red-400 mt-2">
                                    {{ __('Alasan penolakan:') }} <br>
                                    <em>{{ $faceData->getRejectionReason() }}</em>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Form Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Form Title -->
                    <h3 class="text-lg font-semibold mb-6">{{ __('Upload Foto Wajah') }}</h3>

                    <form action="{{ route('admin.therapist-face.store', $therapist->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Image Input Section -->
                        <div class="mb-6">
                            <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Pilih Foto Wajah') }}
                                <span class="text-red-600">*</span>
                            </label>

                            <!-- Image Preview -->
                            <div class="mb-4">
                                <div id="imagePreview" class="hidden w-full max-w-sm bg-gray-100 dark:bg-gray-700 rounded-lg p-4">
                                    <img id="previewImage" src="" alt="Preview" class="w-full h-auto rounded">
                                </div>
                            </div>

                            <!-- File Input -->
                            <input
                                type="file"
                                id="image"
                                name="image"
                                accept="image/*"
                                class="block w-full text-sm text-gray-500 dark:text-gray-400
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-blue-50 file:text-blue-700
                                    dark:file:bg-blue-900/20 dark:file:text-blue-300
                                    hover:file:bg-blue-100 dark:hover:file:bg-blue-900/30
                                    transition"
                                required>

                            @error('image')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror

                            <!-- Info Text -->
                            <p class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                                {{ __('Format: JPG, PNG (Max: 5MB)') }}
                            </p>
                        </div>

                        <!-- Embeddings Input (Hidden) -->
                        <input type="hidden" id="embeddings" name="embeddings" value="[]" required>

                        <!-- Info Box -->
                        <div class="mb-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                            <div class="flex">
                                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-yellow-900 dark:text-yellow-200">{{ __('Petunjuk:') }}</p>
                                    <ul class="text-sm text-yellow-800 dark:text-yellow-300 mt-1 space-y-1">
                                        <li>{{ __('• Pastikan wajah terlihat jelas dan tidak ada bayangan') }}</li>
                                        <li>{{ __('• Cahaya harus cukup untuk melihat wajah dengan baik') }}</li>
                                        <li>{{ __('• Posisikan wajah menghadap ke depan') }}</li>
                                        <li>{{ __('• Hindari ekspresi wajah yang berlebihan') }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex gap-4">
                            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                                {{ __('Upload & Simpan') }}
                            </button>
                            <a href="{{ route('admin.attendances.index') }}" class="px-6 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100 font-semibold rounded-lg transition">
                                {{ __('Batal') }}
                            </a>
                        </div>
                    </form>

                </div>
            </div>

            <!-- Info Section -->
            <div class="mt-6 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">{{ __('Mengapa Registrasi Wajah?') }}</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    {{ __('Registrasi wajah memungkinkan sistem untuk mengenali Anda saat check-in dan check-out menggunakan teknologi face recognition. Data wajah akan dienkripsi dan disimpan dengan aman.') }}
                </p>
                <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">{{ __('Proses Verifikasi') }}</h4>
                <ol class="text-sm text-gray-600 dark:text-gray-400 space-y-2 list-decimal list-inside">
                    <li>{{ __('Upload foto wajah Anda') }}</li>
                    <li>{{ __('Admin akan memverifikasi data wajah') }}</li>
                    <li>{{ __('Setelah diverifikasi, Anda dapat melakukan check-in/check-out') }}</li>
                </ol>
            </div>

        </div>
    </div>

    <!-- JavaScript untuk image preview -->
    <script>
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('previewImage').src = event.target.result;
                    document.getElementById('imagePreview').classList.remove('hidden');
                };
                reader.readAsDataURL(file);

                // TODO: Generate embeddings dari image menggunakan face recognition API
                // Untuk sekarang, gunakan array kosong
                document.getElementById('embeddings').value = JSON.stringify([]);
            }
        });
    </script>
</x-app-layout>
