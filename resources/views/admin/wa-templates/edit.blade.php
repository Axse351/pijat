<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                ✏️ Edit Template: {{ $waTemplate->label }}
            </h2>
            <a href="{{ route('admin.wa-templates.index') }}"
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

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

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-6">

                {{-- Info key (readonly) --}}
                <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                    <span class="text-xs text-gray-400">Key (tidak bisa diubah):</span>
                    <span class="ml-2 font-mono text-sm text-gray-700">{{ $waTemplate->key }}</span>
                </div>

                <form action="{{ route('admin.wa-templates.update', $waTemplate) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Label --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Label <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="label" value="{{ old('label', $waTemplate->label) }}"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                            required>
                        @error('label')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kategori --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select name="category"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                            required>
                            @foreach ($categories as $key => $label)
                                <option value="{{ $key }}"
                                    {{ old('category', $waTemplate->category) === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Template --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Isi Template <span class="text-red-500">*</span>
                        </label>
                        <p class="text-xs text-gray-400 mb-2">
                            Gunakan <code class="bg-gray-100 px-1 rounded">@{{ nama_variabel }}</code> untuk variabel
                            dinamis,
                            misalnya <code class="bg-gray-100 px-1 rounded">@{{ nama_pelanggan }}</code>,
                            <code class="bg-gray-100 px-1 rounded">@{{ layanan }}</code>,
                            <code class="bg-gray-100 px-1 rounded">@{{ jadwal }}</code>.
                        </p>
                        <textarea name="template" rows="8"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                            required>{{ old('template', $waTemplate->template) }}</textarea>
                        @error('template')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status aktif --}}
                    <div class="mb-6 flex items-center gap-3"> 
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                            {{ old('is_active', $waTemplate->is_active) ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-300">
                        <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Template aktif (digunakan untuk pengiriman WA)
                        </label>
                    </div>

                    {{-- Preview button --}}
                    <div class="mb-6">
                        <button type="button" id="btnPreview"
                            class="px-4 py-2 bg-green-50 hover:bg-green-100 text-green-600 border border-green-200 text-sm font-medium rounded-lg transition-colors">
                            👁 Preview dengan data dummy
                        </button>
                        <div id="previewBox" class="hidden mt-3 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-xs font-semibold text-green-600 mb-2">Preview Pesan:</p>
                            <pre id="previewText" class="text-sm text-gray-700 whitespace-pre-wrap font-sans"></pre>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex items-center justify-between gap-3">
                        <form action="{{ route('admin.wa-templates.reset', $waTemplate) }}" method="POST"
                            onsubmit="return confirm('Reset template ini ke default? Perubahan yang sudah disimpan akan hilang.')">
                            @csrf
                            @method('PUT')
                            <button type="submit"
                                class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 text-sm font-medium rounded-lg transition-colors">
                                🔄 Reset ke Default
                            </button>
                        </form>

                        <div class="flex gap-3">
                            <a href="{{ route('admin.wa-templates.index') }}"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                                Batal
                            </a>
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                                💾 Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        document.getElementById('btnPreview').addEventListener('click', function() {
            fetch('{{ route('admin.wa-templates.preview', $waTemplate) }}')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('previewText').textContent = data.preview || '(template kosong)';
                    document.getElementById('previewBox').classList.remove('hidden');
                })
                .catch(() => alert('Gagal memuat preview.'));
        });
    </script>
</x-app-layout>
